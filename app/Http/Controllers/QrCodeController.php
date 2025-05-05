<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    /**
     * Generate a QR code for a user.
     * Chỉ admin mới có quyền tạo QR code mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Kiểm tra nếu người dùng hiện tại không phải admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền tạo QR code mới'
            ], 403);
        }
        
        // Generate a unique QR code
        $qrCode = $user->generateQrCode();
        
        // Generate QR code image as SVG (doesn't require Imagick)
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrCode);
        
        // Save QR code to storage
        $filename = 'qrcodes/' . $user->id . '.svg';
        Storage::disk('public')->put($filename, $qrImage);
        
        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'image_url' => Storage::disk('public')->url($filename)
        ]);
    }
    
    /**
     * Display a user's QR code image.
     * 
     * This method returns ONLY the image, not a full page.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (!$user->qr_code) {
            // Nếu người dùng không có QR code, kiểm tra quyền trước khi tạo
            if (auth()->check() && auth()->user()->isAdmin()) {
                $user->generateQrCode();
            } else {
                // Trả về QR code trống nếu không có quyền tạo mới
                $qrImage = QrCode::format('svg')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate('Không có QR code');
                return response($qrImage)->header('Content-Type', 'image/svg+xml');
            }
        }
        
        // Generate QR code image as SVG (doesn't require Imagick)
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($user->qr_code);
        
        return response($qrImage)->header('Content-Type', 'image/svg+xml');
    }
    
    /**
     * Display the QR code page with user information.
     * 
     * This method returns a complete view with the QR code and user details.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showQrCodePage(User $user)
    {
        $user->load(['checkIns' => function($query) {
            $query->latest('date')->take(5);
        }]);
        
        return view('qrcodes.show', ['user' => $user]);
    }

    /**
     * Validate a QR code during check-in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validate(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);
        
        $qrCode = $request->input('qr_code');
        $user = User::where('qr_code', $qrCode)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ], 404);
        }
        
        // Check if user has an active membership
        if (!$user->hasActiveMembership()) {
            return response()->json([
                'success' => false,
                'message' => 'No active membership',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member_type' => $user->member_type
                ]
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'member_type' => $user->member_type,
                'membership_end_date' => $user->activeMembership()->end_date
            ]
        ]);
    }

    /**
     * Show the QR code generation page.
     * Chỉ admin mới có thể xem trang này
     *
     * @return \Illuminate\Http\Response
     */
    public function showGeneratePage()
    {
        // Kiểm tra quyền admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $users = User::all();
        return view('qrcodes.generate', ['users' => $users]);
    }
}
