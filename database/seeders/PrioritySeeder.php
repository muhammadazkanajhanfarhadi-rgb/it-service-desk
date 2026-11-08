<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            [
                'name'  => 'Critical',
                'slug'  => 'critical',
                'level' => 1,
                'color' => 'status-danger',
                'icon'  => 'emergency',
            ],
            [
                'name'  => 'High',
                'slug'  => 'high',
                'level' => 2,
                'color' => 'status-warning',
                'icon'  => 'priority_high',
            ],
            [
                'name'  => 'Medium',
                'slug'  => 'medium',
                'level' => 3,
                'color' => 'status-info',
                'icon'  => 'remove',
            ],
            [
                'name'  => 'Low',
                'slug'  => 'low',
                'level' => 4,
                'color' => 'status-success',
                'icon'  => 'arrow_downward',
            ],
        ];

        foreach ($priorities as $priority) {
            Priority::updateOrCreate(['slug' => $priority['slug']], $priority);
        }
    }
}
