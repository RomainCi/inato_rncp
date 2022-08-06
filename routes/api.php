<?php

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


Route::group(['prefix' => 'projet', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [ProjetController::class, 'index']);
    Route::post('', [ProjetController::class, 'store']);
    Route::delete('/{id}', [ProjetController::class, 'destroy']);
    Route::post('/{projetId}/role', [ProjetController::class, 'gestionRole']);
    Route::get('/test', [ProjetController::class, 'test']);
    Route::get('/{projetId}/admin', [ProjetController::class, 'showAdmin']);
    Route::get('/{id}', [ProjetController::class, 'show']);
    Route::get('/publicUrl/backgroundImage', [ProjetController::class, 'indexBackgroundImage']);
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [UserApiController::class, 'index']);
    Route::put('', [UserApiController::class, 'update']);
    Route::put('/password', [UserApiController::class, 'updatePassword']);
});

Route::group(['prefix' => 'invitation', 'middleware' => ['auth:sanctum']], function () {
    Route::post('', [ProjetInvitation::class, 'store']);
    Route::get('', [ProjetInvitation::class, 'index']);
    Route::put('/{id}', [ProjetInvitation::class, 'update']);
});

Route::group(['prefix' => 'notifications', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [NotificationController::class, 'index']);
    Route::put('', [NotificationController::class, 'update']);
});

Route::group(['prefix' => 'listes', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/{id}', [ListesController::class, 'show']);
    Route::post('/{id}', [ListesController::class, 'store']);
});

Route::group(['prefix' => 'taches', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/{id}', [TachesController::class, 'show']);
    Route::post('/{id}', [TachesController::class, 'store']);
    Route::put('position/{id}', [TachesController::class, 'updatePosition']);
    Route::put('titre/{id}', [TachesController::class, 'update']);
    Route::delete('/{id}', [TachesController::class, 'destroy']);
});
Route::post('/test', [ProjetController::class, 'test']);
