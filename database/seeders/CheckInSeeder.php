<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CheckInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with active memberships
        $userIds = Membership::where('status', 'active')->pluck('user_id')->unique()->toArray();
        $users = User::whereIn('id', $userIds)->get();

        // Generate check-ins for the past 30 days
        for ($day = 0; $day < 30; $day++) {
            $date = Carbon::now()->subDays($day);
            
            // Skip weekends for some randomness
            if ($date->isWeekend() && rand(0, 1) === 0) {
                continue;
            }

            foreach ($users as $user) {
                // Some users don't check in every day
                if (rand(0, 10) < 3) {
                    continue;
                }
                
                $checkInTime = $date->copy()->addHours(rand(6, 10))->addMinutes(rand(0, 59));
                
                // Some users don't check out
                $checkOutTime = rand(0, 10) < 8 
                    ? $checkInTime->copy()->addHours(rand(1, 3))->addMinutes(rand(0, 59))
                    : null;
                
                CheckIn::create([
                    'user_id' => $user->id,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'date' => $date->format('Y-m-d'),
                ]);
            }
        }

        // Create some check-ins for today
        $today = Carbon::now();
        foreach ($users as $user) {
            // 70% chance of checking in today
            if (rand(1, 10) <= 7) {
                $checkInTime = $today->copy()->subHours(rand(1, 6))->subMinutes(rand(0, 59));
                
                // 50% chance of already checking out
                $checkOutTime = rand(1, 10) <= 5 
                    ? $checkInTime->copy()->addHours(rand(1, 2))->addMinutes(rand(0, 59))
                    : null;
                
                CheckIn::create([
                    'user_id' => $user->id,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'date' => $today->format('Y-m-d'),
                ]);
            }
        }
    }
}
