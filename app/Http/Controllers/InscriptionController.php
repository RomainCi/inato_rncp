<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Jobs\VerificationEmailJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class InscriptionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                "email" => 'required|email',
                "password" => ['required', 'confirmed', Password::min(8)->numbers()->mixedCase()->symbols()],
                "nom" => 'string|required',
                "prenom" => 'string|required',
                "password_confirmation" => 'required'
            ]);

            $userVerif = UserVerification::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'token' => Str::random(35)
            ]);
            dispatch(new VerificationEmailJob($userVerif['email'], $userVerif['token'], $userVerif['nom']))->delay(now()->addSeconds(3));
            return response()->json([
                "message" => "Vous avez 5 min pour vérifier votre email",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "erreur lors de l'inscritpion",
                "erreur" => $e,
            ], 400);
        }
    }
}
