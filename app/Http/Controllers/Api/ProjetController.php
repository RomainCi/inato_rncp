<?php

namespace App\Http\Controllers\Api;

use App\Models\Projet;
use App\Events\RoleEvent;
use App\Models\RoleProjet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;
use App\Models\UserBackgroundImagePublique;


class ProjetController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $projets = RoleProjet::with('userRole')
                ->with('projetRole')
                ->where('user_id', $user->id)
                ->get();
            // $projetsAdmin = $projets->where('role', 'admin');
            // $allRole = RoleProjet::with('userRole')
            //     ->get();
            // foreach ($projetsAdmin as $key => $value) {
            //     $adminProjet[$key] = $allRole->where('projet_id', $value->projet_id)->where('user_id', '!=', $user->id);
            // };
            foreach ($projets as $key => $value) {
                if (Storage::disk('s3')->exists($value->projetRole->path)) {
                    $projet[$key] = ["urlprivate" => Storage::temporaryUrl(
                        $value->projetRole->path,
                        now()->addMinutes(1)
                    ), "titre" => $value->projetRole->nom, "id" => $value->projetRole->id, "role" => $value->role];
                }
            }
            // $image = UserBackgroundImagePublique::all();
            // foreach ($image as $key => $value) {
            //     if (Storage::disk('s3')->exists($value->chemin)) {
            //         $url[$key] = ["url" => Storage::temporaryUrl(
            //             $value->chemin,
            //             now()->addMinutes(1)
            //         ), "id" => $value->id];
            //     }
            // }



            return response()->json([
                "projet" => $projet ?? null,
                // "adminProjet" => $adminProjet ?? 0,
            ]);
            // return response()->json([
            //     "message" => "succes",
            //     "publicUrl" => $url,
            //     "projet" => $projet ?? null,
            //     "adminProjet" => $nomRole ?? 0,
            //     "idUser" => $user->id,
            // ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function store(Request $request): JsonResponse
    {
        try {

            $user = Auth::User();
            $request->validate([
                "titre" => 'required|string'
            ]);

            if ($request->file('image')) {

                $request->validate([
                    "image" => 'file|mimes:jpeg,png,jpg,svg|max:2000'
                ]);
                $file = $request->file('image');
                $name = time() . $file->getClientOriginalName();
                $filePath = 'backgroundImage/' . $user->nom . 'Images/' . $name;

                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $projet = Projet::create([
                    "user_id" => $user->id,
                    "nom" => $request->titre,
                    "path" => $filePath
                ]);
                $role = RoleProjet::create([
                    "user_id" => $user->id,
                    "projet_id" => $projet->id,
                    "role" => 1
                ]);
                return response()->json([
                    "message" => "succes",
                    "nom" => $projet['nom'],
                    "id" => $projet['id'],
                    "image" => $projet['path'],
                ]);
            }
            $request->validate([
                "image" => 'required|string|max:1'
            ]);
            $backgroudUser = UserBackgroundImagePublique::where('id', $request->image)
                ->first();
            $projet = Projet::create([
                "user_id" => $user->id,
                "nom" => $request->titre,
                "path" => $backgroudUser->chemin
            ]);
            $role = RoleProjet::create([
                "user_id" => $user->id,
                "projet_id" => $projet->id,
                "role" => 'admin'
            ]);
            $url = Storage::temporaryUrl(
                $backgroudUser->chemin,
                now()->addMinutes(1)
            );
            return response()->json([
                "message" => "succes",
                "nom" => $projet['nom'],
                "id" => $projet['id'],
                "urlPrivate" => $url
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {

            if (!Gate::allows('admin-projet', $id)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $projet = Projet::findOrFail($id);
                Broadcast(new RoleEvent($id));
                $projet->delete();
                return response()->json([
                    "message" => "succes"
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function show(int $id)
    {
        $user = auth()->user();
        $projet = RoleProjet::where('projet_id', $id)
            ->where('user_id', $user->id)
            ->first();
        return response()->json([
            "projet" => $projet ?? 0,
        ]);
    }

    public function gestionROle(Request $request, int $projetId)
    {

        try {
            $request->validate([
                "roleUser.*.user_id" => 'required|integer',
                "roleUser.*.role" => ['required', Rule::in(['admin', 'editeur', 'visiteur', 'supprimer'])]
            ]);
            if (!Gate::allows('admin-projet', $projetId)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                foreach ($request->roleUser as $key => $value) {
                    RoleProjet::where("projet_id", $projetId)
                        ->where("user_id", $value['user_id'])
                        ->update([
                            "role" => $value['role'],
                        ]);
                }
                RoleProjet::where("role", 'supprimer')->delete();

                Broadcast(new RoleEvent($projetId));
                return response()->json([
                    "message" => "succes"
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function showAdmin(int $projetId): JsonResponse
    {
        try {
            if (!Gate::allows('admin-projet', $projetId)) {
                return response()->json([
                    "message" => "vous n'avez pas l'autorisation"
                ], 403);
            } else {
                $adminProjet = RoleProjet::with('userRole')
                    ->where('projet_id', $projetId)
                    ->where('user_id', '!=', auth()->user()->id)
                    ->get();
                return response()->json([
                    "message" => "succes",
                    "projetAdmin" => $adminProjet,
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
