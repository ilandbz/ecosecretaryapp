<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        
        $docs = Documento::where('user_id', $request->user()->id)
            ->with('tipoDocumento')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($docs);
    }

    public function store(Request $request)
    {
        $doc = Documento::create([
            'user_id' => $request->user()->id,
            'tipo_documento_id' => $request->tipo_documento_id,
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_documento' => now()->toDateString(),
            'hora_documento' => now()->toTimeString(),
        ]);

        return response()->json([
            'message' => 'Documento registrado correctamente',
            'documento' => $doc
        ]);
    }
}
