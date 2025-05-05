<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Display a listing of the memberships.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberships = Membership::with('user')
            ->latest()
            ->paginate(15);
            
        return view('memberships.index', compact('memberships'));
    }
    
    /**
     * Show the form for creating a new membership.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('role', 'member')->get();
        return view('memberships.create', compact('users'));
    }
    
    /**
     * Store a newly created membership in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,pending,expired'
        ]);
        
        $membership = Membership::create($request->all());
        
        // Create notification for the user
        Notification::create([
            'user_id' => $request->user_id,
            'message' => 'Bạn đã đăng ký thành viên thành công. Trạng thái: ' . $request->status,
            'type' => 'membership_renewed',
            'read' => false
        ]);
        
        return redirect()->route('memberships.index')
            ->with('success', 'Membership created successfully.');
    }
    
    /**
     * Display the specified membership.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $membership = Membership::with('user')->findOrFail($id);
        return view('memberships.show', compact('membership'));
    }
    
    /**
     * Show the form for editing the specified membership.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        $users = User::where('role', 'member')->get();
        return view('memberships.edit', compact('membership', 'users'));
    }
    
    /**
     * Update the specified membership in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,pending,expired'
        ]);
        
        $membership = Membership::findOrFail($id);
        $oldStatus = $membership->status;
        $membership->update($request->all());
        
        // If status changed, create a notification
        if ($oldStatus != $request->status) {
            Notification::create([
                'user_id' => $request->user_id,
                'message' => 'Trạng thái thành viên của bạn đã được cập nhật thành: ' . $request->status,
                'type' => 'membership_renewed',
                'read' => false
            ]);
        }
        
        return redirect()->route('memberships.index')
            ->with('success', 'Membership updated successfully');
    }
    
    /**
     * Remove the specified membership from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $membership = Membership::findOrFail($id);
        $membership->delete();
        
        return redirect()->route('memberships.index')
            ->with('success', 'Membership deleted successfully');
    }
    
    /**
     * Renew a membership.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function renew(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1'
        ]);
        
        $membership = Membership::findOrFail($id);
        
        // Cập nhật trạng thái thành expired và lưu vào cơ sở dữ liệu
        $membership->status = 'expired';
        $membership->save();
        
        // Create a new membership
        $startDate = Carbon::now();
        // Chuyển đổi duration_months từ chuỗi sang số nguyên trước khi sử dụng
        $endDate = $startDate->copy()->addMonths((int)$request->duration_months);
        
        $newMembership = Membership::create([
            'user_id' => $membership->user_id,
            'amount' => $request->amount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active'
        ]);
        
        // Create notification
        Notification::create([
            'user_id' => $membership->user_id,
            'message' => 'Thành viên của bạn đã được gia hạn thành công đến ngày ' . $endDate->format('d/m/Y'),
            'type' => 'membership_renewed',
            'read' => false
        ]);
        
        return redirect()->route('memberships.index')
            ->with('success', 'Membership renewed successfully');
    }
    
    /**
     * List memberships that have expired.
     *
     * @return \Illuminate\Http\Response
     */
    public function expiringSoon()
    {
        $expiringSoon = Membership::with('user')
            ->where(function($query) {
                $query->where('status', 'expired')
                      ->orWhere(function($query) {
                          $query->where('status', 'active')
                                ->where('end_date', '<', Carbon::now());
                      });
            })
            ->orderBy('end_date', 'desc')
            ->paginate(15);
            
        return view('memberships.expiring', compact('expiringSoon'));
    }
}
