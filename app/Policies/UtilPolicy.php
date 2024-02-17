<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Util;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        $rolesToCheck = [Role::Ministry_ADMIN, Role::SUPER_ADMIN];

        return $user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty() && $user->disabled === false;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Util $model): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Util $model): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Util $model): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Util $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Util $model): bool
    {
        //
    }
}
