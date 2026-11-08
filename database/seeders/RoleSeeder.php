<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Employee',    'slug' => 'employee',    'description' => 'Karyawan — dapat membuat dan memantau tiket'],
            ['name' => 'Technician',  'slug' => 'technician',  'description' => 'Teknisi IT — menangani tiket yang di-assign'],
            ['name' => 'IT Manager',  'slug' => 'it-manager',  'description' => 'Manager IT — monitoring SLA, laporan, dan workload'],
            ['name' => 'Admin',       'slug' => 'admin',       'description' => 'Administrator — mengelola konfigurasi sistem'],
            ['name' => 'Approver',    'slug' => 'approver',    'description' => 'Approver — menyetujui service request tertentu'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
