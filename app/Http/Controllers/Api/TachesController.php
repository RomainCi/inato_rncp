<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Lists;
use App\Models\Taches;
use Illuminate\Http\Request;
use App\Events\MoveTacheEvent;
use App\Events\AjoutTacheEvent;
use App\Events\DeleteTacheEvent;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\UpdateTitreTacheEvent;
use App\Models\Fichier;
use Illuminate\Support\Facades\Storage;

class TachesController extends Controller
{
    public function show(int $id)
    {
        if (!Gate::allows('acces-lists', $id)) {
            return response()->json([
                "message" => "Vous n'avez pas l'autorisation"
            ], 403);
        } else {
            // $lists = Lists::where('projet_id', $id)
            //     ->get();
            $user = auth()->user();
            $test = Lists::with('listProjet')
                ->with('listTaches')
                ->with('listUser')
                ->where('user_id', $user->id)
                // ->where('list_id', 1)
                ->where('projet_id', $id)
                ->get();
            dd($test);
            // return response()->json([
            //     "message" => "succes",
            //     "listes" => $lists,
            // ]);
        }
    }

    public function store(int $id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                "titreTache" => 'required|string'
            ]);
            $index = null;
            $user = auth()->user();
            $list = Lists::with('listTaches')
                ->findOrFail($id);

            if (!Gate::allows('editeur-projet', $list->projet_id)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                foreach ($list->listTaches as $key => $value) {
                    $index = $value->index;
                }

                if ($index === null) {
                    $index = 0;
                } else {
                    $index = $index + 1;
                }

                $taches = Taches::create([
                    "titre_tache" => $request->titreTache,
                    "list_id" => $id,
                    "user_id" => $user->id,
                    "index" => $index
                ]);
                Broadcast(new AjoutTacheEvent($list->projet_id, $taches));
                return response()->json([
                    "message" => "succes",
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updatePosition(int $id, Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $request->validate([
                "tacheId" => "integer|required",
                "listeId" => "integer|required",
                "index" => "integer|required",
                "lastListeId" => "integer|required",
                "lastIndex" => "integer|required"
            ]);
            $tache = Taches::findOrfail($request->tacheId);
            $listes = Lists::with('listTaches')
                ->with('listRole')
                ->where('id', $request->listeId)
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
                $listes = $listes->listTaches->where('index', '>=', $request->index);
                $lastListes = Lists::with('listTaches')
                    ->where('id', $request->lastListeId)
                    ->first();
                $lastListes = $lastListes->listTaches->where('index', '>', $request->lastIndex);
                foreach ($lastListes as $key => $value) {
                    $taches = Taches::findOrfail($value->id);
                    $taches->update([
                        "index" => $value->index - 1,
                    ]);
                }
                foreach ($listes as $key => $value) {
                    $taches = Taches::findOrfail($value->id);
                    $taches->update([
                        "index" => $value->index + 1,
                    ]);
                }
                $tache->update([
                    "list_id" => $request->listeId,
                    "index" => $request->index,
                ]);
                $nlistes = Lists::with('listTaches')
                    ->where('id', $request->listeId)
                    ->first();
                $nlastListes = Lists::with('listTaches')
                    ->where('id', $request->lastListeId)
                    ->first();
                Broadcast(new MoveTacheEvent($id, $nlastListes, $nlistes));
                return response()->json([
                    "message" => "succes",
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                "titreTache" => 'required|string',
                "idTache" => 'required|integer'
            ]);
            $tache = Taches::findOrFail($request->idTache);
            $listes = Lists::with('listTaches')
                ->with('listRole')
                ->where('id', $tache->list_id)
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
                $tache->update([
                    "titre_tache" => $request->titreTache
                ]);
                Broadcast(new UpdateTitreTacheEvent($id, $tache));
                return response()->json([
                    "message" => "succes",
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function destroy(int $id)
    {
        try {
            $tache = Taches::findOrFail($id);
            $listes = Lists::with('listTaches')
                ->with('listRole')
                ->where('id', $tache->list_id)
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
                $grandListes = $listes->listTaches->where('index', '>', $tache->index);
                foreach ($grandListes as $key => $value) {
                    $taches = Taches::findOrfail($value->id);
                    $taches->update([
                        "index" => $value->index - 1,
                    ]);
                }
                $tache->delete();
                $nlistes = Lists::with('listProjet')
                    ->with('listTaches')
                    ->with('listUser')
                    ->where('id', $tache->list_id)
                    ->first();
                Broadcast(new DeleteTacheEvent($listes->projet_id, $nlistes));
                return response()->json([
                    "message" => "succes",
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
