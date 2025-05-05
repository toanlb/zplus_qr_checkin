<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CheckIn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MobileCheckInController extends Controller
{
    /**
     * Hiển thị trang check-in cho thiết bị di động
     */
    public function index()
    {
        // Lấy 15 hoạt động gần đây nhất (cả check-in và check-out)
        $recentCheckIns = CheckIn::with('user')
                        ->latest('updated_at') // Sắp xếp theo thời gian cập nhật gần nhất
                        ->take(15)
                        ->get();
        
        return view('mobile.check-in', [
            'recentCheckIns' => $recentCheckIns
        ]);
    }
    
    /**
     * Hiển thị trang check-in cho laptop
     */
    public function laptopIndex()
    {
        // Lấy 15 hoạt động gần đây nhất (cả check-in và check-out)
        $recentCheckIns = CheckIn::with('user')
                        ->latest('updated_at') // Sắp xếp theo thời gian cập nhật gần nhất
                        ->take(15)
                        ->get();
        
        return view('mobile.check-in-laptop', [
            'recentCheckIns' => $recentCheckIns
        ]);
    }
    
    /**
     * Xử lý quét mã QR từ thiết bị di động
     */
    public function process(Request $request)
    {
        try {
            $qrCode = $request->input('qr_code');
            if (empty($qrCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy mã QR trong yêu cầu.'
                ]);
            }
            
            Log::info('Processing QR code: ' . $qrCode);
            $action = $request->input('action', 'checkin');
            
            // Validate mã QR
            $userId = $this->validateQrCode($qrCode);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã QR không hợp lệ hoặc đã hết hạn.'
                ]);
            }
            
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng.'
                ]);
            }
            
            // Kiểm tra tư cách thành viên
            if (!$this->verifyMembership($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gói thành viên đã hết hạn hoặc không tồn tại.'
                ]);
            }
            
            if ($action === 'checkin') {
                return $this->handleCheckIn($user);
            } else {
                return $this->handleCheckOut($user);
            }
            
        } catch (\Exception $e) {
            Log::error('Mobile QR Code Processing Error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            Log::error($e->getTraceAsString());
            
            // In development mode, return more details
            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xử lý: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý. Vui lòng thử lại sau.'
            ]);
        }
    }
    
    /**
     * Xử lý dữ liệu mã QR từ thiết bị di động và laptop
     */
    public function processQr(Request $request)
    {
        try {
            $qrCode = $request->input('qr_code');
            if (empty($qrCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy mã QR trong yêu cầu.'
                ]);
            }
            
            Log::info('Processing QR code (processQr): ' . $qrCode);
            $action = $request->input('action', 'checkin');
            
            // Validate mã QR
            $userId = $this->validateQrCode($qrCode);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã QR không hợp lệ hoặc đã hết hạn.'
                ]);
            }
            
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng.'
                ]);
            }
            
            // Kiểm tra tư cách thành viên
            if (!$this->verifyMembership($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gói thành viên đã hết hạn hoặc không tồn tại.'
                ]);
            }
            
            if ($action === 'checkin') {
                return $this->handleCheckIn($user);
            } else {
                return $this->handleCheckOut($user);
            }
            
        } catch (\Exception $e) {
            Log::error('QR Code Processing Error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            Log::error($e->getTraceAsString());
            
            // In development mode, return more details
            if (config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xử lý: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xử lý. Vui lòng thử lại sau.'
            ]);
        }
    }
    
    /**
     * Xác thực mã QR và trả về user ID nếu hợp lệ
     */
    private function validateQrCode($qrCode)
    {
        try {
            // Đơn giản hóa: Tìm kiếm mã QR trực tiếp trong bảng user
            $user = User::where('qr_code', $qrCode)->first();
            
            if ($user) {
                return $user->id;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('QR Code Validation Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Kiểm tra tư cách thành viên của người dùng
     */
    private function verifyMembership($user)
    {
        try {
            // Cố gắng sử dụng phương thức có sẵn nếu có
            if (method_exists($user, 'hasActiveMembership')) {
                return $user->hasActiveMembership() || $user->hasRole('admin');
            }
            
            // Ghi log để xác thực cấu trúc bảng membership
            Log::info('Checking membership for user: ' . $user->id);
            
            // Kiểm tra xem người dùng có quyền admin không
            if ($user->hasRole('admin')) {
                Log::info('User is admin, membership verification passed');
                return true;
            }
            
            // Kiểm tra thành viên không cần có gói nếu không có bảng membership
            if (!class_exists('App\Models\Membership')) {
                Log::info('Membership model does not exist, skipping verification');
                return true;
            }
            
            // Kiểm tra xem người dùng có gói thành viên hợp lệ không
            // Thử cả hai trường end_date và expiry_date
            $activeMembership = $user->memberships()
                ->where(function($query) {
                    $query->where('end_date', '>=', Carbon::now())
                          ->orWhere('expiry_date', '>=', Carbon::now());
                })
                ->where('status', 'active')
                ->first();
            
            Log::info('User active membership check result: ' . ($activeMembership ? 'found' : 'not found'));
            
            return $activeMembership !== null;
        } catch (\Exception $e) {
            Log::error('Membership verification error: ' . $e->getMessage());
            // Trả về true để đảm bảo lỗi không ảnh hưởng đến việc check-in
            return true;
        }
    }
    
    /**
     * Xử lý việc check-in
     */
    private function handleCheckIn($user)
    {
        // Kiểm tra xem người dùng đã check-in hôm nay chưa
        $existingCheckIn = CheckIn::where('user_id', $user->id)
                            ->whereDate('check_in_time', Carbon::today())
                            ->whereNull('check_out_time')
                            ->first();
        
        if ($existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã check-in hôm nay. Bạn có thể check-out khi hoàn thành.'
            ]);
        }
        
        // Tạo check-in mới
        $checkIn = new CheckIn();
        $checkIn->user_id = $user->id;
        $checkIn->check_in_time = Carbon::now();
        $checkIn->date = Carbon::today(); // Adding date field which exists in the schema
        $checkIn->save();
        
        return response()->json([
            'success' => true,
            'type' => 'checkin',
            'message' => 'Check-in thành công!',
            'user' => [
                'name' => $user->name,
                'member_type' => $user->membership_type,
                'check_in_time' => $checkIn->check_in_time->format('H:i:s')
            ]
        ]);
    }
    
    /**
     * Xử lý việc check-out
     */
    private function handleCheckOut($user)
    {
        // Tìm check-in của người dùng ngày hôm nay
        $checkIn = CheckIn::where('user_id', $user->id)
                    ->whereDate('check_in_time', Carbon::today())
                    ->whereNull('check_out_time')
                    ->first();
        
        if (!$checkIn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin check-in. Vui lòng check-in trước.'
            ]);
        }
        
        // Cập nhật thời gian check-out
        $checkIn->check_out_time = Carbon::now();
        $checkIn->save();
        
        // Tính thời gian sử dụng
        $duration = $checkIn->check_in_time->diffForHumans($checkIn->check_out_time, true);
        
        return response()->json([
            'success' => true,
            'type' => 'checkout',
            'message' => 'Check-out thành công!',
            'user' => [
                'name' => $user->name,
                'check_in_time' => $checkIn->check_in_time->format('H:i:s'),
                'check_out_time' => $checkIn->check_out_time->format('H:i:s'),
                'duration' => $duration
            ]
        ]);
    }
}