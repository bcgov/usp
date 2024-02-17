<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Events\AttestationDraftUpdated;
use App\Events\AttestationIssued;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttestationEditRequest;
use App\Http\Requests\AttestationStoreRequest;
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
use Illuminate\Support\Str;
use Inertia\Inertia;

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
            'institutions' => $this->institutions, 'countries' => $this->countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request): ?array
    {
        $error = null;
        $inst = Institution::where('guid', $request->institution_guid)->first();
        //1. check for duplicate attestations
        $check1 = Attestation::where([
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid])->first();

        //2. check cap has not been reached
        if (is_null($check1)) {
            $attestation = Attestation::create($request->validated());
            event(new AttestationIssued($attestation->cap, $attestation, $request->status));
        } else {
            $error = "There's already an attestation for the same user.";
        }

        return [$error, $inst];
    }

    public function storeAttestations(AttestationStoreRequest $request, $page = null): RedirectResponse|\Illuminate\Routing\Redirector
    {
        list($error, $inst) = $this->store($request);

        if ($page === 'institution') {
            if (! is_null($error)) {
                return redirect(route('ministry.institutions.show', [$inst->id, 'attestations']))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.institutions.show', [$inst->id, 'attestations']));
        } else {
            if (! is_null($error)) {
                return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.attestations.index'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    private function update(AttestationEditRequest $request): ?string
    {
        $error = null;

        //1. update only draft attestations
        $check1 = Attestation::where('id', $request->id)->where('status', 'Draft')->first();

        if (! is_null($check1) ) {
            $cap = Cap::where('guid', $request->cap_guid)->first();

            Attestation::where('id', $request->id)->update($request->validated());
            $attestation = Attestation::find($request->id);
            $this->authorize('download', $attestation);
            event(new AttestationDraftUpdated($cap, $attestation, $check1, $request->status));

        } else {
            $error = 'This attestation cannot be edited. Only draft attestations can be edited.';
        }

        return $error;
    }

    public function updateAttestations(AttestationEditRequest $request, $page = null): RedirectResponse|\Illuminate\Routing\Redirector
    {
        $error = $this->update($request);
        $att = Attestation::where('id', $request->id)->first();
        if ($page === 'institution') {
            if (! is_null($error)) {
                return redirect(route('ministry.institutions.show', [$att->institution->id, 'attestations']))->withErrors(['first_name' => $error]);
            }

            return redirect(route('ministry.institutions.show', [$att->institution->id, 'attestations']));
        } else {
            if (! is_null($error)) {
                return redirect(route('ministry.attestations.index'))->withErrors(['first_name' => $error]);
            }

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
}
