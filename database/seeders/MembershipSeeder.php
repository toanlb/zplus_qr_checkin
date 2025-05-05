<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all members except admin
        $users = User::where('role', 'member')->get();

        foreach ($users as $user) {
            // Create an active membership for half of the users
            if ($user->id % 2 === 0) {
                Membership::create([
                    'user_id' => $user->id,
                    'amount' => rand(300000, 1000000), // 300,000 VND to 1,000,000 VND
                    'start_date' => Carbon::now()->subMonths(rand(1, 3)),
                    'end_date' => Carbon::now()->addMonths(rand(1, 6)),
                    'status' => 'active'
                ]);
            } 
            // Create an expired membership for some users
            elseif ($user->id % 3 === 0) {
                Membership::create([
                    'user_id' => $user->id,
                    'amount' => rand(300000, 1000000),
                    'start_date' => Carbon::now()->subMonths(rand(3, 6)),
                    'end_date' => Carbon::now()->subDays(rand(1, 15)),
                    'status' => 'expired'
                ]);
            }
            // Create a pending membership for the rest
            else {
                Membership::create([
                    'user_id' => $user->id,
                    'amount' => rand(300000, 1000000),
                    'start_date' => Carbon::now()->addDays(rand(1, 5)),
                    'end_date' => Carbon::now()->addMonths(rand(1, 6)),
                    'status' => 'pending'
                ]);
            }

            // Add a previous expired membership for some users
            if (in_array($user->id, [2, 4, 6, 8])) {
                Membership::create([
                    'user_id' => $user->id,
                    'amount' => rand(300000, 800000),
                    'start_date' => Carbon::now()->subMonths(rand(12, 18)),
                    'end_date' => Carbon::now()->subMonths(rand(6, 11)),
                    'status' => 'expired'
                ]);
            }
        }
    }
}
