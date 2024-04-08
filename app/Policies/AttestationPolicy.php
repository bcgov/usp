<?php

namespace App\Policies;

use App\Models\Attestation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttestationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $rolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Institution_USER];

        return $user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()
            && $user->disabled === false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attestation $model): bool
    {
        $rolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Institution_USER];

        return $user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()
            && $user->disabled === false;
    }

    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Attestation $model): bool
    {
        $ministryRolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN];

        if($user->roles()->pluck('name')->intersect($ministryRolesToCheck)->isNotEmpty() && $user->disabled === false){
            return true;
        }

        $rolesToCheck = [Role::Institution_ADMIN, Role::Institution_USER];

        return $user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()
            && $user->disabled === false && $model->institution_guid === $user->institution->guid;
    }

    /**
     * Determine whether the user can rebuild the model.
     */
    public function rebuild(User $user, Attestation $model): bool
    {
        $ministryRolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN];

        if($user->roles()->pluck('name')->intersect($ministryRolesToCheck)->isNotEmpty() && $user->disabled === false){
            return true;
        }

        return false;
    }
}
