<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Areas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['nombre' => 'Secretaria Academica'],
            ['nombre' => 'Coordinacion Academica'],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
