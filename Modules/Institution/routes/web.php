<?php

use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;
use Modules\Institution\App\Http\Controllers\AttestationController;
use Modules\Institution\App\Http\Controllers\InstitutionController;
use Modules\Institution\App\Http\Controllers\MaintenanceController;
use Modules\Ministry\App\Http\Controllers\FedCapController;

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
            Route::get('/', [AttestationController::class, 'index'])->name('home');
            Route::get('/attestations', [AttestationController::class, 'index'])->name('attestations.index');
            Route::post('/attestations', [AttestationController::class, 'store'])->name('attestations.store');
            Route::put('/attestations', [AttestationController::class, 'update'])->name('attestations.update');
            Route::get('/attestations/download/{attestation}', [AttestationController::class, 'download'])->name('attestations.download');
            Route::get('/attestations/export', [AttestationController::class, 'exportCsv'])->name('attestations.export');
        Route::post('/duplicate_attestations', [AttestationController::class, 'duplicate'])->name('attestations.duplicate');

            Route::get('/dashboard', [InstitutionController::class, 'index'])->name('dashboard');
            Route::get('/account', [InstitutionController::class, 'show'])->name('show');

            Route::get('/caps', [InstitutionController::class, 'caps'])->name('caps.index');
        Route::post('/api/fetch/capStats', [AttestationController::class, 'capStat'])->name('caps.api.fetch.cap-stat');
        Route::post('/api/check/duplicate_student', [AttestationController::class, 'duplicateStudent'])->name('caps.api.check.duplicate-student');

        Route::get('/faqs', [MaintenanceController::class, 'faqList'])->name('faqs.index');

        Route::post('/fed_caps/default', [FedCapController::class, 'setDefault'])->name('fed_caps.set-default');

    });

    Route::group([
        'middleware' => ['institution_admin'],
    ], function () {
        Route::get('/staff', [InstitutionController::class, 'staffList'])->name('staff.list');

        Route::put('/staff', [InstitutionController::class, 'staffUpdate'])->name('staff.staffUpdate');
        Route::put('/roles', [InstitutionController::class, 'staffUpdateRole'])->name('roles.staffUpdateRole');

    });
});
