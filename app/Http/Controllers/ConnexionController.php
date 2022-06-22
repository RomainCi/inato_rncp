<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class ConnexionController extends Controller
{
    public function authentification(Request $request)
    {
        try {
            $request->validate([
                "email" => 'required|email',
                "password" => ['required', Password::min(8)->numbers()->mixedCase()->symbols()],
            ]);
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            $user = User::where('email', $request->email)->first();
            if (Auth::attempt($credentials)) {
                return response()->json([
                    "message" => "oki"
                ]);
            } else {
                return response()->json(["message" => "pas good"]);
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                "message" => "mot de passe ou email faux",
                "erreur" => $e
            ]);
        }
    }
}
