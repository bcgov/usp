<?php

namespace App\Listeners;

use App\Events\FederalCapCreated;
use App\Models\FedCap;
use Illuminate\Support\Facades\Auth;

class SetupNewFederalCap
{
    /**
     * Handle the event.
     */
    public function handle(FederalCapCreated $event): void
    {
        \Log::info('Federal Cap Listeners started');
        // Get the new cap from the event
        $fedCap = $event->cap;
        FedCap::where('id', '!=', $fedCap->id)
            ->where('status', 'Active')
            ->update(['status' => 'Completed', 'last_touch_by_user_guid' => Auth::user()->guid]);
        $oldFedCaps = FedCap::where('id', '!=', $fedCap->id)->get();
        foreach ($oldFedCaps as $fCap) {
            foreach ($fCap->caps as $instCap) {
                $instCap->active_status = false;
                $instCap->save();
            }
        }
    }
}
