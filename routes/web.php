<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\UserApiController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\InscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('/welcome');



Route::get('/inscription/{token}', [InscriptionController::class, 'verification']);



Route::get('/{vue_capture?}', function () {
    return view('welcome');
});
// Route::get('/{details/vue_capture?}', function () {
//     return view('welcome');
// });

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
