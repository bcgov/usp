<?php

namespace App\Providers;

use App\Events\AttestationDraftUpdated;
use App\Events\AttestationIssued;
use App\Events\AttestationRebuildPdf;
use App\Events\FederalCapCreated;
use App\Events\InstitutionCapCreated;
use App\Events\StaffRoleChanged;
use App\Events\TrackerTriggered;
use App\Listeners\AdjustInstitutionCap;
use App\Listeners\RebuildPdfAttestation;
use App\Listeners\SendActiveRoleNotification;
use App\Listeners\SetupNewFederalCap;
use App\Listeners\StoreTracker;
use App\Listeners\VerifyIssuedAttestation;
use App\Listeners\VerifyUpdatedAttestation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        StaffRoleChanged::class => [
            SendActiveRoleNotification::class,
        ],
        InstitutionCapCreated::class => [
            AdjustInstitutionCap::class,
        ],
        AttestationIssued::class => [
            VerifyIssuedAttestation::class,
        ],
        AttestationDraftUpdated::class => [
            VerifyUpdatedAttestation::class,
        ],
        FederalCapCreated::class => [
            SetupNewFederalCap::class,
        ],
        TrackerTriggered::class => [
            StoreTracker::class,
        ],
        AttestationRebuildPdf::class => [
            RebuildPdfAttestation::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
