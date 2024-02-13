<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cap;
use Modules\Institution\App\Http\Requests\AttestationEditRequest;
use Modules\Institution\App\Http\Requests\AttestationStoreRequest;
use App\Models\Attestation;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\User;
use App\Models\Util;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use PDF;

class AttestationController extends Controller
{
    protected $countries;
    protected $institutions;
    protected $fedCaps;

    public function __construct()
    {
        $this->fedCaps = FedCap::active()->get();
        $this->countries = Country::select('name')->where('active', true)->get();
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);

        $attestations = $this->paginateAtte($user->institution);
        return Inertia::render('Institution::Attestations', ['error' => null, 'results' => $attestations,
            'institution' => $user->institution, 'programs' => $user->institution->programs, 'countries' => $this->countries,
            'instCaps' => $user->institution->activeInstCaps,
            'programCaps' => $user->institution->activeProgramCaps]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttestationStoreRequest $request): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $error = null;
        //1. check for duplicate attestations
        $check1 = Attestation::where([
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid])->first();

        //2. check cap has not been reached
        $check2 = Cap::where('guid', $request->cap_guid)->whereColumn('issued_attestations', '<', 'total_attestations')->first();

        if(is_null($check1) && !is_null($check2)){
            Attestation::create($request->validated());
            $check2->draft_attestations += 1;
            $check2->save();
        }else{
            if(!is_null($check1)){
                $error = "There's already an attestation for the same user.";
            }else{
                $error = "You cannot issue any more attestations. Cap limit restriction.";
            }
        }

        if(!is_null($error))
            return redirect(route('institution.attestations.index'))->withErrors(['first_name' => $error]);

        return redirect(route('institution.attestations.index'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttestationEditRequest $request): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $error = null;

        //1. update only draft attestations
        $check1 = Attestation::where('id', $request->id)->where('status', 'Draft')->first();

        //2. check cap has not been reached
        $check2 = $this->checkCapLimit($request);
//      $check2 = Cap::where('guid', $request->cap_guid)->whereColumn('issued_attestations', '<', 'total_attestations')->first();

        if(!is_null($check1) && $check2){
            //if the inst or program got updated
            //then restore count for old cap
            if($check1->cap_guid != $request->cap_guid){
                $cap = Cap::where('guid', $check1->cap_guid)->first();
                $cap->draft_attestations -= 1;
                $cap->save();
            }


            if($request->status == 'Issued'){
                $cap = Cap::where('guid', $request->cap_guid)->first();
                $cap->draft_attestations -= 1;
                $cap->issued_attestations += 1;
                $cap->save();
            }else{
                $cap = Cap::where('guid', $request->cap_guid)->first();
                $cap->draft_attestations += 1;
                $cap->save();
            }

            Attestation::where('id', $request->id)->update($request->validated());
        }else{
            if(is_null($check1)){
                $error = "This attestation cannot be edited. Only draft attestations can be edited.";
            }else{
                $error = "You cannot issue any more attestations. Cap limit restriction.";
            }
        }

        if(!is_null($error))
            return redirect(route('institution.attestations.index'))->withErrors(['first_name' => $error]);

        return redirect(route('institution.attestations.index'));
    }

    public function download(Request $request, Attestation $attestation)
    {
        $this->authorize('download', $attestation);
        $attestation = Attestation::where('id', $attestation->id)
            ->with('institution', 'program')
            ->where('status', '!=', 'Draft')->first();
        if(is_null($attestation)){
            return false;
        }

        $now_d = date('Y-m-d');
        $now_t = date('H:m:i');
        $utils = Util::getSortedUtils();
        $pdf = PDF::loadView('institution::pdf', compact('attestation', 'now_d', 'now_t', 'utils'));

        return $pdf->download(mt_rand().'-'.$attestation->guid.'-attestation.pdf');
    }


    private function paginateAtte($institution)
    {

        $attestations = Attestation::where('institution_guid', $institution->guid)->with('program');

        if (request()->filter_first_name !== null) {
            $attestations = $attestations->where('first_name', 'ILIKE', '%'.request()->filter_first_name.'%');
        }
        if (request()->filter_last_name !== null) {
            $attestations = $attestations->where('last_name', 'ILIKE', '%'.request()->filter_last_name.'%');
        }

        if (request()->sort !== null) {
            $attestations = $attestations->orderBy(request()->sort, request()->direction);
        } else {
            $attestations = $attestations->orderBy('created_at', 'desc');
        }

        return $attestations->with('institution.activeCaps', 'institution.programs')->paginate(25)->onEachSide(1)->appends(request()->query());
    }


    private function checkCapLimit($request)
    {
        $cap = Cap::where('guid', $request->cap_guid)->first();
        //if it is an inst. cap then it should pass only the check against the inst. cap
        if(is_null($cap->program_guid)){
            $canCreate = $cap->issued_attestations < $cap->total_attestations;

        }

        //if it is a program cap then it should pass the check against the program cap first then another check against the inst. cap
        else{
            $instCap = Cap::where('institution_guid', $request->institution_guid)->active()->where('program_guid', null)->first();
            $canCreate = ($cap->issued_attestations < $cap->total_attestations) &&
                ($instCap->issued_attestations < $instCap->total_attestations);
        }

        return $canCreate;
    }
}
