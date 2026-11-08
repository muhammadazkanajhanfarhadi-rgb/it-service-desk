<?php

namespace Database\Seeders;

use App\Models\Priority;
use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        // SLA baseline from PRD Section 7
        // Priority → [response_minutes, resolution_minutes]
        $policies = [
            'critical' => [
                'name'               => 'Critical SLA',
                'response_minutes'   => 15,      // 15 menit
                'resolution_minutes' => 120,     // 2 jam
                'business_hours_only'=> false,   // 24/7
                'description'        => 'Layanan/sistem kritis down — response 15m, resolve 2j',
            ],
            'high' => [
                'name'               => 'High SLA',
                'response_minutes'   => 30,      // 30 menit
                'resolution_minutes' => 240,     // 4 jam
                'business_hours_only'=> false,
                'description'        => 'Gangguan pekerjaan penting — response 30m, resolve 4j',
            ],
            'medium' => [
                'name'               => 'Medium SLA',
                'response_minutes'   => 120,     // 2 jam
                'resolution_minutes' => 480,     // 8 jam
                'business_hours_only'=> true,    // business hours
                'description'        => 'Gangguan satu/sebagian user — response 2j, resolve 8j',
            ],
            'low' => [
                'name'               => 'Low SLA',
                'response_minutes'   => 240,     // 4 jam
                'resolution_minutes' => 1440,    // 24 jam
                'business_hours_only'=> true,    // business hours
                'description'        => 'Request atau gangguan minor — response 4j, resolve 24j',
            ],
        ];

        foreach ($policies as $prioritySlug => $policy) {
            $priority = Priority::where('slug', $prioritySlug)->first();
            if ($priority) {
                SlaPolicy::updateOrCreate(
                    ['priority_id' => $priority->id],
                    array_merge($policy, ['priority_id' => $priority->id])
                );
            }
        }
    }
}
