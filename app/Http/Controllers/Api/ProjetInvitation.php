<?php

namespace App\Http\Controllers\Api;

use App\Events\InvitationEvent;
use App\Models\User;
use App\Models\Projet;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\RoleProjet;
use App\Notifications\HoteNotif;
use App\Notifications\InvitationNotif;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\New_;

class ProjetInvitation extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $invit = Invitation::select('admin_id', 'projet_id', 'status', 'id', 'color')
                ->with('admins')
                ->with('projets')
                ->where('user_id', $user->id)
                ->get();
            $hote = Invitation::select('user_id', 'projet_id', 'status', 'id')
                ->with('guests')
                ->with('projets')
                ->where('admin_id', $user->id)
                ->get();
            return response()->json([
                "invite" => $invit,
                "hote" => $hote,
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
                    $invit =  Invitation::create([
                        "admin_id" => $user->id,
                        "projet_id" => $request->id,
                        "user_id" => $guestUser->id,
                        "color" => "blue"
                    ]);

                    $invite = Invitation::select('admin_id', 'projet_id', 'status', 'id', 'color')
                        ->with('admins')
                        ->with('projets')
                        ->where('id', $invit->id)
                        ->where('admin_id', $user->id)
                        ->first();

                    $user = User::where('id', $invit->user_id)->first();
                    $user->notify(new InvitationNotif($invite));

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
    public function update(Request $request, int $id)
    {
        try {
            $user = auth()->user();
            $request->validate([
                "invitation" => ['required', Rule::in(['accept', 'refuse'])],
                "idInvit" => 'required|integer',
                "adminId" => 'required|integer'
            ]);
            ///verifier cette gate probleme ////
            if (!Gate::allows('invitation-user', [$id, $request->idInvit, $request->adminId])) {
                return response()->json([
                    "message" => "pas l'autorisation"
                ], 403);
            }
            Invitation::where("projet_id", $id)
                ->where("user_id", $user->id)
                ->where('status', 'pending')
                ->update([
                    "status" => $request->invitation,
                ]);
            if ($request->invitation === "accept") {
                $role = RoleProjet::create([
                    "projet_id" => $id,
                    "user_id" => $user->id,
                    "role" => "visiteur",
                ]);
            }
            Broadcast(new InvitationEvent($request->idInvit, $request->invitation));
            $users = User::where('id', $request->adminId)->first();

            $invite = Invitation::select('user_id', 'projet_id', 'status', 'id', 'color')
                ->with('guests')
                ->with('projets')
                ->where('projet_id', $id)
                ->where('id', $request->idInvit)
                ->where('user_id', $user->id)
                ->first();

            $users->notify(new HoteNotif($invite));
            return response()->json([
                "message" => "succes",
                "status" => $request->invitation,
                "idProjet" => $id,
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
