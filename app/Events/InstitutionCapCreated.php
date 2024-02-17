<?php

namespace App\Events;

use App\Models\Cap;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstitutionCapCreated
{
    use Dispatchable, SerializesModels;

    public $cap;

    /**
     * Create a new event instance.
     */
    public function __construct(Cap $cap)
    {
        $this->cap = $cap;
    }
}
