<?php

use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [UserApiController::class, 'index']);
    Route::put('', [UserApiController::class, 'update']);
    Route::put('/password', [UserApiController::class, 'updatePassword']);
});
