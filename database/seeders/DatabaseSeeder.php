<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Order matters — respect foreign key dependencies.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // 1. Roles (no deps)
            DepartmentSeeder::class, // 2. Departments (no deps)
            LocationSeeder::class,   // 3. Locations (no deps)
            PrioritySeeder::class,   // 4. Priorities (no deps)
            SlaPolicySeeder::class,  // 5. SLA Policies (needs Priority)
            CategorySeeder::class,   // 6. Categories (self-referencing, handled)
            AdminUserSeeder::class,  // 7. Users (needs Role, Department)
        ]);
    }
}
