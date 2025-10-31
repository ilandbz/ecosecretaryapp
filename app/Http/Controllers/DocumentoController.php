<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        return Documento::where('user_id', $request->user()->id)
            ->with(['area:id,nombre','tipoDocumento:id,nombre'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'             => ['required','string','max:255'],
            'contenido'          => ['nullable','string'],
            'tipo_documento_id'  => ['required','exists:tipo_documento,id'], // o nullable si quieres
            'area_id'            => ['required','exists:areas,id'],
        ]);

        $doc = Documento::create([
            'user_id'           => $request->user()->id,
            'area_id'           => $data['area_id'],
            'tipo_documento_id' => $data['tipo_documento_id'],
            'titulo'            => $data['titulo'],
            'contenido'         => $data['contenido'] ?? null,
            'fecha_documento'   => now()->toDateString(),
            'hora_documento'    => now()->format('H:i:s'),
        ]);

        $doc->load('area:id,nombre','tipoDocumento:id,nombre');

        return response()->json(['documento' => $doc], 201);
    }
}
