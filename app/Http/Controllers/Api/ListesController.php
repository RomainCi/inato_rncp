<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lists;
use Illuminate\Support\Facades\Gate;

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
                return response()->json([
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

                return response()->json([
                    "message" => "succes",
                    "liste" => $listes,
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
