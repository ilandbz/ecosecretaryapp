<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->ruta_archivo);
        // Devuelve algo como: https://tu-dominio.com/storage/documentos/6/archivo.jpg
    }
}
