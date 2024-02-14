<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttestationEditRequest;
use App\Http\Requests\AttestationStoreRequest;
use App\Http\Requests\InstitutionEditRequest;
use App\Http\Requests\InstitutionStoreRequest;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\Util;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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
        $this->institutions = Institution::whereHas('activeCaps')->active()->with('activeCaps')->get();
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attestations = $this->paginateAtte();
        return Inertia::render('Ministry::Attestations', ['status' => true, 'results' => $attestations,
            'institutions' => $this->institutions, 'countries' => $this->countries
        ]);
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
            return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);

        return redirect(route('ministry.attestations.index'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttestationEditRequest $request): string|null
    {
        $error = null;

        //1. update only draft attestations
        $check1 = Attestation::where('id', $request->id)->where('status', 'Draft')->first();

        //2. check cap has not been reached,
        $check2 = $this->checkCapLimit($request);

        if (!is_null($check1) && $check2) {
            //if the inst or program got updated
            //then restore count for old cap
            if($check1->cap_guid != $request->cap_guid){
                $cap = Cap::where('guid', $check1->cap_guid)->first();
                $cap->draft_attestations -= 1;
                $cap->save();
            }

            if ($request->status == 'Issued') {
                $cap = Cap::where('guid', $request->cap_guid)->first();
                $cap->draft_attestations -= 1;
                $cap->issued_attestations += 1;
                $cap->save();
            } else {
                $cap = Cap::where('guid', $request->cap_guid)->first();
                $cap->draft_attestations += 1;
                $cap->save();
            }

            Attestation::where('id', $request->id)->update($request->validated());

            if($request->status === 'Issued'){
                $this->storePdf($request);
            }
        } else {
            if (is_null($check1)) {
                $error = "This attestation cannot be edited. Only draft attestations can be edited.";
            } else {
                $error = "You cannot issue any more attestations. Cap limit restriction.";
            }
        }

        return $error;
    }

    public function updateAttestations(AttestationEditRequest $request, $page = null): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $error = $this->update($request);
        $att = Attestation::where('id', $request->id)->first();
        if($page === 'institution'){
            if(!is_null($error))
                return redirect(route('ministry.institutions.show', [$att->institution->id, 'attestations']))->withErrors(['first_name' => $error]);

            return redirect(route('ministry.institutions.show', [$att->institution->id, 'attestations']));
        }

        else{
            if(!is_null($error))
                return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);

            return redirect(route('ministry.attestations.index'));
        }
    }

    public function download(Request $request, Attestation $attestation)
    {
        $this->authorize('download', $attestation);
        $storedPdf = AttestationPdf::where('attestation_guid', $attestation->guid)->first();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(base64_decode($storedPdf->content));
        return $pdf->download(mt_rand().'-'.$attestation->guid.'-attestation.pdf');
    }

    private function storePdf($request)
    {
        $attestation = Attestation::where('id', $request->id)
            ->with('institution', 'program')
            ->where('status', '!=', 'Draft')->first();
        $this->authorize('download', $attestation);

        $now_d = date('Y-m-d');
        $now_t = date('H:m:i');
        $utils = Util::getSortedUtils();

        $html = view('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils'))->render();
        $pdfContent = base64_encode($html);
        AttestationPdf::create(['guid' => Str::orderedUuid()->getHex(),
            'attestation_guid' => $attestation->guid,
            'content' => $pdfContent]);
        return true;

    }


    private function paginateAtte()
    {
        $attestations = new Attestation();

        if (request()->filter_name !== null) {
            $attestations = $attestations->where('first_name', 'ILIKE', '%'.request()->filter_name.'%');
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
