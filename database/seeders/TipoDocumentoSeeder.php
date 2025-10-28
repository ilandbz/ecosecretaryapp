<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            'Oficio',
            'Solicitud',
            'Informe',
            'Memorándum',
            'Resolución',
            'Certificado',
            'Factura',
            'Recibo',
            'Contrato',
            'Acta',
            'Curriculum Vitae',
            'DNI',
            'Pasaporte',
        ];

        foreach ($documentos as $nombre) {
            TipoDocumento::firstOrCreate(['nombre' => $nombre]);
        }

    }
}
