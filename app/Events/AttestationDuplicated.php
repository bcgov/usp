<?php

namespace App\Events;

use App\Models\Attestation;
use App\Models\Cap;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttestationDuplicated
{
    use Dispatchable, SerializesModels;

    public $cap;

    public $attestation;

    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct(Cap $cap, Attestation $attestation, $status)
    {
        $this->cap = $cap;
        $this->attestation = $attestation;
        $this->status = $status;
    }
}
