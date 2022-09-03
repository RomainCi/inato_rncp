<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Jobs\DeleteEmailVerifJob;
use Illuminate\Http\JsonResponse;
use App\Jobs\VerificationEmailJob;
use App\Models\ForgetPassword;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class InscriptionController extends Controller
{
    public function verification(string $token)
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
                $cookie = Cookie::get();

                foreach ($cookie as $key => $value) {
                    Cookie::queue(Cookie::forget($key));
                }
                return redirect()->route('/welcome');
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function forgetPassword(string $token)
    {
        try {
            $password = ForgetPassword::where('token', $token)->first();
            $user = User::where("id", $password->user_id)
                ->update([
                    "password" => Hash::make($password->password)
                ]);
            $password->delete();
            return redirect()->route('/welcome');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
