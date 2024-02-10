<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionStaffEditRequest;
use App\Http\Requests\StaffEditRequest;
use App\Models\Institution;
use App\Models\InstitutionStaff;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InstitutionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;
        return Inertia::render('Institution::Dashboard', ['results' => $institution,
            'instCaps' => $institution->activeInstCaps,
            'programCaps' => $institution->activeProgramCaps]);
    }


    /**
     * Display a listing of the resource.
     */
    public function caps()
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;
        return Inertia::render('Institution::Caps', ['results' => $institution,
            'instCaps' => $institution->activeInstCaps,
            'programCaps' => $institution->activeProgramCaps]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function staffList(Request $request): \Inertia\Response
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff;
        return Inertia::render('Institution::Staff', ['status' => true, 'results' => $institution]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function staffUpdate(InstitutionStaffEditRequest $request): \Inertia\Response
    {
        InstitutionStaff::where('id', $request->id)->update($request->validated());
        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff;
        return Inertia::render('Institution::Staff', ['status' => true, 'results' => $institution]);
    }

    /**
     * Update the specified resource role in storage.
     */
    public function staffUpdateRole(Request $request): \Inertia\Response
    {
        $newRole = Role::where('name', Role::Institution_GUEST)->first();
        if($request->input('role') === 'Admin'){
            $newRole = Role::where('name', Role::Institution_ADMIN)->first();
        }
        if($request->input('role') === 'User'){
            $newRole = Role::where('name', Role::Institution_USER)->first();
        }

        $rolesToCheck = [Role::Ministry_ADMIN, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Institution_USER];
        if(Auth::user()->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty() && Auth::user()->disabled === false){
            $staff = InstitutionStaff::where('id', $request->input('id'))->first();

            if(!is_null($staff))
            {
                //reset roles
                $roles = Role::whereIn('name', [Role::Institution_ADMIN, Role::Institution_USER, Role::Institution_GUEST])->get();
                foreach ($roles as $role) {
                    $staff->user->roles()->detach($role);
                }

                $staff->user->roles()->attach($newRole);
            }
        }

        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff;
        return Inertia::render('Institution::Staff', ['status' => true, 'results' => $institution]);
    }
}
