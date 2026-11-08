<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole  = Role::where('slug', 'admin')->firstOrFail();
        $itDept     = Department::where('code', 'IT')->firstOrFail();

        // Default admin account
        User::updateOrCreate(
            ['email' => 'admin@itservicedesk.local'],
            [
                'name'       => 'System Administrator',
                'email'      => 'admin@itservicedesk.local',
                'password'   => Hash::make('Admin@123!'),
                'role_id'    => $adminRole->id,
                'department_id' => $itDept->id,
                'job_title'  => 'System Administrator',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // IT Manager account
        $managerRole = Role::where('slug', 'it-manager')->firstOrFail();
        User::updateOrCreate(
            ['email' => 'manager@itservicedesk.local'],
            [
                'name'       => 'IT Manager',
                'email'      => 'manager@itservicedesk.local',
                'password'   => Hash::make('Manager@123!'),
                'role_id'    => $managerRole->id,
                'department_id' => $itDept->id,
                'job_title'  => 'IT Manager',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // Technician account
        $techRole = Role::where('slug', 'technician')->firstOrFail();
        User::updateOrCreate(
            ['email' => 'technician@itservicedesk.local'],
            [
                'name'       => 'IT Technician',
                'email'      => 'technician@itservicedesk.local',
                'password'   => Hash::make('Tech@123!'),
                'role_id'    => $techRole->id,
                'department_id' => $itDept->id,
                'job_title'  => 'IT Support Technician',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );

        // Demo employee account
        $empRole   = Role::where('slug', 'employee')->firstOrFail();
        $hrDept    = Department::where('code', 'HR')->firstOrFail();
        User::updateOrCreate(
            ['email' => 'employee@itservicedesk.local'],
            [
                'name'       => 'Demo Employee',
                'email'      => 'employee@itservicedesk.local',
                'password'   => Hash::make('Employee@123!'),
                'role_id'    => $empRole->id,
                'department_id' => $hrDept->id,
                'job_title'  => 'Staff HR',
                'is_active'  => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
