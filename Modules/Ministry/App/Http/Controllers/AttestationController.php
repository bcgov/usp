<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Events\AttestationDraftUpdated;
use App\Events\AttestationIssued;
use App\Events\AttestationRebuildPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttestationEditRequest;
use App\Http\Requests\AttestationStoreRequest;
use App\Models\Attestation;
use App\Models\AttestationPdf;
use App\Models\Cap;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AttestationController extends Controller {

    protected $countries;

    protected $institutions;

    protected $fedCaps;

    public function __construct() {
        $this->fedCaps = FedCap::active()->get();
        $this->countries = Country::select('name')
            ->where('active', TRUE)
            ->get();
        $this->institutions = Institution::whereHas('activeCaps')
            ->active()
            ->with('activeCaps')
            ->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $attestations = $this->paginateAtte();

        return Inertia::render('Ministry::Attestations', [
            'status' => TRUE,
            'results' => $attestations,
            'institutions' => $this->institutions,
            'countries' => $this->countries
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request): ?array {
        $error = NULL;
        $inst = Institution::where('guid', $request->institution_guid)->first();
        //1. check for duplicate attestations
        $check1 = Attestation::where([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
            'dob' => $request->dob,
            'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid,
            'cap_guid' => $request->cap_guid,
            'email' => $request->email,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'province' => $request->province,
            'country' => $request->country,
        ])->first();

        //2. check cap has not been reached
        if (is_null($check1)) {
            $attestation = Attestation::create($request->validated());
            event(new AttestationIssued($attestation->cap, $attestation, $request->status));
        }
        else {
            $error = "There's already an attestation for the same user.";
        }

        return [$error, $inst];
    }

    public function storeAttestations(AttestationStoreRequest $request, $page = NULL): RedirectResponse|\Illuminate\Routing\Redirector {
        [$error, $inst] = $this->store($request);

        if ($page === 'institution') {
            if (!is_null($error)) {
                return redirect(route('ministry.institutions.show', [
                    $inst->id,
                    'attestations',
                ]))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.institutions.show', [
                $inst->id,
                'attestations',
            ]));
        }
        else {
            if (!is_null($error)) {
                return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.attestations.index'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    private function update(AttestationEditRequest $request): ?string {
        $error = NULL;

        //1. update only draft attestations
        $check1 = Attestation::where('id', $request->id)
            ->where('status', 'Draft')
            ->first();

        //2. dont allow duplicate
        $check2 = Attestation::where([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
            'dob' => $request->dob,
            'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid,
            'cap_guid' => $request->cap_guid,
            'email' => $request->email,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'province' => $request->province,
            'country' => $request->country,
        ])
            ->where('id', '!=', $request->id)
            ->first();

        if (is_null($check1)) {
            $error = 'This attestation cannot be edited. Only draft attestations can be edited.';
        }
        elseif (!is_null($check2)) {
            $error = "There's already an attestation for the exact same user.";
        }
        else {
            $cap = Cap::where('guid', $request->cap_guid)->first();

            Attestation::where('id', $request->id)
                ->update($request->validated());
            $attestation = Attestation::find($request->id);
            $this->authorize('download', $attestation);
            event(new AttestationDraftUpdated($cap, $attestation, $check1, $request->status));

        }

        return $error;
    }

    public function updateAttestations(AttestationEditRequest $request, $page = NULL): RedirectResponse|\Illuminate\Routing\Redirector {
        $error = $this->update($request);
        $att = Attestation::where('id', $request->id)->first();
        if ($page === 'institution') {
            if (!is_null($error)) {
                return redirect(route('ministry.institutions.show', [
                    $att->institution->id,
                    'attestations',
                ]))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.institutions.show', [
                $att->institution->id,
                'attestations',
            ]));
        }
        else {
            if (!is_null($error)) {
                return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.attestations.index'));
        }
    }

    public function rebuild(Request $request, Attestation $attestation) {
        $this->authorize('rebuild', $attestation);
        event(new AttestationRebuildPdf($attestation));
        return redirect(route('ministry.attestations.index'));
    }


    public function download(Request $request, Attestation $attestation) {
        $this->authorize('download', $attestation);
        $storedPdf = AttestationPdf::where('attestation_guid', $attestation->guid)
            ->first();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(base64_decode($storedPdf->content));
        $pdf->getCanvas()
            ->get_cpdf()
            ->setEncryption('', env('PDF_KEY'), ['print']);

        return $pdf->download($attestation->last_name . '-' . $attestation->fed_guid . '-attestation.pdf');
    }

    private function paginateAtte() {
        $attestations = Attestation::where('fed_cap_guid', Cache::get('global_fed_caps')['default']);

        if (request()->filter_name !== NULL) {
            $attestations = $attestations->where('first_name', 'ILIKE', '%' . request()->filter_name . '%')
                ->orWhere('last_name', 'ILIKE', '%' . request()->filter_name . '%');
        }
        if (request()->filter_pal !== NULL) {
            $attestations = $attestations->where('fed_guid', request()->filter_pal);
        }
        if (request()->filter_program) {
            $attestations->whereHas('program', function($query) {
                $query->where('program_graduate', request()->filter_program === 'graduate');
            });
        }

        if (request()->sort === 'program_graduate') {
            $attestations->join('programs', 'attestations.program_guid', '=', 'programs.guid')
                ->orderBy('programs.program_graduate', request()->direction ?? 'asc');
        }
        elseif (request()->sort !== NULL) {
            $attestations = $attestations->orderBy(request()->sort, request()->direction);
        }
        else {
            $attestations = $attestations->orderBy('created_at', 'desc');
        }

        return $attestations->with([
            'institution.activeCaps',
            'institution.programs',
            'program' => function($query) {
                $query->select('guid', 'program_name', 'program_graduate');
            }
        ])->paginate(25)->onEachSide(1)->appends(request()->query());
    }

}
