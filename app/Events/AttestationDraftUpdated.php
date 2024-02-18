<?php

namespace App\Events;

use App\Models\Attestation;
use App\Models\Cap;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttestationDraftUpdated
{
    use Dispatchable, SerializesModels;

    public $cap;

    public $attestation;

    public $oldAttestation;

    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct(Cap $cap, Attestation $attestation, Attestation $oldAttestation, $status)
    {
        $this->cap = $cap;
        $this->attestation = $attestation;
        $this->oldAttestation = $oldAttestation;
        $this->status = $status;
    }
}
