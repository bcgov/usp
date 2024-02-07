<?php

namespace App\Policies;

use App\Models\Attestation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttestationPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        $rolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Institution_USER];
        return $user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()
            && $user->disabled === false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attestation $model): bool
    {
        return false;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function download(User $user, Attestation $model): bool
    {
        return false;
    }

}
