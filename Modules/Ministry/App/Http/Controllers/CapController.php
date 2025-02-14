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
    SUM(CASE WHEN status = 'Issued' THEN 1 ELSE 0 END) as issued_inst_attestations,
    SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) as declined_inst_attestations,
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = true THEN 1 ELSE 0 END) as issued_res_grad_inst_attestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = true THEN 1 ELSE 0 END) as declined_res_grad_inst_attestations
")
                ->leftJoin('programs', 'programs.guid', '=', 'attestations.program_guid')
                ->where('attestations.institution_guid', $instCap->institution_guid)
                ->where('attestations.fed_cap_guid', $instCap->fed_cap_guid)
                ->first();

            $issuedInstAttestations       = $counts->issued_inst_attestations;
            $declinedInstAttestations     = $counts->declined_inst_attestations;
            $issuedResGradInstAttestations = $counts->issued_res_grad_inst_attestations;
            $declinedResGradInstAttestations = $counts->declined_res_grad_inst_attestations;

            $instituionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedInstAttestations,
                $issuedResGradInstAttestations, $declinedInstAttestations, $declinedResGradInstAttestations, $instCap);

        }

        return Response::json(['status' => true, 'body' =>
            [
                'instCap' => $instCap,
                'issued' => $issuedInstAttestations ?? 0,
                'declined' => $declinedInstAttestations ?? 0,
                'resGradIssued' => $issuedResGradInstAttestations ?? 0,
                'declinedUndegrad' => $instituionAttestationsDetails['declinedUndegrad'] ?? 0,
                'remainingUndergrad' => $instituionAttestationsDetails['undergradRemaining'] ?? 0
            ]], 200);
    }
}
