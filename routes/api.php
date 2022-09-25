<?php

use App\Http\Controllers\Api\FichierController;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserApiController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\Api\ProjetController;
use App\Http\Controllers\Api\ProjetInvitation;
use App\Http\Controllers\Api\TachesController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ListesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/users', [UserApiController::class, 'store']);

Route::post('/login', [ConnexionController::class, 'authentification']);
Route::post('/forget', [ConnexionController::class, 'forgetPassword']);

Route::group(['prefix' => 'projet', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [ProjetController::class, 'index']);
    Route::post('', [ProjetController::class, 'store']);
    Route::delete('/{id}', [ProjetController::class, 'destroy']);
    Route::post('/{projetId}/role', [ProjetController::class, 'gestionRole']);
    Route::get('/test', [ProjetController::class, 'test']);
    Route::get('/{projetId}/admin', [ProjetController::class, 'showAdmin']);
    Route::get('/{id}', [ProjetController::class, 'show']);
    Route::get('/publicUrl/backgroundImage', [ProjetController::class, 'indexBackgroundImage']);
    Route::post('quitter/{projetId}', [ProjetController::class, 'quitterProjet']);
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [UserApiController::class, 'index']);
    Route::put('', [UserApiController::class, 'update']);
    Route::put('/password', [UserApiController::class, 'updatePassword']);
    Route::put('/email', [UserApiController::class, 'updateEmail']);
    Route::post('/deconnexion', [UserApiController::class, 'deconnexion']);
    Route::delete('/', [UserApiController::class, 'destroy']);
});

Route::group(['prefix' => 'invitation', 'middleware' => ['auth:sanctum']], function () {
    Route::post('', [ProjetInvitation::class, 'store']);
    Route::get('', [ProjetInvitation::class, 'index']);
    Route::put('/{id}', [ProjetInvitation::class, 'update']);
    Route::delete('/{id}', [ProjetInvitation::class, 'destroy']);
});

Route::group(['prefix' => 'notifications', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [NotificationController::class, 'index']);
    Route::put('', [NotificationController::class, 'update']);
});

Route::group(['prefix' => 'listes', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/{id}', [ListesController::class, 'show']);
    Route::post('/{id}', [ListesController::class, 'store']);
    Route::put('titre/{id}', [ListesController::class, 'update']);
    Route::delete('/{id}', [ListesController::class, 'destroy']);
});

Route::group(['prefix' => 'taches', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/{id}', [TachesController::class, 'show']);
    Route::post('/{id}', [TachesController::class, 'store']);
    Route::put('position/{id}', [TachesController::class, 'updatePosition']);
    Route::put('titre/{id}', [TachesController::class, 'update']);
    Route::delete('/{id}', [TachesController::class, 'destroy']);
});

Route::group(['prefix' => 'fichier', 'middleware' => ['auth:sanctum']], function () {
    Route::post('', [FichierController::class, 'store']);
    Route::get('/{id}', [FichierController::class, 'index']);
    Route::get('downloadFichier/{id}', [FichierController::class, 'downloadFichier']);
});
Route::get('verification', function () {
    return response()->json(["message" => true]);
})->middleware(['auth:sanctum']);

Route::post('/test', [ProjetController::class, 'test']);
