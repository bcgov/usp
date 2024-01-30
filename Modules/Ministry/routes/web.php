<?php

use Illuminate\Support\Facades\Route;
use Modules\Ministry\App\Http\Controllers\MinistryController;

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

//Route::group([], function () {
//    Route::resource('ministry', MinistryController::class)->names('ministry');
//});

Route::prefix('ministry')->group(function () {
    Route::group(
        [
            'middleware' => ['auth', 'ministry_active'],
            'as' => 'ministry.',
        ], function () {
        Route::get('/', [MinistryController::class, 'index'])->name('home');
    });
});
