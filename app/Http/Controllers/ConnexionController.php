<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteForgetPasswordJob;
use App\Jobs\ForgetPasswordJob;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ForgetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;


class ConnexionController extends Controller
{
    public function authentification(Request $request): \Illuminate\Http\JsonResponse
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

            // $user = User::where('email', $request->email)->first();
            if (Auth::attempt($credentials)) {

                return response()->json([
                    "message" => "success",
                ]);
            } else {
                return response()->json(["message" => "erreur"]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "mot de passe ou email faux",
                "error" => $e
            ]);
        }
    }
    public function forgetPassword(Request $request)
    {
        try {
            $request->validate([
                "email" => 'required|email'
            ]);

            $user = User::where('email', $request->email)->first();
            if ($user == null) {
                return response()->json([
                    "message" => "email incconue",

                ], 403);
            } else {
                function generateRandomString($length)
                {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $specials = '@%&*#?!';
                    $majuscule = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $chiffre = '0123456789';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < $length - 1; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    // Add the special c

                    $randomString .= $specials[rand(0, strlen($specials) - 1)];
                    $randomString .= $majuscule[rand(0, strlen($majuscule) - 1)];
                    $randomString .= $chiffre[rand(0, strlen($chiffre) - 1)];
                    return str_shuffle($randomString);
                }
                $password =  generateRandomString(30);

                $resetPassword = ForgetPassword::create([
                    "user_id" => $user->id,
                    "token" => Str::random(55),
                    "password" => $password
                ]);
                ForgetPasswordJob::dispatch($user->email, $resetPassword->token, $resetPassword->password)->delay(now()->addSecond(3));
                DeleteForgetPasswordJob::dispatch($resetPassword->id)->delay(now()->addMinutes(5));
                // dispatch(new ForgetPasswordJob($user->email, $resetPassword->token, $resetPassword->password))->delay(now()->addSecond(3));
                // dispatch(new DeleteForgetPasswordJob($resetPassword->id))->delay(now()->addMinutes(5));
                return response()->json([
                    "message" => "succes"
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
