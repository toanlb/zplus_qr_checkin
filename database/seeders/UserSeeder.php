<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0987654321',
            'birth_date' => now()->subYears(30),
            'address' => 'Số 123, Đường ABC, Quận XYZ, Hà Nội',
            'member_type' => 'premium',
            'role' => 'admin',
            'qr_code' => md5('admin@example.com' . time()),
        ]);

        // Create regular members with different membership types
        $memberTypes = ['basic', 'standard', 'premium'];
        
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'Thành viên ' . $i,
                'email' => 'member' . $i . '@example.com',
                'password' => Hash::make('password'),
                'phone' => '09' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'birth_date' => now()->subYears(rand(18, 60))->subDays(rand(1, 365)),
                'address' => 'Số ' . rand(1, 100) . ', Đường ' . chr(rand(65, 90)) . ', Quận ' . rand(1, 12) . ', Hà Nội',
                'member_type' => $memberTypes[array_rand($memberTypes)],
                'role' => 'member',
                'qr_code' => md5('member' . $i . '@example.com' . time()),
            ]);
        }
    }
}
