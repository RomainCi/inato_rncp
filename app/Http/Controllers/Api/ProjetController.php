<?php

namespace App\Http\Controllers\Api;

use App\Models\Projet;
use App\Events\RoleEvent;
use App\Models\RoleProjet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Broadcast;
use App\Models\UserBackgroundImagePublique;
use Symfony\Contracts\Service\Attribute\Required;

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
            // dd($projets);
            foreach ($projets as $key => $value) {
                if (Storage::disk('s3')->exists($value->projetRole->path)) {
                    $projet[$key] = ["urlprivate" => Storage::temporaryUrl(
                        $value->projetRole->path,
                        now()->addMinutes(1)
                    ), "titre" => $value->projetRole->nom, "id" => $value->projetRole->id, "role" => $value->role];
                }
            }
            // dd($projet);
            return response()->json([
                "message" => 'succes',
                "projet" => $projet ?? null,
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function indexBackgroundImage(): JsonResponse
    {
        try {
            $image = UserBackgroundImagePublique::all();
            foreach ($image as $key => $value) {
                if (Storage::disk('s3')->exists($value->chemin)) {
                    $url[$key] = ["url" => Storage::temporaryUrl(
                        $value->chemin,
                        now()->addMinutes(1)
                    ), "id" => $value->id];
                }
            }
            return response()->json([
                "message" => "succes",
                "public" => $url,
            ]);
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
                    "projet" => $projet,
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
            $projets = RoleProjet::with('userRole')
                ->with('projetRole')
                ->where('user_id', $user->id)
                ->where('projet_id', $projet->id)
                ->first();
            $dataProjet = ["urlprivate" => $url, "titre" => $projets->projetRole->nom, "id" => $projets->projetRole->id, "role" => $projets->role];
            return response()->json([
                "message" => "succes",
                "projet" => $dataProjet
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

    public function show(int $id): JsonResponse
    {
        $user = auth()->user();
        $projet = RoleProjet::where('projet_id', $id)
            ->where('user_id', $user->id)
            ->first();
        return response()->json([
            "projet" => $projet ?? 0,
        ]);
    }

    public function gestionROle(Request $request, int $projetId): JsonResponse
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
                    if ($value['role'] === 'supprimer') {
                        Invitation::where("projet_id", $projetId)
                            ->where('user_id', $value['user_id'])
                            ->update([
                                "status" => "supprimer"
                            ]);
                    }
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
    public function quitterProjet(int $projetId): JsonResponse
    {
        try {
            if (!Gate::allows('acces-lists', $projetId)) {
                return response()->json([
                    "message" => "Vous n'avez pas les acces"
                ]);
            } else {
                $roleProjet = RoleProjet::where('projet_id', $projetId)
                    ->where('user_id', auth()->user()->id)
                    ->first();
                $roleProjet->delete();
                Invitation::where("projet_id", $projetId)
                    ->where('user_id', auth()->user()->id)
                    ->update([
                        "status" => "supprimer"
                    ]);
                Broadcast(new RoleEvent($projetId));
                return response()->json([
                    "message" => "succes"
                ]);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
