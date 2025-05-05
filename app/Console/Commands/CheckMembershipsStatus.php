<?php

namespace App\Console\Commands;

use App\Mail\MembershipExpiredNotification;
use App\Mail\MembershipExpiringNotification;
use App\Models\Membership;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckMembershipsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-memberships-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra trạng thái membership và gửi thông báo cho các membership sắp hết hạn hoặc đã hết hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->checkExpiringMemberships();
        $this->checkExpiredMemberships();
        $this->updateExpiredStatus();
        
        $this->info('Đã kiểm tra trạng thái membership và gửi thông báo thành công!');
    }
    
    /**
     * Kiểm tra và gửi thông báo cho các membership sắp hết hạn
     */
    private function checkExpiringMemberships()
    {
        // Lấy danh sách membership sắp hết hạn trong vòng 7 ngày
        $expiringMemberships = Membership::with('user')
            ->where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays(7))
            ->where('end_date', '>', Carbon::now())
            ->get();
            
        $this->info("Tìm thấy {$expiringMemberships->count()} membership sắp hết hạn.");
        
        foreach ($expiringMemberships as $membership) {
            $daysLeft = Carbon::now()->diffInDays($membership->end_date);
            
            // Tạo thông báo trong hệ thống
            Notification::create([
                'user_id' => $membership->user_id,
                'message' => "Thành viên của bạn sẽ hết hạn sau $daysLeft ngày. Vui lòng gia hạn để tiếp tục sử dụng dịch vụ.",
                'type' => 'membership_expiring',
                'read' => false,
            ]);
            
            // Gửi email thông báo
            try {
                if ($membership->user && $membership->user->email) {
                    Mail::to($membership->user->email)
                        ->send(new MembershipExpiringNotification($membership->user, $membership));
                    
                    $this->line("  - Đã gửi email thông báo đến: {$membership->user->email}");
                }
            } catch (\Exception $e) {
                Log::error("Lỗi khi gửi email: " . $e->getMessage());
                $this->error("  - Lỗi khi gửi email đến {$membership->user->email}: {$e->getMessage()}");
            }
        }
    }
    
    /**
     * Kiểm tra và gửi thông báo cho các membership đã hết hạn
     */
    private function checkExpiredMemberships()
    {
        // Lấy danh sách membership vừa hết hạn 1 ngày trước đó
        $expiredMemberships = Membership::with('user')
            ->where('status', 'active')
            ->where('end_date', '<', Carbon::now())
            ->where('end_date', '>=', Carbon::now()->subDay())
            ->get();
            
        $this->info("Tìm thấy {$expiredMemberships->count()} membership vừa hết hạn.");
        
        foreach ($expiredMemberships as $membership) {
            // Tạo thông báo trong hệ thống
            Notification::create([
                'user_id' => $membership->user_id,
                'message' => "Thành viên của bạn đã hết hạn. Vui lòng gia hạn để tiếp tục sử dụng dịch vụ.",
                'type' => 'membership_expired',
                'read' => false,
            ]);
            
            // Gửi email thông báo
            try {
                if ($membership->user && $membership->user->email) {
                    Mail::to($membership->user->email)
                        ->send(new MembershipExpiredNotification($membership->user, $membership));
                    
                    $this->line("  - Đã gửi email thông báo đến: {$membership->user->email}");
                }
            } catch (\Exception $e) {
                Log::error("Lỗi khi gửi email: " . $e->getMessage());
                $this->error("  - Lỗi khi gửi email đến {$membership->user->email}: {$e->getMessage()}");
            }
        }
    }
    
    /**
     * Cập nhật trạng thái thành expired cho các membership đã hết hạn
     */
    private function updateExpiredStatus()
    {
        $count = Membership::where('status', 'active')
            ->where('end_date', '<', Carbon::now())
            ->update(['status' => 'expired']);
            
        $this->info("Đã cập nhật trạng thái cho $count membership từ active sang expired.");
    }
}
