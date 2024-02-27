<?php

namespace App\Listeners;

use App\Events\AttestationIssued;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Util;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VerifyIssuedAttestation
{
    /**
     * Handle the event.
     */
    public function handle(AttestationIssued $event): void
    {
        // Get the cap, attestation and status from the event
        $cap = $event->cap;
        $attestation = $event->attestation;
        $status = $event->status;

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

            if($valid) {
                $this->storePdf($attestation->id);
                $cap->issued_attestations += 1;
            }else{
                $attestation->status = 'Draft';
                $attestation->save();
                $cap->draft_attestations += 1;
            }
        }else{
            $attestation->status = 'Draft';
            $attestation->save();
            $cap->draft_attestations += 1;
        }

        $cap->save();


        //validate expiry date and dob
        // Get today's date
        $today = Carbon::now()->startOfDay();

        // Check if the dob is gte than today
        $dob = Carbon::createFromFormat('Y-m-d', $attestation->dob);
        if ($dob->gte($today)) {
            $attestation->dob = '1770-07-07';
            $attestation->save();
        }

        $expiryDate = Carbon::createFromFormat('Y-m-d', $attestation->expiry_date);

        // validate expiry date
        $endDate = Carbon::createFromFormat('Y-m-d', $instCap->end_date);
        $startDate = Carbon::createFromFormat('Y-m-d', $instCap->start_date);
        if ($expiryDate->gt($endDate) || $expiryDate->lt($startDate) || $expiryDate->lt($today)) {
            $attestation->expiry_date = $instCap->end_date;
            $attestation->save();
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
