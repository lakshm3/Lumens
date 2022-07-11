<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "password" => "required|string|min:6"
        ]);
        if (sha1($request["password"]) != config("app.admin_password"))
            return response()->json([
                "success" => false,
                "message" => "Mot de passe erroné"
            ]);

        return response()->json([
            "success" => true
        ]);
    }
}
