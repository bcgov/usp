<?php

namespace App\Events;

use App\Models\FedCap;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FederalCapCreated
{
    use Dispatchable, SerializesModels;

    public $cap;

    /**
     * Create a new event instance.
     */
    public function __construct(FedCap $cap)
    {
        $this->cap = $cap;
    }
}
