<?php

namespace App\Http\Controllers\Api;

use App\Models\Lists;
use App\Models\RoleProjet;
use Illuminate\Http\Request;
use App\Events\AjoutListeEvent;
use App\Events\DeleteListeEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Events\UpdateTitreListeEvent;
use Illuminate\Support\Facades\Broadcast;

class ListesController extends Controller
{
    public function show(int $id)
    {
        try {
            if (!Gate::allows('acces-lists', $id)) {
                return response()->json([
                    "message" => "Vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $lists = Lists::with('listProjet')
                    ->with('listTaches')
                    ->with('listUser')
                    ->where('projet_id', $id)
                    ->get();
                $user = auth()->user();
                $user = RoleProjet::where('projet_id', $id)
                    ->where('user_id', $user->id)
                    ->first();

                return response()->json([
                    "role" => $user->role,
                    "message" => "succes",
                    "listes" => $lists,
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function store(int $id, Request $request)
    {
        try {
            $request->validate([
                "titreListe" => "string|required",
            ]);
            $user = auth()->user();

            if (!Gate::allows('editeur-projet', $id)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $listes = Lists::create([
                    "titre_list" => $request->titreListe,
                    "projet_id" => $id,
                    "user_id" => $user->id,
                ]);
                $listes = Lists::with('listProjet')
                    ->with('listTaches')
                    ->with('listUser')
                    ->findOrFail($listes->id);
                Broadcast(new AjoutListeEvent($id, $listes));
                return response()->json([
                    "message" => "succes",
                    "liste" => $listes,
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function update(int $id, Request $request)
    {
        try {
            $request->validate([
                "titreListe" => 'required|string',
                "idListe" => 'required|integer'
            ]);
            $listes = Lists::with('listRole')
                ->where('id', $request->idListe)
                ->where('projet_id', $id)
                ->first();
            if ($listes === null) {
                return response()->json([
                    "message" => "erreur",
                ], 400);
            }
            if (!Gate::allows('move-item', $listes)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $listes->update([
                    "titre_list" => $request->titreListe,
                ]);
                Broadcast(new UpdateTitreListeEvent($id, $listes));
                return response()->json([
                    "message" => "succes",
                ]);
            }
        } catch (\Exception $e) {
            //throw $th;
        }
    }
    public function destroy(int $id)
    {
        try {
            $listes = Lists::with('listRole')
                ->where('id', $id)
                ->first();
            if ($listes === null) {
                return response()->json([
                    "message" => "erreur",
                ], 400);
            }
            if (!Gate::allows('move-item', $listes)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $listes->delete();
                Broadcast(new DeleteListeEvent($listes->projet_id, $listes));
                return response()->json([
                    "message" => 'succes'
                ]);
            }
        } catch (\Exception $e) {
            //throw $th;
        }
    }
}
