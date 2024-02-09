<?php

use Illuminate\Support\Facades\Route;
use Modules\Institution\App\Http\Controllers\AttestationController;
use Modules\Institution\App\Http\Controllers\InstitutionController;

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

Route::prefix('institution')->group(function () {
    Route::group(
        [
            'middleware' => ['auth', 'institution_active'],
            'as' => 'institution.',
        ], function () {
        Route::get('/attestations', [AttestationController::class, 'index'])->name('attestations.index');
        Route::get('/', [InstitutionController::class, 'index'])->name('home');
    });

    Route::group([
        'middleware' => ['institution_admin'],
    ], function () {
        Route::get('/staff', [InstitutionController::class, 'staffList'])->name('staff.list');


        Route::put('/staff', [InstitutionController::class, 'staffUpdate'])->name('staff.staffUpdate');
        Route::put('/roles', [InstitutionController::class, 'staffUpdateRole'])->name('roles.staffUpdateRole');

    });
});
