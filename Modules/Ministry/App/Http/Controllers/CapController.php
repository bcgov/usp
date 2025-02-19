<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Events\InstitutionCapCreated;
use App\Facades\InstitutionFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\CapEditRequest;
use App\Http\Requests\CapStoreRequest;
use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Response;

class CapController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CapStoreRequest $request): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $conditions = ['active_status' => true, 'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid];

        //add a check to see if there is already a cap associated with this program
        if ($request->has('program_id') && $request->program_id != '') {
            $conditions['program_guid'] = $request->program_guid;
        } else {
            $conditions['program_guid'] = null;
        }

        $check = Cap::where($conditions)->first();

        //if there is no cap, then create new one
        if (is_null($check)) {
            $cap = Cap::create($request->validated());
            event(new InstitutionCapCreated($cap));
        }

        //there is one, then disable it, and create a new one
        else {
            $check->active_status = false;
            $check->save();

            $comment = 'Previous cap Start Date: '.$check->start_date.', End Date: '.$check->end_date.
                ', Total: '.$check->total_attestations.', Issued Attestations: '.$check->issued_attestations;

            $cap = Cap::create($request->validated());
            $cap->comment = is_null($check->comment) ? $cap->comment.'. '.$comment : $check->comment.'. '.$cap->comment.'. '.$comment;
            $cap->save();

            event(new InstitutionCapCreated($cap));

        }

        $institution = Institution::where('id', $request->institution_id)->first();

        return redirect(route('ministry.institutions.show', [$institution->id, 'caps']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CapEditRequest $request): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $conditions = ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'active_status' => true,
            'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid];

        //add a check to see if there is already a cap associated with this program
        if ($request->has('program_guid') && $request->program_guid != '') {
            $conditions['program_guid'] = $request->program_guid;
        } else {
            $conditions['program_guid'] = null;
        }

        $check = Cap::where('id', '!=', $request->id)->where($conditions)->first();
        if (is_null($check)) {
            Cap::where('id', $request->id)->update($request->validated());
            $cap = Cap::find($request->id);
            event(new InstitutionCapCreated($cap));
        }
        $institution = Institution::where('guid', $request->institution_guid)->first();

        return redirect(route('ministry.institutions.show', [$institution->id, 'caps']));
    }

    public function capStat(Request $request)
    {
        $instCap = Cap::where('institution_guid', $request->input('institution_guid'))
            ->selectedFedcap()
            ->active()
            ->where('program_guid', null)
            ->first();

        if(!is_null($instCap)){

            $counts = Attestation::selectRaw("
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = false THEN 1 ELSE 0 END) as issued_undergrad_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = false THEN 1 ELSE 0 END) as declined_undergrad_attestations,
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = true THEN 1 ELSE 0 END) as issued_grad_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = true THEN 1 ELSE 0 END) as declined_grad_attestations
")
                ->leftJoin('programs', 'programs.guid', '=', 'attestations.program_guid')
                ->where('attestations.institution_guid', $instCap->institution_guid)
                ->where('attestations.fed_cap_guid', $instCap->fed_cap_guid)
                ->first();

            $issuedUnderAttestations       = $counts->issued_undergrad_attestations;
            $declinedUnderAttestations     = $counts->declined_undergrad_attestations;
            $issuedGradAttestations = $counts->issued_grad_attestations;
            $declinedGradAttestations = $counts->declined_grad_attestations;

            $institutionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedUnderAttestations,
                $issuedGradAttestations, $declinedUnderAttestations, $declinedGradAttestations, $instCap);

        }

        return Response::json(['status' => true, 'body' =>
            [
                'instCap' => $instCap,
                'issued' => $issuedUnderAttestations ?? 0,
                'declined' => $declinedUnderAttestations ?? 0,
                'issuedGrad' => $issuedGradAttestations ?? 0,
                'declinedGrad' => $declinedGradAttestations ?? 0,
                'remainingUndergrad' => $institutionAttestationsDetails['undergradRemaining'] ?? 0,
                'totalRemaining' => $institutionAttestationsDetails['totalRemaining'] ?? 0
            ]], 200);
    }
}
