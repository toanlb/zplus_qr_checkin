<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo vai trò mặc định
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Quản trị viên hệ thống với toàn quyền truy cập'
            ],
            [
                'name' => 'member',
                'description' => 'Thành viên chỉ có quyền xem và sửa profile của mình'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
