<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Projet;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class ProjetInvitation extends Controller
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
            $invit = Invitation::select('admin_id', 'projet_id', 'status')
                ->with('admins')
                ->with('projets')
                ->where('user_id', $user->id)
                ->get();

            // $hote = $user->adminInvitation;
            // $test = User::with('guestInvitation')->get();
            // $test = $user->guestInvitation->with('test');
            // dd($test);
            // $invite = $user->guestInvitation;
            // dd($invite);
            return response()->json([
                "invite" => $invit,
            ]);
            // return response()->json([
            //     "hoteProjet" => $hote,
            //     "invite" => $invite,
            //     "message" => "succes",
            // ]);
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
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                "id" => 'required|integer',
                "email" => 'required|email',
            ]);
            $user = auth()->user();

            if (!Gate::allows('admin-projet', $request->id)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $projet = Projet::findOrFail($request->id);
                $guestUser = User::where('email', $request->email)->first();
                if ($guestUser === null) {
                    return response()->json([
                        "message" => "email inconnue"
                    ], 400);
                }
                if (Gate::allows('verification-invitation', [$guestUser->id, $projet])) {
                    return response()->json([
                        "message" => "stop spam",
                    ], 409);
                } else {
                    Invitation::create([
                        "admin_id" => $user->id,
                        "projet_id" => $request->id,
                        "user_id" => $guestUser->id
                    ]);
                    return response()->json([
                        "message" => "succes"
                    ]);
                };
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $request->validate([
                "invitation" => ['required', Rule::in(['accept', 'refuse'])]
            ]);
            if (!Gate::allows('invitation-user', $id)) {
                return response()->json([
                    "message" => "pas l'autorisation"
                ], 403);
            } else {
            }

            Invitation::where("projet_id", $id)
                ->where("user_id", $user->id)
                ->where('status', 'pending')
                ->update([
                    "status" => $request->invitation,
                ]);
            // $test = Invitation::all();
            // dd($test);
            // $projet = Projet::findOrFail($id);
            // $projet = Projet::all();
            // $projet = $projet->projetI->where('user_id', $user->id);
            // $projet->status = $request->invitation;
            // $projet->save();
            // dd($projet);
            $guest = $user->guestInvitation->refresh();
            $toto = $user->guestInvitation;
            dd($toto);
            // $guest->refresh();
            // dd($guest);
            //     ->where('projet_id', $id)
            //     ->where('status', 'pending')
            //     ->update([
            //         "status" => $request->invitation,
            //     ]);
            // dd($guest);
            return response()->json([
                "message" => "succes",
                // "invite" => $guest
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
