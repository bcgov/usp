<?php

namespace App\Listeners;

use App\Events\AttestationRebuildPdf;
use App\Events\TrackerTriggered;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Util;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RebuildPdfAttestation
{
    /**
     * Handle the event.
     */
    public function handle(AttestationRebuildPdf $event): void
    {
        // Get the cap, attestation and status from the event
        $attestation = $event->attestation;

        AttestationPdf::where(['attestation_guid' => $attestation->guid,])->delete();
        $this->storePdf($attestation->id);

        event(new TrackerTriggered(Auth::user()->guid, Auth::user()->first_name, 'rebuild pdf',
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

        $html = view('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils', 'draft'))->render();
        $pdfContent = base64_encode($html);
        AttestationPdf::create(['guid' => Str::orderedUuid()->getHex(),
            'attestation_guid' => $attestation->guid,
            'content' => $pdfContent]);

        return true;
    }
}
