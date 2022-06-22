<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Jobs\DeleteEmailVerifJob;
use Illuminate\Http\JsonResponse;
use App\Jobs\VerificationEmailJob;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class InscriptionController extends Controller
{
    public function verification($token)
    {

        try {
            $userCompte = auth()->user();
            if ($userCompte == null) {
                $user = UserVerification::where('token', $token)->first();
                User::create([
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                ]);
                UserVerification::findOrFail($user['id'])
                    ->delete();
                return redirect()->route('/welcome');
            } else {
                $user = UserVerification::where('token', $token)->first();

                User::where("id", $userCompte->id)
                    ->update([
                        "nom" => $user->nom,
                        "prenom" => $user->prenom,
                        "password" => $userCompte->password,
                        "email" => $user->email
                    ]);
                UserVerification::findOrFail($user['id'])
                    ->delete();
                Cookie::queue(Cookie::forget('laravel_session'));
                return redirect()->route('/welcome');
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
