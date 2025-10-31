<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'user_id', 'area_id', 'tipo_documento_id',
        'titulo', 'contenido', 'fecha_documento', 'hora_documento'
    ];
    public function area() { return $this->belongsTo(Area::class); }
    public function tipoDocumento() { return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id'); }
}
