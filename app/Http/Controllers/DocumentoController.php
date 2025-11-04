<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        /*return Documento::where('user_id', $request->user()->id)
            ->with(['area:id,nombre','tipoDocumento:id,nombre'])
            ->orderByDesc('created_at')
            ->get();*/
    
        $user = $request->user();

        $query = Documento::with(['tipoDocumento', 'area', 'user']);

        if (!$user->isAdmin()) {
            // Si no es administrador, solo muestra los documentos del usuario
            $query->where('user_id', $user->id);
        }

        $documentos = $query->orderBy('fecha_documento', 'desc')->get();

        return response()->json($documentos);

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
            $path = $file->store("documentos/{$documento->id}", 'public');

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


    public function publicIndex(Request $request)
    {
        return Documento::get();
        $query = Documento::with([
                'tipoDocumento:id,nombre',
                'area:id,nombre',
                'user:id,name'
            ])
            ->orderByDesc('fecha_documento');
        // Retorna sin autenticación
        $documentos = $query->get();
        return response()->json($documentos);
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('tipo_documento_id')) {
            $query->where('tipo_documento_id', $request->tipo_documento_id);
        }

        // Retorna sin autenticación
        $documentos = $query->get();

        // Incluye archivos si existen
        $documentos->load('archivos:id,documento_id,nro,ruta_archivo');

        return response()->json($documentos);
    }

}
