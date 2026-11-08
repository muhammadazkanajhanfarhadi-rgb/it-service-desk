<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Top-level categories with subcategories
        $categories = [
            [
                'name' => 'Network & Connectivity',
                'icon' => 'wifi',
                'color' => '#38BDF8',
                'children' => ['Wi-Fi Issues', 'VPN Access', 'Network Slow', 'Port / Switch'],
            ],
            [
                'name' => 'Hardware',
                'icon' => 'computer',
                'color' => '#A78BFA',
                'children' => ['Laptop / PC', 'Monitor', 'Printer', 'Peripheral', 'Server Hardware'],
            ],
            [
                'name' => 'Software & Applications',
                'icon' => 'apps',
                'color' => '#4F8CFF',
                'children' => ['Operating System', 'Office Apps', 'Business Software', 'Developer Tools', 'Installation Request'],
            ],
            [
                'name' => 'Account & Access',
                'icon' => 'manage_accounts',
                'color' => '#22C55E',
                'children' => ['Password Reset', 'New Account', 'Access Rights', 'Email Issues'],
            ],
            [
                'name' => 'Security',
                'icon' => 'security',
                'color' => '#EF4444',
                'children' => ['Virus / Malware', 'Phishing', 'Data Breach', 'Unauthorized Access'],
            ],
            [
                'name' => 'Email & Communication',
                'icon' => 'mail',
                'color' => '#F59E0B',
                'children' => ['Email Not Working', 'Email Configuration', 'Calendar Issues', 'Teams / Zoom'],
            ],
            [
                'name' => 'Service Request',
                'icon' => 'assignment',
                'color' => '#60A5FA',
                'children' => ['New Equipment', 'Software License', 'Access Request', 'Other Request'],
            ],
        ];

        foreach ($categories as $order => $data) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'       => $data['name'],
                    'slug'       => Str::slug($data['name']),
                    'icon'       => $data['icon'],
                    'color'      => $data['color'],
                    'parent_id'  => null,
                    'is_active'  => true,
                    'sort_order' => $order,
                ]
            );

            foreach ($data['children'] as $childOrder => $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($data['name'] . '-' . $childName)],
                    [
                        'name'       => $childName,
                        'slug'       => Str::slug($data['name'] . '-' . $childName),
                        'parent_id'  => $parent->id,
                        'is_active'  => true,
                        'sort_order' => $childOrder,
                    ]
                );
            }
        }
    }
}
