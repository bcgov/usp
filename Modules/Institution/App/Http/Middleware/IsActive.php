<?php

namespace Modules\Institution\App\Http\Middleware;

use App\Models\InstitutionStaff;
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

        //active user must have at least an Institution USER role
        if (! $user->hasRole(Role::SUPER_ADMIN)
            && ! $user->hasRole(Role::Institution_ADMIN)
            && ! $user->hasRole(Role::Institution_USER)) {

            if (! $user->hasRole(Role::Institution_GUEST)) {
                $role = Role::where('name', Role::Institution_GUEST)->first();
                $user->roles()->attach($role);
            }
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Please contact Institution Admin to grant you access.',
            ]);
        }

        //prevent login for inactive institutions
        if(! $user->hasActiveInstitution() ){
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Please contact Ministry staff. Your institution is inactive.',
            ]);
        }

        $staff = InstitutionStaff::where('user_guid', $user->guid)->first();
        if($staff->status != 'Active'){

            Auth::logout();

//            return redirect(route('login'))->withErrors(['first_name' => '.']);
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Please contact your Institution Admin to grant you access.',
            ]);

//            return Inertia::render('Home', [
//                'loginAttempt' => true,
//                'hasAccess' => false,
//                'status' => 'Please contact your Institution Admin to grant you access.',
//            ]);
        }


        return $next($request);
    }
}
