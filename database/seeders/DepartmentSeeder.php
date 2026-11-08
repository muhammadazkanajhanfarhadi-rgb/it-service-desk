<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Information Technology', 'code' => 'IT',  'description' => 'Departemen Teknologi Informasi'],
            ['name' => 'Human Resources',        'code' => 'HR',  'description' => 'Departemen Sumber Daya Manusia'],
            ['name' => 'Finance & Accounting',   'code' => 'FIN', 'description' => 'Departemen Keuangan & Akuntansi'],
            ['name' => 'Operations',             'code' => 'OPS', 'description' => 'Departemen Operasional'],
            ['name' => 'Marketing',              'code' => 'MKT', 'description' => 'Departemen Pemasaran'],
            ['name' => 'Management',             'code' => 'MGT', 'description' => 'Manajemen dan Direksi'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
