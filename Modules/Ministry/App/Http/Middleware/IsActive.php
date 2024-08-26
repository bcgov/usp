<?php

namespace Modules\Ministry\App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$roles
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $roles = empty($roles) ? [null] : $roles;

        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        //if the user is disabled or has neither idir/bceid
        if ($user->disabled || (is_null($user->idir_user_guid) && is_null($user->bceid_user_guid))) {
            Auth::logout();

            return redirect()->route('login');
        }

        //active user must have at least a Ministry User role
        if ($user->roles->isEmpty()) {
            $role = Role::where('name', Role::Ministry_GUEST)->first();
            $user->roles()->attach($role);

            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Please contact Ministry Admin to grant you access.',
            ]);
        }
        if (! $user->hasRole(Role::SUPER_ADMIN) && ! $user->hasRole(Role::Ministry_ADMIN) && ! $user->hasRole(Role::Ministry_USER)) {
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Please contact Ministry Admin to verify your access.',
            ]);
        }

        return $next($request);
    }
}
