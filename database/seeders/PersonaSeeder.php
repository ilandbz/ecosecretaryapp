<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Persona;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$path = database_path('alumnos.csv');
        $path = public_path('alumnos.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \Exception("No se pudo abrir el archivo CSV: {$path}");
        }

        $firstLine = true;
        $count = 0;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            $data = array_map(fn($v) => trim($v, "\"'"), $data);

            $numero_dni      = $data[0] ?? null;
            $nombres         = $data[1] ?? null;
            $apellido_paterno= $data[2] ?? null;
            $apellido_materno= $data[3] ?? null;
            $fecha_nacimiento= !empty($data[4]) && $data[4] != 'NULL' ? $data[4] : '2024-10-01';
            $direccion       = ($data[5] ?? null) != 'NULL' ? $data[5] : null;
            $telefono        = ($data[6] ?? null) != 'NULL' ? $data[6] : null;
            $programa_id      = ($data[7] ?? null) != 'NULL' ? $data[7] : null;

            if ($numero_dni && $nombres) {
                $persona = Persona::firstOrCreate(
                    ['numero_dni' => $numero_dni],
                    [
                        'nombres'          => $nombres,
                        'apellido_paterno' => $apellido_paterno,
                        'apellido_materno' => $apellido_materno,
                        'fecha_nacimiento' => $fecha_nacimiento,
                        'direccion'        => $direccion,
                        'telefono'         => $telefono,
                    ]
                );
                $alumno = Alumno::firstOrCreate([
                    'persona_id' => $persona->id,
                    'programa_id' => $programa_id
                ]);
                $superadmin = User::firstOrCreate([
                    'name'      => $numero_dni,
                    'dni'       => $numero_dni,
                    'password'  => Hash::make($numero_dni),
                    'role_id'   => Role::where('nombre', 'Alumno')->first()->id,
                ]);
                $count++;
            }
        }

        fclose($handle);

        $this->command->info("✅ Se importaron {$count} personas desde el CSV correctamente.");
    }
}
