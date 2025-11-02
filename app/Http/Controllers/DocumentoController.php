<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
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
            'tipo_documento_id'  => ['required','exists:tipo_documentos,id'],
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

    public function uploadArchivos(Request $request, \App\Models\Documento $documento)
    {
        $request->validate([
            'files'   => ['required','array'],
            'files.*' => ['file','max:5120','mimes:pdf,jpg,jpeg,png'] // 5MB
        ]);

        $guardados = [];
        foreach ($request->file('files') as $i => $file) {
            $path = $file->store("documentos/{$documento->id}", 'private');

            $archivo = Archivo::create([
                'documento_id' => $documento->id,
                'nro'          => $i+1,
                'ruta_archivo' => $path,
            ]);

            $guardados[] = $archivo;
        }

        return response()->json([
            'message'  => 'Archivos subidos',
            'archivos' => $guardados
        ], 201);
    }

}
