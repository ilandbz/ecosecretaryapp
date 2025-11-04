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
        // Normaliza la ruta (arregla casos como "6documentos/...")
        $path = ltrim($this->ruta_archivo, '/');
        // si por error quedó "6documentos/..." lo reparamos
        if (preg_match('/^\d+documentos\//', $path)) {
            $path = preg_replace('/^\d+documentos\//', 'documentos/', $path);
        }

        // usa el disco public
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        // fallback por si la config de filesystems no devuelve url
        return asset('storage/'.$path);
    }
}
