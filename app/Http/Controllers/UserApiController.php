<?php

namespace App\Http\Controllers;

// use auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use App\Jobs\DeleteEmailVerifJob;
use App\Jobs\VerificationEmailJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;


class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = auth()->user();
            return response()->json([
                "message" => "succes",
                "nom" => $user->nom,
                "prenom" => $user->prenom,
                "email" => $user->email,
                "id" => $user->id
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            $mail = User::where('email', $request->email)->exists();
            if ($mail === true) {
                return response()->json([
                    'succes' => 'notSucces',
                    "message" => "cette email existe deja"
                ],);
            }
            $mail = UserVerification::where('email', $request->email)->exists();
            if ($mail === true) {
                return response()->json([
                    'succes' => 'not succes',
                    "message" => "Valider votre email"
                ],);
            }
            $userVerif = UserVerification::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'token' => Str::random(55)
            ]);
            VerificationEmailJob::dispatch($userVerif['email'], $userVerif['token'], $userVerif['nom'])->delay(now()->addSeconds(3));
            dispatch(new DeleteEmailVerifJob($userVerif['id']))->delay(now()->addMinutes(5));
            return response()->json([
                'succes' => 'succes',
                "message" => "Vous avez 5 min pour vérifier votre email",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "erreur lors de l'inscritpion",
                "erreur" => $e,
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                "password" => ['required', 'confirmed', Password::min(8)->numbers()->mixedCase()->symbols(), 'string'],
                "ancien" => ['required', Password::min(8)->numbers()->mixedCase()->symbols(), 'string'],
                "password_confirmation" => 'required',
            ]);

            $user = auth()->user();
            if (Hash::check($request->ancien, $user->password)) {
                User::where('id', $user->id)
                    ->update([
                        "password" => Hash::make($request->password)
                    ]);
                return response()->json([
                    "message" => "succes",
                ]);
            } else {
                return response()->json([
                    "message" => "password not match"
                ], 400);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                "prenom" => "string|required",
                "nom" => "string|required",
            ]);
            User::where("id", $user->id)
                ->update([
                    "nom" => $request->nom,
                    "prenom" => $request->prenom
                ]);
            return response()->json([
                "message" => "succes",
                "nom" => $request->nom,
                "prenom" => $request->prenom
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function updateEmail(Request $request)
    {
        try {
            $request->validate([
                "email" => "required|email"
            ]);
            $user = auth()->user();
            if ($user->email == $request->email) {
                return response()->json([
                    "message" => "meme adresse mail"
                ]);
            }
            $userVerif = UserVerification::create([
                'email' => $request->email,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'token' => Str::random(55),
                'password' => $user->password,
            ]);
            dispatch(new VerificationEmailJob($userVerif['email'], $userVerif['token'], $userVerif['nom']))->delay(now()->addSeconds(3));
            dispatch(new DeleteEmailVerifJob($userVerif['id']))->delay(now()->addMinutes(5));
            return response()->json([
                "message" => "oki"
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = auth()->user();
        $userCompte = User::findOrFail($user->id);
        $userCompte->delete();
        Auth::guard('web')->logout();
        return response()->json([
            "message" => 'succes'
        ]);
    }

    public function deconnexion(Request $request)
    {
        Auth::guard('web')->logout();

        return response()->json('', 204);
    }
}
