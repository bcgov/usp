<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Events\StaffRoleChanged;
use App\Facades\InstitutionFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionStaffEditRequest;
use App\Models\Attestation;
use App\Models\Cap;
use App\Models\InstitutionStaff;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $issuedInstAttestations = 0;
        $issuedResGradInstAttestations = 0;
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;

        $instCap = Cap::where('institution_guid', $institution->guid)
            ->selectedFedcap()
            ->active()
            ->where('program_guid', null)
            ->first();

        $capTotal = 0;

        if (! is_null($instCap)) {
            $capTotal = $instCap->total_attestations;
            $issuedInstAttestations = Attestation::where('status', 'Issued')
                ->where('institution_guid', $institution->guid)
                ->where('fed_cap_guid', $instCap->fed_cap_guid)
                ->count();

            // Total for Grad3. attestations
            $issuedResGradInstAttestations = Attestation::where('status', 'Issued')
                ->where('institution_guid', $institution->guid)
                ->where('fed_cap_guid', $instCap->fed_cap_guid)
                ->whereHas('program', function ($query) {
                    $query->where('program_graduate', true);
                })
                ->count();

            $instituionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedInstAttestations, $issuedResGradInstAttestations, $instCap);
        }

        return Inertia::render('Institution::Dashboard', [
            'results' => $institution,
            'capTotal' => $capTotal,
            'resGraduateCapTotal' => $instCap->total_reserved_graduate_attestations ?? 0,
            'issued' => $issuedInstAttestations,
            'issuedUndegrad' => $instituionAttestationsDetails['issuedUndegrad'] ?? 0,
            'undergradRemaining' => $instituionAttestationsDetails['undergradRemaining'] ?? 0,
            'issuedResGrad' => $issuedResGradInstAttestations,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function show(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;

        return Inertia::render('Institution::Institution', ['institution' => $institution]);
    }

    /**
     * Display a listing of the resource.
     */
    public function caps()
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;
        $institution->activeInstCaps->makeHidden(['comment']);
        $institution->activeProgramCaps->makeHidden(['comment']);

        return Inertia::render('Institution::Caps', [
            'results' => $institution,
            'instCaps' => $institution->activeInstCaps,
            'programCaps' => $institution->activeProgramCaps,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function staffList(Request $request): \Inertia\Response
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff()->with('user.roles')->get();

        return Inertia::render('Institution::Staff', [
            'status' => true,
            'results' => $institution,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function staffUpdate(InstitutionStaffEditRequest $request): \Inertia\Response
    {
        InstitutionStaff::where('id', $request->id)
            ->update($request->validated());
        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff;

        return Inertia::render('Institution::Staff', [
            'status' => true,
            'results' => $institution,
        ]);
    }

    /**
     * Update the specified resource role in storage.
     */
    public function staffUpdateRole(Request $request): \Inertia\Response
    {
        $newRole = Role::where('name', Role::Institution_GUEST)->first();
        if ($request->input('role') === 'User') {
            $newRole = Role::where('name', Role::Institution_USER)->first();
        }

        $rolesToCheck = [
            Role::Ministry_ADMIN,
            Role::SUPER_ADMIN,
            Role::Institution_ADMIN,
            Role::Institution_USER,
        ];
        if (Auth::user()
            ->roles()
            ->pluck('name')
            ->intersect($rolesToCheck)
            ->isNotEmpty() && Auth::user()->disabled === false) {
            $staff = InstitutionStaff::where('id', $request->input('id'))
                ->first();

            if (! is_null($staff)) {
                //reset roles
                $roles = Role::whereIn('name', [
                    Role::Institution_ADMIN,
                    Role::Institution_USER,
                    Role::Institution_GUEST,
                ])->get();
                foreach ($roles as $role) {
                    $staff->user->roles()->detach($role);
                }

                $staff->user->roles()->attach($newRole);
                event(new StaffRoleChanged($staff->user, $newRole));
            }
        }

        $user = User::find(Auth::user()->id);
        $institution = $user->institution->staff;

        return Inertia::render('Institution::Staff', [
            'status' => true,
            'results' => $institution,
        ]);
    }
}
