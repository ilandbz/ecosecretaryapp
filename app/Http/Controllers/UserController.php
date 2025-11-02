<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserData(Request $request)
    {
        $user_id = auth()->user()->id;

        $user = User::with(['role', 'persona'])->findOrFail($user_id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'dni' => $user->dni,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->nombre,
            ] : null,
        ]);
    }
}
