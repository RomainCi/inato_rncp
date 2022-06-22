<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user = $user->projets;

        return response()->json([
            "message" => "succes",
            "projet" => $user,
        ]);
    }
    public function store(Request $request)
    {
        try {
            $id = Auth::User()->id;
            $request->validate([
                "titre" => 'required|string'
            ]);


            if ($request->file('image')) {
                $request->validate([
                    "image" => 'file|mimes:jpeg,png,jpg,svg|max:2000'
                ]);
                Storage::disk('local')->put('imageUserProjet', $request->image);
            }

            $projet = Projet::create([
                "user_id" => $id,
                "nom" => $request->titre
            ]);

            return response()->json([
                "message" => "succes",
                "nom" => $projet['nom'],
                "id" => $projet['id'],
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
