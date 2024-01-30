<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/logout', [App\Http\Controllers\UserController::class, 'logout'])->name('get-logout');
Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout'])->name('logout');
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', [App\Http\Controllers\UserController::class, 'login'])->name('login');
Route::get('/applogin', [App\Http\Controllers\UserController::class, 'appLogin'])->name('app-login');
Route::get('/bceid-login', [App\Http\Controllers\UserController::class, 'bceidLogin'])->name('bceid-login');
Route::middleware(['auth'])->get('/home', [App\Http\Controllers\UserController::class, 'home'])->name('home');
