<?php

namespace App\Listeners;

use App\Events\InstitutionCapCreated;
use App\Events\TrackerTriggered;
use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Institution;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;

class AdjustInstitutionCap
{
    /**
     * Handle the event.
     */
    public function handle(InstitutionCapCreated $event): void
    {
        // Get the new cap from the event
        $cap = $event->cap;
        $institution = Institution::where('guid', $cap->institution_guid)->first();

        \Log::info('Cap Listeners started');
        // Get the federal cap and check if we have hit the cap for issued attestations
        $issuedAttestations = Attestation::where('status', 'Issued')
            ->where('institution_guid', $institution->guid)
            ->where('fed_cap_guid', $cap->fed_cap_guid)
            ->count();
        \Log::info('0 $issuedAttestations: '.$issuedAttestations);

        // If we hit or acceded the fed cap limit for issued attestations
        if ($issuedAttestations >= $cap->fedCap->total_attestations) {
            \Log::info('1 $issuedAttestations >= $cap->fedCap->total_attestations: '.$issuedAttestations.'>='.$cap->fedCap->total_attestations);
            $cap->total_attestations = 0;
        }

        // The fed cap is not reached
        else {
            // How much of the fed cap is left
            $remainderFedCap = $cap->fedCap->total_attestations - $issuedAttestations;
            \Log::info('2 $remainderFedCap: '.$remainderFedCap);

            // If the new cap is gt $remainderFedCap then set it to match
            if ($cap->total_attestations >= $remainderFedCap) {
                \Log::info('3.0 $cap->total_attestations >= $remainderFedCap: '.$cap->total_attestations.'>='.$remainderFedCap);
                \Log::info('3.1 $cap->total_attestations >= $remainderFedCap: $cap->total_attestations='.$cap->total_attestations);
                $cap->total_attestations = $remainderFedCap;
            }
        }

        // Now let's check if it's a program cap
        if (! is_null($cap->program_guid)) {
            $program = Program::where('guid', $cap->program_guid)->first();
            \Log::info('4 !is_null($cap->program_guid): '.$remainderFedCap);

            // If either the program or institution status is false then decline
            if (! $institution->active_status || ! $program->active_status) {
                \Log::info('5 !$institution->active_status || !$program->active_status: '.$institution->active_status.' || '.$program->active_status);
                $cap->total_attestations = 0;
            } else {
                $instCap = Cap::where('institution_guid', $institution->guid)
                    ->active()
                    ->where('program_guid', null)
                    ->where('fed_cap_guid', $cap->fed_cap_guid)
                    ->first();

                // If the new cap is gt the institution active cap
                if ($cap->total_attestations >= $instCap->total_attestations) {
                    \Log::info('6 $cap->total_attestations >= $instCap->total_attestations: '.$cap->total_attestations.'>='.$instCap->total_attestations);
                    $cap->total_attestations = $instCap->total_attestations;
                }

                // Get the inst cap and check if we have hit the cap for issued attestations
                // This is going to be all attes. under this inst. and are using the same fed cap as this.
                $issuedAttestations = Attestation::where('status', 'Issued')
                    ->where('institution_guid', $institution->guid)
                    ->where('fed_cap_guid', $cap->fed_cap_guid)
                    ->count();

                // If we hit or acceded the inst cap limit for issued attestations
                if ($issuedAttestations >= $instCap->total_attestations) {
                    \Log::info('7 $issuedAttestations >= $instCap->total_attestations: '.$issuedAttestations.' >= '.$instCap->total_attestations);
                    $cap->total_attestations = 0;
                } else {

                    // How much of the institution cap is left
                    $remainderInstCap = $instCap->total_attestations - $issuedAttestations;

                    // If the new cap is gt $remainderInstCap then set it to match
                    if ($cap->total_attestations >= $remainderInstCap) {
                        \Log::info('8 $cap->total_attestations >= $remainderInstCap: '.$cap->total_attestations.' >= '.$remainderInstCap);
                        $cap->total_attestations = $remainderInstCap;
                    }
                }
            }
        }

        $cap->save();

        // If the cap is institution level, check any program caps and update the limit to be the same
        if (is_null($cap->program_guid)) {
            \Log::info('9 is_null($cap->program_guid): '.$cap->total_attestations);
            // No program limit can have more attestations available to it than the institution cap
            Cap::where('institution_guid', $institution->guid)
                ->active()
                ->where('program_guid', '!=', null)
                ->where('fed_cap_guid', $cap->fed_cap_guid)
                ->where('total_attestations', '>', $cap->total_attestations)
                ->update(['total_attestations' => $cap->total_attestations]);
        }

        event(new TrackerTriggered(Auth::user()->guid, Auth::user()->first_name, 'created',
            'Cap', $cap));

    }
}
