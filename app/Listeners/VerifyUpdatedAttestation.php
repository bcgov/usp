<?php

namespace App\Listeners;

use App\Events\AttestationDraftUpdated;
use App\Events\TrackerTriggered;
use App\Facades\InstitutionFacade;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

        event(new TrackerTriggered(Auth::user()->guid, Auth::user()->first_name, 'before_update',
            'Attestation', $oldAttestation));

        // Need to verify if this attestation is linked to a graduate or undergraduate program
        if (!$attestation->relationLoaded('program')) {
            $attestation->load('program');
        }

        $isProgramGraduate = $attestation->program->program_graduate ?? null;

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

            $counts = Attestation::selectRaw("
    SUM(CASE WHEN status = 'Issued' THEN 1 ELSE 0 END) as issued_inst_attestations,
    SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) as declined_inst_attestations,
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = true THEN 1 ELSE 0 END) as issued_res_grad_inst_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = true THEN 1 ELSE 0 END) as declined_res_grad_inst_attestations
")
                ->leftJoin('programs', 'programs.guid', '=', 'attestations.program_guid')
                ->where('attestations.institution_guid', $cap->institution_guid)
                ->where('attestations.fed_cap_guid', $cap->fed_cap_guid)
                ->first();

            $issuedInstAttestations       = $counts->issued_inst_attestations;
            $declinedInstAttestations     = $counts->declined_inst_attestations;
            $issuedResGradInstAttestations = $counts->issued_res_grad_inst_attestations;
            $declinedResGradInstAttestations = $counts->declined_res_grad_inst_attestations;


            // If the attestation is linked to a reserved graduate program
            // Get the inst cap and check if we have hit the cap for reserved graduate issued attestations

            $instituionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedInstAttestations,
                $issuedResGradInstAttestations, $declinedInstAttestations, $declinedResGradInstAttestations, $instCap);

            // If we hit or acceded the inst cap limit for issued attestations
            if ($issuedInstAttestations > $instCap->total_attestations) {
                \Log::info('1 $issuedAttestations >= $instCap->total_attestations: '.$issuedInstAttestations.' >= '.$instCap->total_attestations);
                $valid = false;
            }

            //check if the program cap has been reached
            //if so switch it to draft
            $issuedProgAttestations = Attestation::whereIn('status', ['Issued', 'Declined'])
                ->where('cap_guid', $cap->guid)
                ->count();

            // If we hit or acceded the inst cap limit for issued attestations
            if ($issuedProgAttestations > $cap->total_attestations) {
                \Log::info('2 $issuedProgAttestations >= $instCap->total_attestations: '.$issuedProgAttestations.' >= '.$instCap->total_attestations);
                $valid = false;
            }

            // If we hit or acceded the limit for Undergrad issued attestations
            if (!$isProgramGraduate && ($instituionAttestationsDetails['undergradRemaining']) === -1) {
                \Log::info('3  $instituionAttestationsDetails[\'undergradRemaining\'] === -1');
                $valid = false;
            }

            if ($attestation->gt_fifty_pct_in_person == false) {
                $valid = false;
            }

            if ($valid) {
                $cap->draft_attestations -= 1;
                $cap->issued_attestations += 1;
                // If it's an attestation linked to a Graduate Program
                if ($isProgramGraduate) {
                    $cap->draft_reserved_graduate_attestations -= 1;
                    $cap->issued_reserved_graduate_attestations += 1;
                }
                $attestation->issued_by_user_guid = Auth::user()->guid;
                $attestation->issue_date = Carbon::now()->startOfDay();
                $attestation->save();
                $this->storePdf($attestation->id);
            } else {
                $attestation->status = 'Draft';
                $attestation->save();
            }
        } else {
            $attestation->status = $status;
            $attestation->save();
        }

        $cap->save();

        //if selected a different inst or program
        //then restore count for old cap
        if ($oldAttestation->cap_guid != $attestation->cap_guid) {
            $capCap = Cap::where('guid', $oldAttestation->cap_guid)->first();
            $capCap->draft_attestations -= 1;
            if ($isProgramGraduate) {
                $cap->draft_reserved_graduate_attestations -= 1;
            }
            $capCap->save();
        }

        //validate expiry date and dob
        // Get today's date
        $today = Carbon::now()->startOfDay();

        // Check if the dob is gte than today
        $dob = Carbon::createFromFormat('Y-m-d', $attestation->dob);
        if ($dob->gte($today)) {
            $attestation->dob = '1770-07-07';
        }

        $expiryDate = Carbon::createFromFormat('Y-m-d', $attestation->expiry_date);

        // validate expiry date
        $endDate = Carbon::createFromFormat('Y-m-d', $instCap->end_date);
        $startDate = Carbon::createFromFormat('Y-m-d', $instCap->start_date);
        if ($expiryDate->gt($endDate) || $expiryDate->lt($startDate) || $expiryDate->lt($today)) {
            $attestation->expiry_date = $instCap->end_date;
        }
        $attestation->save();

        event(new TrackerTriggered(Auth::user()->guid, Auth::user()->first_name, 'after_update',
            'Attestation', $attestation));

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

        // Get cap linked to the attestation to extract year
        $cap = $attestation->cap;
        $capStartDate = $cap ? $cap->start_date : null;
        $year = date('Y', strtotime($capStartDate));

        $html = view('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils', 'draft', 'year'))->render();
        $pdfContent = base64_encode($html);
        AttestationPdf::create(['guid' => Str::orderedUuid()->getHex(),
            'attestation_guid' => $attestation->guid,
            'content' => $pdfContent]);

        return true;
    }
}
