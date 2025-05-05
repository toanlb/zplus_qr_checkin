<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Display a listing of the members
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'member');
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by membership status
        if ($request->has('status')) {
            $status = $request->status;
            $query->whereHas('activeMembership', function($q) use ($status) {
                $q->where('status', $status);
            });
        }
        
        $members = $query->paginate(10);
        
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created member
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
            'member_type' => 'required|in:regular,premium,vip',
        ]);

        // Generate unique QR code string
        $qrCodeString = Str::uuid()->toString();
        
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'member_type' => $request->member_type,
            'password' => Hash::make($request->password),
            'role' => 'member',
            'qr_code' => $qrCodeString,
        ]);

        // Generate QR code image
        $qrCode = QrCode::format('svg')
                        ->size(300)
                        ->errorCorrection('H')
                        ->generate($qrCodeString);
        
        // Save QR code to storage
        $qrPath = 'qrcodes/' . $user->id . '.svg';
        Storage::disk('public')->put($qrPath, $qrCode);
        
        return redirect()->route('members.show', $user)
                         ->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified member
     */
    public function show(User $member)
    {
        // Eager load relationships
        $member->load(['memberships', 'checkIns']);
        
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified member
     */
    public function edit(User $member)
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified member
     */
    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'member_type' => 'required|in:regular,premium,vip',
        ]);

        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'member_type' => $request->member_type,
        ]);

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member updated successfully');
    }

    /**
     * Remove the specified member
     */
    public function destroy(User $member)
    {
        // Delete QR code image
        Storage::disk('public')->delete('qrcodes/' . $member->id . '.svg');
        // Also try to delete the old PNG format if it exists
        Storage::disk('public')->delete('qrcodes/' . $member->id . '.png');
        
        // Delete member
        $member->delete();

        return redirect()->route('members.index')
                         ->with('success', 'Member deleted successfully');
    }

    /**
     * Regenerate a new QR code for member
     */
    public function regenerateQR(User $member)
    {
        // Generate new QR code string
        $qrCodeString = Str::uuid()->toString();
        
        // Update member QR code
        $member->update([
            'qr_code' => $qrCodeString
        ]);
        
        // Generate new QR code image
        $qrCode = QrCode::format('svg')
                        ->size(300)
                        ->errorCorrection('H')
                        ->generate($qrCodeString);
        
        // Save QR code to storage
        $qrPath = 'qrcodes/' . $member->id . '.svg';
        Storage::disk('public')->delete($qrPath); // Delete old QR code
        Storage::disk('public')->put($qrPath, $qrCode);
        
        return redirect()->route('members.show', $member)
                         ->with('success', 'QR code regenerated successfully');
    }
}