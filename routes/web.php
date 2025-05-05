<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\MobileCheckInController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Member home route - xử lý logic chuyển hướng dựa trên vai trò
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('member.home');

Route::get('/dashboard', function () {
    // Lấy dữ liệu thực từ database
    $checkInsCount = \App\Models\CheckIn::count();
    $activeMembers = \App\Models\User::where('member_type', '!=', null)->count();
    $expiringMemberships = \App\Models\Membership::where('end_date', '>', now())
        ->where('end_date', '<', now()->addDays(30))
        ->count();
    $monthlyRevenue = \App\Models\Membership::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');
    
    // Lấy check-in gần đây nhất
    $recentCheckIns = \App\Models\CheckIn::with('user')
        ->orderBy('date', 'desc')
        ->orderBy('check_in_time', 'desc')
        ->take(4)
        ->get();
    
    // Lấy thành viên sắp hết hạn
    $expiringMembershipsList = \App\Models\Membership::with('user')
        ->where('end_date', '>', now())
        ->where('end_date', '<', now()->addDays(30))
        ->orderBy('end_date')
        ->take(3)
        ->get();
    
    return view('dashboard', compact(
        'checkInsCount', 
        'activeMembers', 
        'expiringMemberships', 
        'monthlyRevenue', 
        'recentCheckIns', 
        'expiringMembershipsList'
    ));
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Routes cho tất cả người dùng đã đăng nhập (cả admin và member)
    // Profile Routes - Chỉ xem và sửa thông tin cá nhân
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // QR Code Routes - Chỉ xem QR code
    Route::get('/qrcode/image/{user}', [QrCodeController::class, 'show'])->name('qrcode.show')
        ->middleware('can:view,user'); // Chỉ xem QR code của chính mình hoặc admin
    Route::get('/qrcode/view/{user}', [QrCodeController::class, 'showQrCodePage'])->name('qrcode.view')
        ->middleware('can:view,user'); // Chỉ xem QR code của chính mình hoặc admin
    
    // Notification Routes - Chỉ xem thông báo của mình
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

    // Admin Routes - Chỉ dành cho admin
    Route::middleware('admin')->group(function () {
        // QR Code generation - Chỉ admin mới được tạo QR code mới
        Route::post('/qrcode/generate/{userId}', [QrCodeController::class, 'generate'])->name('qrcode.generate');
        Route::post('/qrcode/validate', [QrCodeController::class, 'validate'])->name('qrcode.validate');
        Route::get('/qr-codes/generate', [QrCodeController::class, 'showGeneratePage'])->name('qr-codes.generate');
        
        // Check-in Routes
        Route::post('/checkin', [CheckInController::class, 'checkIn'])->name('checkin.create');
        Route::post('/checkout', [CheckInController::class, 'checkOut'])->name('checkout.create');
        Route::post('/check-ins/process', [CheckInController::class, 'process'])->name('check-ins.process');
        Route::get('/checkin/history/{userId}', [CheckInController::class, 'history'])->name('checkin.history');
        Route::get('/check-ins', [CheckInController::class, 'index'])->name('check-ins.index');
        Route::get('/check-ins/all-history', [CheckInController::class, 'allHistory'])->name('check-ins.all-history');
        Route::get('/check-ins/create', [CheckInController::class, 'create'])->name('check-ins.create');
        Route::post('/check-ins', [CheckInController::class, 'store'])->name('check-ins.store');
        Route::get('/check-ins/{id}', [CheckInController::class, 'show'])->name('check-ins.show');
        Route::get('/check-ins/{id}/edit', [CheckInController::class, 'edit'])->name('check-ins.edit');
        Route::put('/check-ins/{id}', [CheckInController::class, 'update'])->name('check-ins.update');
        Route::delete('/check-ins/{id}', [CheckInController::class, 'destroy'])->name('check-ins.destroy');
        
        // Member Routes
        Route::resource('members', MemberController::class);
        
        // Report Routes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])->name('reports.user-activity');
        Route::get('/reports/membership-status', [ReportController::class, 'membershipStatus'])->name('reports.membership-status');
        Route::get('/reports/daily-trends', [ReportController::class, 'dailyTrends'])->name('reports.daily-trends');
        Route::match(['get', 'post'], '/reports/custom-report', [ReportController::class, 'customReport'])->name('reports.custom-report');
        
        // Settings Routes
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        // Mobile Routes - Trang check-in/check-out dành cho thiết bị di động
        Route::get('/mobile/check', [MobileCheckInController::class, 'index'])->name('mobile.check');
        Route::post('/mobile/process', [MobileCheckInController::class, 'process'])->name('mobile.process');
        Route::post('/mobile/process-qr', [MobileCheckInController::class, 'processQr'])->name('mobile.process-qr');
        
        // Laptop Routes - Trang check-in/check-out dành cho laptop
        Route::get('/laptop/check', [MobileCheckInController::class, 'laptopIndex'])->name('laptop.check');
        
        // Membership Management
        Route::resource('memberships', MembershipController::class);
        Route::post('/memberships/renew/{id}', [MembershipController::class, 'renew'])->name('memberships.renew');
        Route::get('/memberships-expiring', [MembershipController::class, 'expiringSoon'])->name('memberships.expiring');
        
        // Check-in Monitoring
        Route::get('/today-checkins', [CheckInController::class, 'todayCheckIns'])->name('checkins.today');
        
        // Notification Management
        Route::get('/admin/notifications', [NotificationController::class, 'adminIndex'])->name('admin.notifications.index');
        Route::get('/admin/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/admin/notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::get('/admin/notifications/{id}/edit', [NotificationController::class, 'edit'])->name('notifications.edit');
        Route::put('/admin/notifications/{id}', [NotificationController::class, 'update'])->name('notifications.update');
        Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Member Management
        Route::post('members/{member}/regenerate-qr', [MemberController::class, 'regenerateQR'])->name('members.regenerate-qr');

        // Role Management
        Route::resource('roles', RoleController::class);
        Route::get('/roles/{id}/users', [RoleController::class, 'users'])->name('roles.users');
        Route::get('/assign-roles', [RoleController::class, 'assignForm'])->name('roles.assign-form');
        Route::post('/roles/assign', [RoleController::class, 'assignRole'])->name('roles.assign');
    });
});

require __DIR__.'/auth.php';
