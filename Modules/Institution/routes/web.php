<?php

use Illuminate\Support\Facades\Route;
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
        Route::get('/', [InstitutionController::class, 'index'])->name('home');
    });

    Route::group([
        'middleware' => ['institution_admin'],
        'prefix' => 'maintenance',
        'as' => 'maintenance.',
    ], function () {
        Route::get('/staff', [MaintenanceController::class, 'staffList'])->name('staff.list');
        Route::get('/staff/{user}', [MaintenanceController::class, 'staffShow'])->name('staff.show');
        Route::post('/staff/{user}', [MaintenanceController::class, 'staffEdit'])->name('staff.edit');
    });
});
