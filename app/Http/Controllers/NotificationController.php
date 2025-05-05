<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notifications = Notification::where(function($query) {
                // Thông báo riêng của người dùng
                $query->where('user_id', auth()->id());
            })
            ->orWhere(function($query) {
                // Thông báo dạng announcement và promotion cho tất cả người dùng
                $query->whereNull('user_id')
                      ->whereIn('type', ['announcement', 'promotion']);
            })
            ->latest()
            ->paginate(10);
            
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);
            
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Create a new notification for a user or all users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:announcement,promotion',
            'user_id' => 'nullable|exists:users,id',
            'send_to_all' => 'required|boolean'
        ]);
        
        if ($request->send_to_all) {
            // Send to all users
            $users = User::all();
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => $request->message,
                    'type' => $request->type,
                    'read' => false
                ]);
            }
            $message = 'Notification sent to all users.';
        } else {
            // Send to specific user
            Notification::create([
                'user_id' => $request->user_id,
                'message' => $request->message,
                'type' => $request->type,
                'read' => false
            ]);
            $message = 'Notification sent to user.';
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Admin view for managing notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminIndex()
    {
        $users = User::all();
        $notifications = Notification::with('user')
            ->latest()
            ->paginate(15);
            
        return view('admin.notifications.index', compact('notifications', 'users'));
    }
    
    /**
     * Remove the specified notification from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user or if user is admin
        if ($notification->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }
    
    /**
     * Show the form for creating a new notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('notifications.create', compact('users'));
    }
    
    /**
     * Display the specified notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user or if user is admin
        if ($notification->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('notifications.show', compact('notification'));
    }
    
    /**
     * Show the form for editing the specified notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $users = User::all();
        return view('notifications.edit', compact('notification', 'users'));
    }
    
    /**
     * Update the specified notification in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:announcement,promotion,membership_renewed',
            'user_id' => 'required|exists:users,id',
        ]);
        
        $notification = Notification::findOrFail($id);
        $notification->update($request->all());
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }
}
