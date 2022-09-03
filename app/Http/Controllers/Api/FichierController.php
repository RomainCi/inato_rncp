<?php

namespace App\Http\Controllers\Api;

use App\Models\Fichier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lists;
use App\Models\Projet;
use App\Models\Taches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class FichierController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = Auth::User();
            $request->validate([
                "tache_id" => 'required|integer'
            ]);
            $tache = Taches::findOrFail($request->tache_id);
            $liste = Lists::findOrFail($tache->list_id);
            if (!Gate::allows('editeur-projet', $liste->projet_id)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                if ($request->hasFile('fichier')) {
                    $request->validate([
                        "fichier" => 'max:10000',
                    ]);
                    $file = $request->file('fichier');
                    $finfo = finfo_open(FILEINFO_MIME);
                    $name = time() . $file->getClientOriginalName();
                    $test = finfo_file($finfo, $file);
                    finfo_close($finfo);
                    $filePath = 'taches/' . $user->nom . 'fichier/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    Fichier::create([
                        "path" => $filePath,
                        "tache_id" => $request->tache_id,
                        "mime" => $test,
                        "nom" => $file->getClientOriginalName()
                    ]);
                    return response()->json([
                        "message" => "succes",
                    ]);
                } else {
                    return response()->json([
                        "message" => "fichier null"
                    ]);
                }
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function index(int $id)
    {
        try {
            $tache = Taches::findOrFail($id);
            $liste = Lists::findOrFail($tache->list_id);
            if (!Gate::allows('acces-lists', $liste->projet_id)) {
                return response()->json([
                    "message" => "Vous n'avez pas l'autorisation"
                ], 403);
            }
            $fichier = Fichier::where('tache_id', $id)->select('id', 'nom')->get();

            return response()->json([
                "message" => "sucess",
                "fichier" => $fichier
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function downloadFichier(int $id)
    {
        $fichier = Fichier::findOrFail($id);
        $tache = Taches::findOrFail($fichier->tache_id);
        $liste = Lists::findOrFail($tache->list_id);
        if (!Gate::allows('acces-lists', $liste->projet_id)) {
            return response()->json([
                "message" => "Vous n'avez pas l'autorisation"
            ], 403);
        } else {
            $headers = [
                'ResponseContentType' => $fichier->mime,
                "ResponseNomType" => $fichier->nom
            ];
            if (Storage::disk('s3')->exists($fichier->path)) {
                return Storage::download($fichier->path, $fichier->nom, $headers);
            }
        }
    }
}
