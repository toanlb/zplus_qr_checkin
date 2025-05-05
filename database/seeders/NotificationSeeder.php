<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        // Notification types
        $notificationTypes = [
            'membership_expiring' => 'Thành viên sắp hết hạn',
            'membership_expired' => 'Thành viên đã hết hạn',
            'membership_renewed' => 'Thành viên đã gia hạn',
            'announcement' => 'Thông báo chung',
            'promotion' => 'Khuyến mãi',
        ];
        
        // Create expiring membership notifications
        $expiringMemberships = Membership::where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays(10))
            ->get();
            
        foreach ($expiringMemberships as $membership) {
            $daysLeft = Carbon::now()->diffInDays($membership->end_date);
            
            Notification::create([
                'user_id' => $membership->user_id,
                'message' => "Thành viên của bạn sẽ hết hạn sau $daysLeft ngày. Vui lòng gia hạn để tiếp tục sử dụng dịch vụ.",
                'type' => 'membership_expiring',
                'read' => (bool) rand(0, 1),
            ]);
        }
        
        // Create expired membership notifications
        $expiredMemberships = Membership::where('status', 'expired')
            ->where('end_date', '>=', Carbon::now()->subDays(15))
            ->get();
            
        foreach ($expiredMemberships as $membership) {
            Notification::create([
                'user_id' => $membership->user_id,
                'message' => "Thành viên của bạn đã hết hạn. Vui lòng gia hạn để tiếp tục sử dụng dịch vụ.",
                'type' => 'membership_expired',
                'read' => (bool) rand(0, 1),
            ]);
        }
        
        // Create some general announcements
        $announcements = [
            'Chúng tôi sẽ bảo trì hệ thống vào ngày 15/06/2025. Xin lỗi vì sự bất tiện này.',
            'Lịch hoạt động dịp lễ 30/4 và 1/5: Câu lạc bộ mở cửa từ 7:00 đến 22:00 hàng ngày.',
            'Chúng tôi vừa cập nhật thêm các lớp học mới. Vui lòng kiểm tra lịch để đăng ký.',
            'Khóa học Yoga nâng cao sẽ bắt đầu vào tuần tới. Đăng ký ngay!',
        ];
        
        foreach ($announcements as $announcement) {
            foreach ($users as $user) {
                // Only send to some users randomly
                if (rand(0, 2) === 0) {
                    continue;
                }
                
                Notification::create([
                    'user_id' => $user->id,
                    'message' => $announcement,
                    'type' => 'announcement',
                    'read' => (bool) rand(0, 1),
                ]);
            }
        }
        
        // Create some promotions
        $promotions = [
            'Giảm 20% khi gia hạn thành viên trong tháng này!',
            'Đăng ký gói Premium trong tuần này để nhận 1 tháng miễn phí.',
            'Giới thiệu bạn bè, nhận ưu đãi: Giảm 10% cho bạn và người được giới thiệu.',
        ];
        
        foreach ($promotions as $promotion) {
            foreach ($users as $user) {
                // Only send to some users randomly
                if (rand(0, 3) !== 0) {
                    continue;
                }
                
                Notification::create([
                    'user_id' => $user->id,
                    'message' => $promotion,
                    'type' => 'promotion',
                    'read' => (bool) rand(0, 1),
                ]);
            }
        }
    }
}
