<?php

use App\Http\Controllers\FaqController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Modules\Institution\Http\Controllers\InstAtteController;
use Modules\Institution\Http\Controllers\InstController;
use Modules\Institution\Http\Controllers\MaintController;
use Modules\Ministry\Http\Controllers\FedCapController;
use \Modules\Institution\Http\Middleware\InstIsActive;
use \Modules\Institution\Http\Middleware\InstIsAdmin;

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
            'middleware' => [Authenticate::class, InstIsActive::class],
            'as' => 'institution.',
        ], function () {
        Route::get('/', [InstAtteController::class, 'index'])->name('home');
        Route::get('/attestations', [InstAtteController::class, 'index'])->name('attestations.index');
        Route::post('/attestations', [InstAtteController::class, 'store'])->name('attestations.store');
        Route::put('/attestations', [InstAtteController::class, 'update'])->name('attestations.update');
        Route::get('/attestations/download/{attestation}', [InstAtteController::class, 'download'])->name('attestations.download');
        Route::get('/attestations/export', [InstAtteController::class, 'exportCsv'])->name('attestations.export');
        Route::post('/duplicate_attestations', [InstAtteController::class, 'duplicate'])->name('attestations.duplicate');

        Route::get('/dashboard', [InstController::class, 'index'])->name('dashboard');
        Route::get('/account', [InstController::class, 'show'])->name('show');

        Route::get('/caps', [InstController::class, 'caps'])->name('caps.index');
        Route::post('/api/fetch/capStats', [InstAtteController::class, 'capStat'])->name('caps.api.fetch.cap-stat');
        Route::post('/api/check/duplicate_student', [InstAtteController::class, 'duplicateStudent'])->name('caps.api.check.duplicate-student');

        Route::get('/faqs', [MaintController::class, 'faqList'])->name('faqs.index');

        Route::post('/fed_caps/default', [FedCapController::class, 'setDefault'])->name('fed_caps.set-default');

    });

    Route::group([
        'middleware' => [Authenticate::class, InstIsAdmin::class],
    ], function () {
        Route::get('/staff', [InstController::class, 'staffList'])->name('staff.list');

        Route::put('/staff', [InstController::class, 'staffUpdate'])->name('staff.staffUpdate');
        Route::put('/roles', [InstController::class, 'staffUpdateRole'])->name('roles.staffUpdateRole');

    });
});
