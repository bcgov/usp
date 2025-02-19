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
        $issuedUnderAttestations = 0;
        $issuedGradAttestations = 0;
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

            $counts = Attestation::selectRaw("
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = false THEN 1 ELSE 0 END) as issued_undergrad_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = false THEN 1 ELSE 0 END) as declined_undergrad_attestations,
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = true THEN 1 ELSE 0 END) as issued_grad_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = true THEN 1 ELSE 0 END) as declined_grad_attestations
")
                ->leftJoin('programs', 'programs.guid', '=', 'attestations.program_guid')
                ->where('attestations.institution_guid', $institution->guid)
                ->where('attestations.fed_cap_guid', $instCap->fed_cap_guid)
                ->first();
            $issuedUnderAttestations       = $counts->issued_undergrad_attestations;
            $declinedUnderAttestations     = $counts->declined_undergrad_attestations;
            $issuedGradAttestations = $counts->issued_grad_attestations;
            $declinedGradAttestations = $counts->declined_grad_attestations;

            $institutionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedUnderAttestations,
                $issuedGradAttestations, $declinedUnderAttestations, $declinedGradAttestations, $instCap);
        }

        return Inertia::render('Institution::Dashboard', [
            'results' => $institution,
            'capTotal' => $capTotal,
            'resGraduateCapTotal' => $instCap->total_reserved_graduate_attestations ?? 0,
            'issued' => $issuedUnderAttestations ?? 0,
            'declined' => $declinedUnderAttestations ?? 0,
            'undergradRemaining' => $institutionAttestationsDetails['undergradRemaining'] ?? 0,
            'totalRemaining' => $institutionAttestationsDetails['totalRemaining'] ?? 0,
            'issuedGrad' => $issuedGradAttestations ?? 0,
            'declinedGrad' => $declinedGradAttestations ?? 0,
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
