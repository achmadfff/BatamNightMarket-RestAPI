<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    protected function respondWithTokenuser($token)
    {
        return response()->json([
            'token' => $token,
            'role' => 'user',
            'token_type' => 'bearer',
            'expires_in' => env('JWT_TTL') . ' minute'
        ], 200);
    }
    protected function respondWithTokenowner($token)
    {
        return response()->json([
            'token' => $token,
            'role' => 'owner',
            'token_type' => 'bearer',
            'expires_in' => env('JWT_TTL') . ' minute'
        ], 200);
    }
}
