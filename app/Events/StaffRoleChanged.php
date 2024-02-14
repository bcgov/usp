<?php

namespace App\Events;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffRoleChanged
{
    use Dispatchable, SerializesModels;

    public $user;
    public $newRole;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Role $newRole)
    {
        $this->user = $user;
        $this->newRole = $newRole;
    }

}
