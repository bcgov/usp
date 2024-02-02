<?php

use Illuminate\Support\Facades\Route;
use Modules\Ministry\App\Http\Controllers\CapController;
use Modules\Ministry\App\Http\Controllers\FedCapController;
use Modules\Ministry\App\Http\Controllers\InstitutionController;

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

Route::prefix('ministry')->group(function () {
    Route::group(
        [
            'middleware' => ['auth', 'ministry_active'],
            'as' => 'ministry.',
        ], function () {
        Route::get('/', [InstitutionController::class, 'index'])->name('home');
        Route::get('/institutions', [InstitutionController::class, 'index'])->name('institutions.index');
        Route::get('/institutions/{institution}/{page?}', [InstitutionController::class, 'show'])->name('institutions.show');
        Route::put('/institutions', [InstitutionController::class, 'update'])->name('institutions.update');
        Route::post('/institutions', [InstitutionController::class, 'store'])->name('institutions.store');

        Route::get('/fed_caps', [FedCapController::class, 'index'])->name('fed_caps.index');
        Route::get('/fed_caps/{fedCap}/{page?}', [FedCapController::class, 'show'])->name('fed_caps.show');
        Route::put('/fed_caps', [FedCapController::class, 'update'])->name('fed_caps.update');
        Route::post('/fed_caps', [FedCapController::class, 'store'])->name('fed_caps.store');

        Route::put('/caps', [CapController::class, 'update'])->name('caps.update');
        Route::post('/caps', [CapController::class, 'store'])->name('caps.store');

    });
});
