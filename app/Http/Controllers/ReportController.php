<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the report dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard
        $totalUsers = User::count();
        $activeMembers = User::whereHas('memberships', function($query) {
            $query->where('status', 'active');
        })->count();
        
        $todayCheckIns = CheckIn::whereDate('date', Carbon::today())->count();
        $weekCheckIns = CheckIn::whereBetween('date', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->count();
        
        $expiringSoon = Membership::where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays(7))
            ->count();
            
        // Get recent check-ins for quick view
        $recentCheckIns = CheckIn::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'totalUsers', 
            'activeMembers', 
            'todayCheckIns', 
            'weekCheckIns', 
            'expiringSoon',
            'recentCheckIns'
        ));
    }

    /**
     * Generate and display user activity report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function userActivity(Request $request)
    {
        $dateRange = $request->date_range ?? 'week';
        $startDate = null;
        $endDate = null;
        
        // Determine date range based on selection
        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
                $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
                break;
            default:
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
        }
        
        // Get user activity data for the specified date range
        $userActivity = CheckIn::with('user')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->groupBy('user_id');
            
        // Calculate statistics for each user
        $userStats = [];
        foreach ($userActivity as $userId => $checkIns) {
            $user = $checkIns->first()->user;
            
            // Calculate total visits and duration
            $totalVisits = $checkIns->count();
            $totalDuration = 0;
            
            foreach ($checkIns as $checkIn) {
                if ($checkIn->check_out_time) {
                    $checkInTime = Carbon::parse($checkIn->check_in_time);
                    $checkOutTime = Carbon::parse($checkIn->check_out_time);
                    $totalDuration += $checkInTime->diffInMinutes($checkOutTime);
                }
            }
            
            $userStats[$userId] = [
                'user' => $user,
                'total_visits' => $totalVisits,
                'total_duration_minutes' => $totalDuration,
                'avg_duration_minutes' => $totalVisits > 0 ? round($totalDuration / $totalVisits) : 0,
                'last_visit' => $checkIns->first()->date,
                'checkIns' => $checkIns
            ];
        }

        return view('reports.user-activity', compact(
            'userStats', 
            'dateRange', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Generate and display membership status report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function membershipStatus(Request $request)
    {
        $status = $request->status ?? 'all';
        
        $membershipsQuery = Membership::with('user');
        
        // Filter by status if specified
        if ($status !== 'all') {
            $membershipsQuery->where('status', $status);
        }
        
        // Add expiration filter if needed
        if ($request->expiring && $status === 'active') {
            $days = (int) $request->expiring;
            $membershipsQuery->where('end_date', '<=', Carbon::now()->addDays($days));
        }
        
        $memberships = $membershipsQuery->orderBy('end_date')->paginate(20);
        
        // Get statistics for the dashboard
        $stats = [
            'total' => Membership::count(),
            'active' => Membership::where('status', 'active')->count(),
            'expired' => Membership::where('status', 'expired')->count(),
            'expiring7days' => Membership::where('status', 'active')
                ->where('end_date', '<=', Carbon::now()->addDays(7))
                ->where('end_date', '>', Carbon::now())
                ->count(),
            'expiring30days' => Membership::where('status', 'active')
                ->where('end_date', '<=', Carbon::now()->addDays(30))
                ->where('end_date', '>', Carbon::now())
                ->count(),
        ];

        return view('reports.membership-status', compact('memberships', 'status', 'stats'));
    }

    /**
     * Generate and display daily check-in trend report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dailyTrends(Request $request)
    {
        $period = $request->period ?? 'month';
        
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'quarter':
                $startDate = Carbon::now()->subMonths(3);
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                break;
            default:
                $startDate = Carbon::now()->subMonth();
        }
        
        $endDate = Carbon::now();
        
        // Get daily check-in counts
        $dailyCounts = CheckIn::selectRaw('date, COUNT(*) as count')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Prepare data for chart
        $dates = [];
        $counts = [];
        
        foreach ($dailyCounts as $record) {
            $dates[] = Carbon::parse($record->date)->format('d/m/Y');
            $counts[] = $record->count;
        }
        
        // Get busiest day of week
        $busiestDay = CheckIn::selectRaw('DAYOFWEEK(date) as day_of_week, COUNT(*) as count')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('day_of_week')
            ->orderBy('count', 'desc')
            ->first();
            
        // Get busiest hour of day
        $busiestHour = CheckIn::selectRaw('HOUR(check_in_time) as hour, COUNT(*) as count')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();
            
        // Format day of week name
        $dayOfWeekName = null;
        if ($busiestDay) {
            $dayNames = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
            $dayOfWeekName = $dayNames[$busiestDay->day_of_week % 7];
        }

        return view('reports.daily-trends', compact(
            'period',
            'dates',
            'counts',
            'dailyCounts',
            'dayOfWeekName',
            'busiestHour'
        ));
    }

    /**
     * Generate and display a custom downloadable report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customReport(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'report_type' => 'required|in:user_activity,memberships,check_ins',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'format' => 'required|in:csv,pdf',
            ]);
            
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            
            // Generate report data based on type
            switch ($request->report_type) {
                case 'user_activity':
                    // User activity report data generation
                    $data = $this->generateUserActivityReportData($startDate, $endDate);
                    $filename = 'user_activity_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
                    break;
                    
                case 'memberships':
                    // Memberships report data generation
                    $data = $this->generateMembershipsReportData($startDate, $endDate);
                    $filename = 'memberships_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
                    break;
                    
                case 'check_ins':
                    // Check-ins report data generation
                    $data = $this->generateCheckInsReportData($startDate, $endDate);
                    $filename = 'check_ins_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
                    break;
            }
            
            // Generate CSV report
            if ($request->format === 'csv') {
                return $this->generateCsvReport($data, $filename);
            }
            
            // Redirect back with error if PDF not implemented
            // Note: For PDF generation, you would need to install a PDF generation package
            return redirect()->back()->with('error', 'PDF generation is not implemented yet.');
        }
        
        return view('reports.custom-report');
    }
    
    /**
     * Generate user activity report data.
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function generateUserActivityReportData($startDate, $endDate)
    {
        $userActivity = CheckIn::with('user')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('user_id');
            
        $data = [
            ['ID', 'Tên Thành Viên', 'Email', 'Tổng Lượt Đăng Nhập', 'Tổng Thời Gian (phút)', 'Thời Gian TB (phút)']
        ];
        
        foreach ($userActivity as $userId => $checkIns) {
            $user = $checkIns->first()->user;
            
            // Calculate total duration
            $totalDuration = 0;
            foreach ($checkIns as $checkIn) {
                if ($checkIn->check_out_time) {
                    $checkInTime = Carbon::parse($checkIn->check_in_time);
                    $checkOutTime = Carbon::parse($checkIn->check_out_time);
                    $totalDuration += $checkInTime->diffInMinutes($checkOutTime);
                }
            }
            
            $totalVisits = $checkIns->count();
            $avgDuration = $totalVisits > 0 ? round($totalDuration / $totalVisits) : 0;
            
            $data[] = [
                $user->id,
                $user->name,
                $user->email,
                $totalVisits,
                $totalDuration,
                $avgDuration
            ];
        }
        
        return $data;
    }
    
    /**
     * Generate memberships report data.
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function generateMembershipsReportData($startDate, $endDate)
    {
        $memberships = Membership::with('user')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('end_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get();
            
        $data = [
            ['ID', 'Tên Thành Viên', 'Email', 'Loại Thành Viên', 'Ngày Bắt Đầu', 'Ngày Kết Thúc', 'Trạng Thái']
        ];
        
        foreach ($memberships as $membership) {
            $data[] = [
                $membership->user->id,
                $membership->user->name,
                $membership->user->email,
                $membership->membership_type,
                $membership->start_date,
                $membership->end_date,
                $membership->status
            ];
        }
        
        return $data;
    }
    
    /**
     * Generate check-ins report data.
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return array
     */
    private function generateCheckInsReportData($startDate, $endDate)
    {
        $checkIns = CheckIn::with('user')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->orderBy('check_in_time')
            ->get();
            
        $data = [
            ['ID', 'Tên Thành Viên', 'Email', 'Ngày', 'Giờ Check-in', 'Giờ Check-out', 'Thời Gian Sử Dụng (phút)']
        ];
        
        foreach ($checkIns as $checkIn) {
            $duration = null;
            if ($checkIn->check_out_time) {
                $checkInTime = Carbon::parse($checkIn->check_in_time);
                $checkOutTime = Carbon::parse($checkIn->check_out_time);
                $duration = $checkInTime->diffInMinutes($checkOutTime);
            }
            
            $data[] = [
                $checkIn->user->id,
                $checkIn->user->name,
                $checkIn->user->email,
                $checkIn->date,
                $checkIn->check_in_time->format('H:i:s'),
                $checkIn->check_out_time ? $checkIn->check_out_time->format('H:i:s') : 'N/A',
                $duration ?? 'N/A'
            ];
        }
        
        return $data;
    }
    
    /**
     * Generate and download CSV report.
     *
     * @param  array  $data
     * @param  string  $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function generateCsvReport($data, $filename)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}