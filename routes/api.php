<?php

use App\Models\Projet;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\Api\ProjetController;
use App\Http\Controllers\Api\ProjetInvitation;
use App\Http\Controllers\InscriptionController;

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


Route::post('/test', [ProjetController::class, 'test']);
