<?php

namespace Database\Seeders;

use App\Models\Programa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programas = [
            ['nombre'    => 'Administracion de Empresas'],
            ['nombre'    => 'Desarrollo de Sistemas de Informacion'],
            ['nombre'    => 'Administración de Negocios Bancarios y Financieros'],
            ['nombre'    => 'Contabilidad'],
            ['nombre'    => 'Secretariado'],
        ];
        foreach($programas as $row){
            Programa::firstOrCreate($row);
        }
    }
}
