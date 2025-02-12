<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Events\AttestationDraftUpdated;
use App\Events\AttestationIssued;
use App\Facades\InstitutionFacade;
use App\Http\Controllers\Controller;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\User;
use App\Services\Institution\InstitutionAttestationsDetails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Modules\Institution\App\Http\Requests\AttestationDuplicateRequest;
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
//        $this->fedCaps = FedCap::active()->get();
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

        // Use cache for fetching the default fed_cap_guid
        $default_fed_cap = Cache::get('global_fed_caps_' . $user->id)['default'];

        $cap = Cap::where([
            ['fed_cap_guid', $default_fed_cap],
            ['institution_guid', $institution->guid],
            ['program_guid', null]
        ])->active()->first();

        if (is_null($cap)) {
            return redirect(route('institution.dashboard'))->withErrors(['error' => "Error: No institution cap found for this institution, attestations can't be retrieved."]);
        }

        $attestations = $this->paginateAtte($institution);

        // Display a warning message to users if the institution has reached a cap.
        $warning_message = '';

        // Get the inst cap and check if we have hit the cap for issued attestations
        // This is going to be all attes. under this inst. and are using the same fed cap as this.

        $counts = Attestation::selectRaw("
    SUM(CASE WHEN status = 'Issued' THEN 1 ELSE 0 END) as issuedinstattestations,
    SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) as declinedinstattestations,
    SUM(CASE WHEN status = 'Issued' AND programs.program_graduate = true THEN 1 ELSE 0 END) as issuedresgradinstattestations,
    SUM(CASE WHEN status = 'Declined' AND programs.program_graduate = true THEN 1 ELSE 0 END) as declinedresgradinstattestations
")
            ->leftJoin('programs', 'programs.guid', '=', 'attestations.program_guid')
            ->where('attestations.institution_guid', $cap->institution_guid)
            ->where('attestations.fed_cap_guid', $cap->fed_cap_guid)
            ->first();

        $issuedInstAttestations       = $counts->issuedinstattestations;
        $declinedInstAttestations     = $counts->declinedinstattestations;
        $issuedResGradInstAttestations = $counts->issuedresgradinstattestations;
        $declinedResGradInstAttestations = $counts->declinedresgradinstattestations;

        $instituionAttestationsDetails = InstitutionFacade::getInstitutionAttestInfo($issuedInstAttestations,
            $issuedResGradInstAttestations, $declinedInstAttestations, $declinedResGradInstAttestations, $cap);


        // If we hit or acceded the reserved graduate inst cap limit for issued attestations
        if ($instituionAttestationsDetails['undergradRemaining'] === 0) {
            $warning_message = "Your institution has reached the limit for undergraduate attestations. You can't issue a new attestation linked to an undergraduate program or it will be automatically converted as a Draft.";
        }
        elseif ($instituionAttestationsDetails['issued'] >= $cap->total_attestations) {
            $warning_message = "Your institution has reached the maximum attestations cap. You can't issue a new attestation or it will be automatically converted as a Draft.";
        }

        // Fetch countries with caching
        $this->countries = Cache::remember('active_countries', now()->addHours(10), function () {
            return Country::select('name')->where('active', true)->get();
        });

        return Inertia::render('Institution::Attestations', [
            'error' => null,
            'warning' => $warning_message,
            'results' => $attestations,
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
            'fed_cap_guid' => Cache::get('global_fed_caps_' . Auth::id())['default'],
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid, 'email' => $request->email,
            'address1' => $request->address1, 'address2' => $request->address2, 'city' => $request->city,
            'zip_code' => $request->zip_code, 'province' => $request->province, 'country' => $request->country])
            ->whereNot('status', 'Cancelled Draft')
            ->first();

        if (is_null($check1)) {
            $attestation = Attestation::create($request->validated());
            $this->authorize('download', $attestation);
            event(new AttestationIssued($attestation->cap, $attestation, $request->status));
        } else {
            $error = "This user has already been issued an attestation.";
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
            'fed_cap_guid' => Cache::get('global_fed_caps_' . Auth::id())['default'],
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid, 'email' => $request->email,
            'address1' => $request->address1, 'address2' => $request->address2, 'city' => $request->city,
            'zip_code' => $request->zip_code, 'province' => $request->province, 'country' => $request->country])
            ->where('id', '!=', $request->id)
            ->whereNot('status', 'Cancelled Draft')
            ->first();

        if (is_null($check1)) {
            $error = 'This attestation cannot be edited. Only draft attestations can be edited.';
        } elseif (! is_null($check2)) {
            $error = "This user has already been issued an attestation.";
        } else {
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

    /**
     * Store a duplicate resource in storage.
     */
    public function duplicate(AttestationDuplicateRequest $request): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $attestation = Attestation::create($request->validated());
        $this->authorize('download', $attestation);
        event(new AttestationIssued($attestation->cap, $attestation, $request->status));

        return redirect(route('institution.attestations.index'));
    }

    public function download(Request $request, Attestation $attestation)
    {
        $this->authorize('download', $attestation);
        $storedPdf = AttestationPdf::where('attestation_guid', $attestation->guid)->first();

        $pdf = App::make('dompdf.wrapper');

        //combining steps here cause pdf to be sometime generated with missing bytes
        $loadHTML = base64_decode($storedPdf->content);
        $trimHTML = trim($loadHTML);
        $pdf->loadHTML($trimHTML);
        $pdf->render()->getCanvas()->get_cpdf()->setEncryption('', env('PDF_KEY'), ['print']);

        // Clear any output buffers.
        if (ob_get_length()) {
            ob_clean();
        }

        // Get the raw PDF output.
        $pdfContent = $pdf->output();

        $filename = $attestation->last_name . '-' . $attestation->fed_guid . '-attestation.pdf';
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Length' => strlen($pdfContent)
        ]);
    }

    public function exportCsv()
    {
        //$user = User::find(Auth::id());
        $institution = Auth::user()->institution;

        $data = Attestation::with('program')
            ->where('institution_guid', $institution->guid)
            ->where('fed_cap_guid', Cache::get('global_fed_caps_' . Auth::id())['default'])
            ->whereNot('status', 'Cancelled Draft')
            ->orderByDesc('created_at')->get();

        $csvData = [];
        $csvDataHeader = ['PAL ID', 'PROGRAM NAME', 'STUDENT NUMBER', 'FIRST NAME', 'LAST NAME', 'TRAVEL ID', 'DOB', 'ADDRESS1', 'ADDRESS2', 'EMAIL', 'CITY',
            'POSTAL CODE', 'PROVINCE', 'COUNTRY', '>50% IN PERSON', 'STATUS', 'GRADUATE', 'EXPIRY DATE', 'ISSUED BY', 'ISSUE DATE'];

        foreach ($data as $d) {
            $csvData[] = [$d->fed_guid, $d->program->program_name, $d->student_number, $d->first_name, $d->last_name, $d->id_number,
                $d->dob, $d->address1, $d->address2, $d->email, $d->city, $d->zip_code, $d->province, $d->country,
                $d->gt_fifty_pct_in_person, $d->status, $d->program->program_graduate == true ? 'TRUE' : 'FALSE', $d->expiry_date, $d->issued_by_name, $d->updated_at];
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
            ->selectedFedcap()
            ->active()
            ->where('program_guid', null)
            ->first();

        if (! is_null($instCap)) {
            $issuedInstAttestations = Attestation::whereIn('status', ['Issued', 'Declined'])
                ->where('institution_guid', $instCap->institution_guid)
                ->where('fed_cap_guid', $instCap->fed_cap_guid)
                ->count();

            $issuedResGradInstAttestations = Attestation::whereIn('status', ['Issued', 'Declined'])
                ->where('institution_guid', $instCap->institution_guid)
                ->where('fed_cap_guid', $instCap->fed_cap_guid)
                ->whereHas('program', function ($query) {
                    $query->where('program_graduate', true);
                })
                ->count();
        }

        return Response::json(['status' => true, 'body' => ['instCap' => $instCap, 'issued' => $issuedInstAttestations ?? 0, 'resGradIssued' => $issuedResGradInstAttestations ?? 0]]);
    }

    public function duplicateStudent(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;

        $instCap = Cap::where('institution_guid', $institution->guid)
            ->selectedFedcap()
            ->active()
            ->where('program_guid', null)
            ->first();

        if (! is_null($instCap)) {
            $issuedInstAttestations = Attestation::where('institution_guid', $instCap->institution_guid)
                ->where('fed_cap_guid', $instCap->fed_cap_guid)
                ->where('student_number', $request->input('student_number'))
                ->whereNot('status', 'Cancelled Draft')
                ->count();
        }

        return Response::json(['status' => true, 'body' => ['count' => $issuedInstAttestations ?? 0]]);
    }

    private function paginateAtte($institution)
    {
        $default_fed_cap = Cache::get('global_fed_caps_' . Auth::id())['default'];
        $attestations = Attestation::where('institution_guid', $institution->guid)
            ->where('fed_cap_guid', $default_fed_cap)
            ->whereNot('status', 'Cancelled Draft')
            ->with([
                'program:guid,program_name,program_graduate',  // Eager load only needed fields
                'institution:id,guid,name',
            ]);
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

        if (request()->filter_program) {
            $attestations->whereHas('program', function ($query) {
                $query->where('program_graduate', request()->filter_program === 'graduate');
            });
        }

        return $attestations->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
