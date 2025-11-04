<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = [
        'documento_id',
        'nro',
        'ruta_archivo',
        'url', // opcional si luego agregas la URL pública
    ];

    // Relación con Documento
    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}
