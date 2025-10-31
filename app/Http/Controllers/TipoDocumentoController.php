<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        return TipoDocumento::select('id','nombre')
            ->orderBy('nombre')
            ->get();
    }
}
