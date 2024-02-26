<?php

namespace App\Listeners;

use App\Events\AttestationDraftUpdated;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Util;
use Illuminate\Support\Str;

class VerifyUpdatedAttestation
{
    /**
     * Handle the event.
     */
    public function handle(AttestationDraftUpdated $event): void
    {
        // Get the cap, attestation and status from the event
        $cap = $event->cap;
        $attestation = $event->attestation;
        $status = $event->status;
        $oldAttestation = $event->oldAttestation;

        //do not restrict creating draft attestations

        $instCap = Cap::where('institution_guid', $cap->institution_guid)
            ->active()
            ->where('program_guid', null)
            ->where('fed_cap_guid', $cap->fed_cap_guid)
            ->first();

        $valid = true;
        if ($status == 'Issued') {

            //check if the inst cap has been reached
            //if so then switch it to draft

            // Get the inst cap and check if we have hit the cap for issued attestations
            // This is going to be all attes. under this inst. and are using the same fed cap as this.
            $issuedInstAttestations = Attestation::where('status', 'Issued')
                ->where('institution_guid', $cap->institution_guid)
                ->where('fed_cap_guid', $cap->fed_cap_guid)
                ->count();

            // If we hit or acceded the inst cap limit for issued attestations
            if($issuedInstAttestations > $instCap->total_attestations){
                \Log::info('1 $issuedAttestations >= $instCap->total_attestations: ' . $issuedInstAttestations . ' >= ' . $instCap->total_attestations);
                $valid = false;
            }

            //check if the program cap has been reached
            //if so switch it to draft
            $issuedProgAttestations = Attestation::where('status', 'Issued')
                ->where('cap_guid', $cap->guid)
                ->count();

            // If we hit or acceded the inst cap limit for issued attestations
            if($issuedProgAttestations > $cap->total_attestations){
                \Log::info('2 $issuedProgAttestations >= $instCap->total_attestations: ' . $issuedProgAttestations . ' >= ' . $instCap->total_attestations);
                $valid = false;
            }

            if($attestation->gt_fifty_pct_in_person == false){
                $valid = false;
            }

            if($valid){
                $this->storePdf($attestation->id);
                $cap->draft_attestations -= 1;
                $cap->issued_attestations += 1;
            }else{
                $attestation->status = 'Draft';
                $attestation->save();
            }
        }else{
            $attestation->status = 'Draft';
            $attestation->save();
        }

        $cap->save();

        //if selected a different inst or program
        //then restore count for old cap
        if ($oldAttestation->cap_guid != $attestation->cap_guid) {
            $capCap = Cap::where('guid', $oldAttestation->cap_guid)->first();
            $capCap->draft_attestations -= 1;
            $capCap->save();
        }

    }

    private function storePdf($atteId)
    {
        $attestation = Attestation::where('id', $atteId)
            ->with('institution', 'program')
            ->where('status', '!=', 'Draft')->first();

        $now_d = date('Y-m-d');
        $now_t = date('H:m:i');
        $utils = Util::getSortedUtils();
        $draft = env('APP_ENV') !== 'production';

        $html = view('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils', 'draft'))->render();
        $pdfContent = base64_encode($html);
        AttestationPdf::create(['guid' => Str::orderedUuid()->getHex(),
            'attestation_guid' => $attestation->guid,
            'content' => $pdfContent]);

        return true;
    }
}
