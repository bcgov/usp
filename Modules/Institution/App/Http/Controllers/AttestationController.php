<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Events\AttestationDraftUpdated;
use App\Events\AttestationIssued;
use App\Http\Controllers\Controller;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Institution\App\Http\Requests\AttestationEditRequest;
use Modules\Institution\App\Http\Requests\AttestationStoreRequest;
use Response;

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
        // Get the inst cap and check if we have hit the cap for issued attestations
        // This is going to be all attes. under this inst. and are using the same fed cap as this.
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;
        $fedCap = FedCap::active()->first();
        $cap = Cap::where('fed_cap_guid', $fedCap->guid)->active()
            ->where('program_guid', null)
            ->where('institution_guid', $institution->guid)
            ->first();

        $user = User::find(Auth::user()->id);

        $attestations = $this->paginateAtte($user->institution);

        return Inertia::render('Institution::Attestations', ['error' => null, 'results' => $attestations,
            'institution' => $user->institution,
            'programs' => $user->institution->activePrograms, 'countries' => $this->countries,
            'instCaps' => $user->institution->activeInstCaps,
            'programCaps' => $user->institution->activeProgramCaps,
            'instCap' => $cap]);
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
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid, 'email' => $request->email,
            'address1' => $request->address1, 'address2' => $request->address2, 'city' => $request->city,
            'zip_code' => $request->zip_code, 'province' => $request->province, 'country' => $request->country])->first();

        if (is_null($check1)) {
            $attestation = Attestation::create($request->validated());
            $this->authorize('download', $attestation);
            event(new AttestationIssued($attestation->cap, $attestation, $request->status));

        } else {
            $error = "There's already an attestation for the same user.";
        }

        if (! is_null($error)) {
            return redirect(route('institution.attestations.index'))->withErrors(['first_name' => $error]);
        }

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

        //2. dont allow duplicate
        $check2 = Attestation::where([
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid, 'email' => $request->email,
            'address1' => $request->address1, 'address2' => $request->address2, 'city' => $request->city,
            'zip_code' => $request->zip_code, 'province' => $request->province, 'country' => $request->country])
            ->where('id', '!=', $request->id)
            ->first();

        if(is_null($check1)){
            $error = 'This attestation cannot be edited. Only draft attestations can be edited.';
        }
        elseif(!is_null($check2)){
            $error = "There's already an attestation for the exact same user.";
        }else{
            $cap = Cap::where('guid', $request->cap_guid)->first();

            Attestation::where('id', $request->id)->update($request->validated());
            $attestation = Attestation::find($request->id);
            $this->authorize('download', $attestation);
            event(new AttestationDraftUpdated($cap, $attestation, $check1, $request->status));
        }

        if (! is_null($error)) {
            return redirect(route('institution.attestations.index'))->withErrors(['first_name' => $error]);
        }

        return redirect(route('institution.attestations.index'));
    }

    public function download(Request $request, Attestation $attestation)
    {
        $this->authorize('download', $attestation);
        $storedPdf = AttestationPdf::where('attestation_guid', $attestation->guid)->first();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(base64_decode($storedPdf->content));
        $pdf->getCanvas()->get_cpdf()->setEncryption('', env('PDF_KEY'), ['print']);

        return $pdf->download($attestation->last_name.'-'.$attestation->fed_guid.'-attestation.pdf');
    }

    public function exportCsv()
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;

        $data = Attestation::where('institution_guid', $institution->guid)->orderByDesc('created_at')->get();

        $csvData = [];
        $csvDataHeader = ['PAL ID', 'PROGRAM NAME', 'STUDENT NUMBER', 'FIRST NAME', 'LAST NAME', 'TRAVEL ID', 'DOB', 'ADDRESS1', 'ADDRESS2', 'EMAIL', 'CITY',
            'POSTAL CODE', 'PROVINCE', 'COUNTRY', '>50% IN PERSON', 'STATUS', 'EXPIRY DATE', 'ISSUED BY', 'ISSUE DATE'];

        foreach ($data as $d) {
            $csvData[] = [$d->fed_guid, $d->program->program_name, $d->student_number, $d->first_name, $d->last_name, $d->id_number,
                $d->dob, $d->address1, $d->address2, $d->email, $d->city, $d->zip_code, $d->province, $d->country,
                $d->gt_fifty_pct_in_person, $d->status, $d->expiry_date, $d->issued_by_name, $d->updated_at];
        }
        $output = fopen('php://temp', 'w');
        // Write CSV headers
        fputcsv($output, $csvDataHeader);

        // Write CSV rows
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $response = Response::make(stream_get_contents($output), 200);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="attestations_export.csv"');
        fclose($output);

        return $response;

    }

    public function capStat(Request $request)
    {
        $instCap = Cap::where('institution_guid', $request->input('institution_guid'))
            ->active()
            ->where('program_guid', null)
            ->first();

        $issuedInstAttestations = Attestation::where('status', 'Issued')
            ->where('institution_guid', $instCap->institution_guid)
            ->where('fed_cap_guid', $instCap->fed_cap_guid)
            ->count();

        return Response::json(['status' => true, 'body' => ['instCap' => $instCap, 'issued' => $issuedInstAttestations]]);
    }


    public function duplicateStudent(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;

        $instCap = Cap::where('institution_guid', $institution->guid)
            ->active()
            ->where('program_guid', null)
            ->first();

        $issuedInstAttestations = Attestation::where('institution_guid', $instCap->institution_guid)
            ->where('fed_cap_guid', $instCap->fed_cap_guid)
            ->where('student_number', $request->input('student_number'))
            ->count();

        return Response::json(['status' => true, 'body' => ['count' => $issuedInstAttestations]]);
    }

    private function paginateAtte($institution)
    {

        $attestations = Attestation::where('institution_guid', $institution->guid)->with('program');

        if (request()->filter_term !== null && request()->filter_type !== null) {
            $attestations = match (request()->filter_type) {
                'snumber' => $attestations->where('student_number', 'ILIKE', '%'.request()->filter_term.'%'),
                'fname' => $attestations->where('first_name', 'ILIKE', '%'.request()->filter_term.'%'),
                'lname' => $attestations->where('last_name', 'ILIKE', '%'.request()->filter_term.'%'),
                'travel_id' => $attestations->where('id_number', 'ILIKE', '%'.request()->filter_term.'%'),
                'pal_id' => $attestations->where('fed_guid', 'ILIKE', '%'.request()->filter_term.'%'),
                'city' => $attestations->where('city', 'ILIKE', '%'.request()->filter_term.'%'),
                'country' => $attestations->where('country', 'ILIKE', '%'.request()->filter_term.'%'),
            };
        }

        if (request()->sort !== null) {
            $attestations = $attestations->orderBy(request()->sort, request()->direction);
        } else {
            $attestations = $attestations->orderBy('created_at', 'desc');
        }

        return $attestations->with('institution.activeCaps', 'institution.programs')->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
