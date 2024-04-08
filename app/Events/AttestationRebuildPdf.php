<?php

namespace App\Events;

use App\Models\Attestation;
use App\Models\Cap;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttestationRebuildPdf
{
    use Dispatchable, SerializesModels;

    public $attestation;

    /**
     * Create a new event instance.
     */
    public function __construct(Attestation $attestation)
    {
        $this->attestation = $attestation;
    }
}
