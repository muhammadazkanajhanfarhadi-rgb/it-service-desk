<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Kantor Pusat — Lantai 1', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'city' => 'Jakarta'],
            ['name' => 'Kantor Pusat — Lantai 2', 'building' => 'Gedung Utama', 'floor' => 'Lantai 2', 'city' => 'Jakarta'],
            ['name' => 'Kantor Pusat — Lantai 3', 'building' => 'Gedung Utama', 'floor' => 'Lantai 3', 'city' => 'Jakarta'],
            ['name' => 'Data Center',             'building' => 'Gedung Server', 'floor' => 'Basement', 'city' => 'Jakarta'],
            ['name' => 'Cabang Surabaya',          'building' => 'Kantor Cabang', 'floor' => null,      'city' => 'Surabaya'],
            ['name' => 'Cabang Bandung',           'building' => 'Kantor Cabang', 'floor' => null,      'city' => 'Bandung'],
            ['name' => 'Remote / WFH',             'building' => null,           'floor' => null,       'city' => null],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['name' => $loc['name']], $loc);
        }
    }
}
