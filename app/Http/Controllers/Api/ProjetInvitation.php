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
            $notif = $user->notifications()->where('notifiable_id', $user->id)->get();
            foreach ($notif as $key => $value) {
                $invitation[] = ["invitation" => Invitation::where('id', $value->data["invitation"][0]["id"])
                    ->with('admins')
                    ->with('projets')
                    ->with('guests')
                    ->get(), "notification" => ["notif" => $value->read_at]];
            }

            return response()->json([
                "invite" => $invitation ?? [],
                "message" => "succes",
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
                    ]);

                    $invite = Invitation::with('admins')
                        ->with('projets')
                        ->with('guests')
                        ->where('id', $invit->id)
                        ->where('admin_id', $user->id)
                        ->get();

                    $user = User::where('id', $invite[0]->user_id)->first();
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
            $usersInvite = User::where('id', $request->adminId)->first();

            $invite = Invitation::with('admins')
                ->with('projets')
                ->with("guests")
                ->where('projet_id', $id)
                ->where('id', $request->idInvit)
                ->where('user_id', $user->id)
                ->get();

            $usersInvite->notify(new HoteNotif($invite));
            $readAt = "0";
            foreach ($user->unreadNotifications as  $value) {
                if ($value->data["invitation"][0]["id"] == $request->idInvit) {
                    $value->markAsRead();
                    $readAt = $value->read_at;
                }
            };
            return response()->json([
                "message" => "succes",
                "status" => $request->invitation,
                "id" => $request->idInvit,
                "readAt" => $readAt
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
        try {
            $user = auth()->user();
            ///////////////////ameliorer la securite /////////////
            $invitation = Invitation::findOrFail($id);
            if ($invitation->user_id != $user->id) {
                if ($invitation->admin_id != $user->id) {
                    return response()->json([
                        "message" => "pas acces"
                    ], 403);
                }
            }
            if ($invitation->status == "pending") {
                $invitation->delete();
            }
            $readAt = "0";
            foreach ($user->unreadNotifications as  $value) {
                if ($value->data["invitation"][0]["id"] == $id) {
                    $value->markAsRead();
                    $readAt = $value->read_at;
                }
            };
            foreach ($user->notifications as  $value) {
                if ($value->data["invitation"][0]["id"] == $id) {
                    $value->delete();
                }
            };
            return response()->json([
                "message" => "succes",
                "id" => $id,
                "readAt" => $readAt
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
