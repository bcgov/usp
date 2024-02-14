<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionStaffEditRequest;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\InstitutionStaff;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InstitutionStaffController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionStaffEditRequest $request): \Inertia\Response
    {
        InstitutionStaff::where('id', $request->id)->update($request->validated());

        $institution = Institution::where('guid', $request->institution_guid)->with(['caps', 'staff.user.roles'])->first();
        $fedCaps = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'staff', 'results' => $institution,
            'fedCaps' => $fedCaps]);
    }

    /**
     * Update the specified resource role in storage.
     */
    public function updateRole(Request $request): \Inertia\Response
    {
        $newRole = Role::where('name', Role::Institution_GUEST)->first();
        if ($request->input('role') === 'Admin') {
            $newRole = Role::where('name', Role::Institution_ADMIN)->first();
        }
        if ($request->input('role') === 'User') {
            $newRole = Role::where('name', Role::Institution_USER)->first();
        }

        $rolesToCheck = [Role::Ministry_ADMIN, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Ministry_USER];
        if (Auth::user()->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty() && Auth::user()->disabled === false) {
            $staff = InstitutionStaff::where('id', $request->input('id'))->first();

            if (! is_null($staff)) {
                //reset roles
                $roles = Role::whereIn('name', [Role::Institution_ADMIN, Role::Institution_USER, Role::Institution_GUEST])->get();
                foreach ($roles as $role) {
                    $staff->user->roles()->detach($role);
                }

                $staff->user->roles()->attach($newRole);
            }
        }

        $institution = Institution::where('guid', $request->input('institution_guid'))->with(['caps', 'staff.user.roles'])->first();
        $fedCaps = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'staff', 'results' => $institution,
            'fedCaps' => $fedCaps]);

    }
}
