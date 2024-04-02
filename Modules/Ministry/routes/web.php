<?php

use Illuminate\Support\Facades\Route;
use Modules\Ministry\App\Http\Controllers\AttestationController;
use Modules\Ministry\App\Http\Controllers\CapController;
use Modules\Ministry\App\Http\Controllers\FedCapController;
use Modules\Ministry\App\Http\Controllers\InstitutionController;
use Modules\Ministry\App\Http\Controllers\InstitutionStaffController;
use Modules\Ministry\App\Http\Controllers\MaintenanceController;
use Modules\Ministry\App\Http\Controllers\ProgramController;

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

        Route::put('/programs', [ProgramController::class, 'update'])->name('programs.update');
        Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');

        Route::put('/institution_staff', [InstitutionStaffController::class, 'update'])->name('institution_staff.update');
        Route::put('/institution_roles', [InstitutionStaffController::class, 'updateRole'])->name('institution_roles.updateRole');
        Route::get('/institution_login/{guid}', [InstitutionStaffController::class, 'ministryLogin'])->name('institution_login.login');

        Route::get('/attestations', [AttestationController::class, 'index'])->name('attestations.index');
        Route::post('/attestations/{page?}', [AttestationController::class, 'storeAttestations'])->name('attestations.store');
        Route::put('/attestations/{page?}', [AttestationController::class, 'updateAttestations'])->name('attestations.update');
        Route::get('/attestations/download/{attestation}', [AttestationController::class, 'download'])->name('attestations.download');

        Route::post('/api/fetch/programs/{program?}', [ProgramController::class, 'fetchPrograms'])->name('programs.api.fetch');
        Route::post('/api/fetch/attestations/{institution?}', [InstitutionController::class, 'fetchAttestations'])->name('attestations.api.fetch');
        Route::post('/api/fetch/fedcap_inst', [FedCapController::class, 'fetchFedcapInst'])->name('fedcaps.api.fetch.institution-caps');
        Route::post('/api/fetch/capStats', [CapController::class, 'capStat'])->name('caps.api.fetch.cap-stat');

        Route::group([
            'middleware' => ['ministry_admin'],
        ], function () {
            Route::group([
                'prefix' => 'maintenance',
                'as' => 'maintenance.',
            ], function () {

                Route::get('/staff', [MaintenanceController::class, 'staffList'])->name('staff.list');

                Route::put('/staff/{user}', [MaintenanceController::class, 'updateStatus'])->name('staff.status.update');
                Route::put('/staff/roles/{user}', [MaintenanceController::class, 'updateRole'])->name('staff.roles.update');

                Route::get('/utils', [MaintenanceController::class, 'utilList'])->name('utils.list');
                Route::put('/utils/{util}', [MaintenanceController::class, 'utilUpdate'])->name('utils.update');
                Route::post('/utils', [MaintenanceController::class, 'utilStore'])->name('utils.store');

                Route::get('/faqs', [MaintenanceController::class, 'faqList'])->name('faqs.list');
                Route::put('/faqs/{faq}', [MaintenanceController::class, 'faqUpdate'])->name('faqs.update');
                Route::post('/faqs', [MaintenanceController::class, 'faqStore'])->name('faqs.store');
            });

            Route::get('/reports/summary', [MaintenanceController::class, 'reportsSummary'])->name('reports.summary');
            Route::post('/reports/summary', [MaintenanceController::class, 'reportsSummaryFetch'])->name('reports.summary.fetch');
            Route::get('/reports/detail', [MaintenanceController::class, 'reportsDetail'])->name('reports.detail');
            Route::post('/reports/detail', [MaintenanceController::class, 'reportsDetailFetch'])->name('reports.detail.fetch');

            Route::get('/reports/sources', [MaintenanceController::class, 'reportSources'])->name('reports.sources');
            Route::get('/reports/sources-download/{from}/{to}/{type}', [MaintenanceController::class, 'reportSourcesFetch'])->name('reports.sources.fetch');

        });
    });
});
