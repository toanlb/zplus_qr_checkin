<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    /**
     * Display a listing of check-ins.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $checkIns = CheckIn::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);

        return view('check-ins.index', compact('checkIns'));
    }

    /**
     * Record a check-in for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $userInput = $request->user_id;
        $now = Carbon::now();
        
        // Check if input is a numeric ID or a QR code string
        if (is_numeric($userInput)) {
            $user = User::find($userInput);
        } else {
            // Find user by QR code
            $user = User::where('qr_code', $userInput)->first();
        }
        
        if (!$user) {
            $message = 'Không tìm thấy thành viên với mã QR hoặc ID đã nhập.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }
        
        $userId = $user->id;
        
        // Check if the user already has an active check-in for today
        $existingCheckIn = CheckIn::where('user_id', $userId)
            ->whereDate('date', $now->toDateString())
            ->whereNull('check_out_time')
            ->first();
            
        if ($existingCheckIn) {
            $message = $user->name . ' đã check-in và chưa check-out.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }
        
        // Verify user has an active membership
        if (!$user->hasActiveMembership()) {
            $message = $user->name . ' không có gói thành viên đang hoạt động.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }

        // Create new check-in record
        CheckIn::create([
            'user_id' => $userId,
            'date' => $now->toDateString(),
            'check_in_time' => $now,
        ]);

        $message = 'Check-in thành công cho ' . $user->name;
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'member_type' => $user->member_type
                ]
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Record a check-out for a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $userInput = $request->user_id;
        $now = Carbon::now();
        
        // Check if input is a numeric ID or a QR code string
        if (is_numeric($userInput)) {
            $user = User::find($userInput);
        } else {
            // Find user by QR code
            $user = User::where('qr_code', $userInput)->first();
        }
        
        if (!$user) {
            $message = 'Không tìm thấy thành viên với mã QR hoặc ID đã nhập.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }
        
        $userId = $user->id;
        
        // Find the active check-in for today
        $activeCheckIn = CheckIn::where('user_id', $userId)
            ->whereDate('date', $now->toDateString())
            ->whereNull('check_out_time')
            ->first();
            
        if (!$activeCheckIn) {
            $message = 'Không tìm thấy check-in đang hoạt động cho ' . $user->name . ' hôm nay.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('error', $message);
        }
        
        // Update the check-in with check-out time
        $activeCheckIn->update([
            'check_out_time' => $now,
        ]);

        // Calculate duration
        $checkInTime = Carbon::parse($activeCheckIn->check_in_time);
        $checkOutTime = $now;
        $durationMinutes = $checkInTime->diffInMinutes($checkOutTime);
        
        $hours = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;
        
        $durationText = '';
        if ($hours > 0) {
            $durationText = $hours . 'h ' . $minutes . 'm';
        } else {
            $durationText = $minutes . ' phút';
        }

        $message = 'Check-out thành công cho ' . $user->name . '. Thời gian sử dụng: ' . $durationText;
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'duration' => $durationText
                ]
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Process a check-in or check-out request from QR code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
            'action' => 'required|in:checkin,checkout',
        ]);

        $qrCode = $request->qr_code;
        $action = $request->action;
        $now = Carbon::now();
        
        // Find user by QR code
        $user = User::where('qr_code', $qrCode)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thành viên với mã QR đã nhập.',
            ]);
        }
        
        // Verify user has an active membership
        if (!$user->hasActiveMembership()) {
            return response()->json([
                'success' => false,
                'message' => $user->name . ' không có gói thành viên đang hoạt động.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member_type' => $user->member_type
                ]
            ]);
        }
        
        if ($action === 'checkin') {
            // Check if the user already has an active check-in for today
            $existingCheckIn = CheckIn::where('user_id', $user->id)
                ->whereDate('date', $now->toDateString())
                ->whereNull('check_out_time')
                ->first();
                
            if ($existingCheckIn) {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' đã check-in và chưa check-out.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'member_type' => $user->member_type
                    ]
                ]);
            }
            
            // Create new check-in record
            $checkIn = CheckIn::create([
                'user_id' => $user->id,
                'date' => $now->toDateString(),
                'check_in_time' => $now,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công cho ' . $user->name,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member_type' => $user->member_type
                ],
                'checkIn' => [
                    'id' => $checkIn->id,
                    'date' => $checkIn->date,
                    'check_in_time' => $checkIn->check_in_time->format('H:i:s'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                ]
            ]);
        } else {
            // Check-out logic
            $existingCheckIn = CheckIn::where('user_id', $user->id)
                ->whereDate('date', $now->toDateString())
                ->whereNull('check_out_time')
                ->first();
                
            if (!$existingCheckIn) {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' chưa check-in hoặc đã check-out rồi.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'member_type' => $user->member_type
                    ]
                ]);
            }
            
            // Update check-out time
            $existingCheckIn->update([
                'check_out_time' => $now,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Check-out thành công cho ' . $user->name,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member_type' => $user->member_type
                ],
                'checkIn' => [
                    'id' => $existingCheckIn->id,
                    'date' => $existingCheckIn->date,
                    'check_in_time' => $existingCheckIn->check_in_time->format('H:i:s'),
                    'check_out_time' => $existingCheckIn->check_out_time->format('H:i:s'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                ]
            ]);
        }
    }

    /**
     * Display check-in history for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function history($userId)
    {
        $user = User::findOrFail($userId);
        $checkIns = CheckIn::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);

        return view('check-ins.history', compact('user', 'checkIns'));
    }

    /**
     * Display today's check-ins (for admin).
     *
     * @return \Illuminate\View\View
     */
    public function todayCheckIns()
    {
        $today = Carbon::today()->toDateString();
        $checkIns = CheckIn::with('user')
            ->whereDate('date', $today)
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);
            
        $activeCheckIns = $checkIns->whereNull('check_out_time')->count();
        $completedCheckIns = $checkIns->whereNotNull('check_out_time')->count();

        return view('check-ins.today', compact('checkIns', 'activeCheckIns', 'completedCheckIns'));
    }

    /**
     * Display all check-ins history in the system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function allHistory(Request $request)
    {
        $query = CheckIn::with('user')->orderBy('date', 'desc')->orderBy('check_in_time', 'desc');
        
        // Filter by user if specified
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }
        
        // Filter by check-in status
        if ($request->has('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('check_out_time');
            } elseif ($request->status === 'active') {
                $query->whereNull('check_out_time');
            }
        }
        
        $checkIns = $query->paginate(20);
        $users = User::orderBy('name')->get();
        
        return view('check-ins.all-history', compact('checkIns', 'users'));
    }

    /**
     * Show the form for creating a new check-in.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::whereHas('memberships', function($query) {
            $query->where('status', 'active');
        })->get();
        
        return view('check-ins.create', compact('users'));
    }

    /**
     * Store a newly created check-in in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_time' => 'nullable|after:check_in_time',
        ]);

        // Verify user has an active membership
        $user = User::find($request->user_id);
        if (!$user->hasActiveMembership()) {
            return redirect()->back()->with('error', 'User does not have an active membership.');
        }

        // Create new check-in record
        CheckIn::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
        ]);

        return redirect()->route('check-ins.index')
            ->with('success', 'Check-in created successfully.');
    }

    /**
     * Display the specified check-in.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $checkIn = CheckIn::with('user')->findOrFail($id);
        return view('check-ins.show', compact('checkIn'));
    }

    /**
     * Show the form for editing the specified check-in.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $checkIn = CheckIn::findOrFail($id);
        $users = User::all();
        return view('check-ins.edit', compact('checkIn', 'users'));
    }

    /**
     * Update the specified check-in in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in_time' => 'required',
            'check_out_time' => 'nullable|after:check_in_time',
        ]);

        $checkIn = CheckIn::findOrFail($id);
        $checkIn->update($request->all());

        return redirect()->route('check-ins.index')
            ->with('success', 'Check-in updated successfully.');
    }

    /**
     * Remove the specified check-in from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $checkIn = CheckIn::findOrFail($id);
        $checkIn->delete();

        return redirect()->route('check-ins.index')
            ->with('success', 'Check-in deleted successfully.');
    }
}
