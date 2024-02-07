<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttestationEditRequest;
use App\Http\Requests\AttestationStoreRequest;
use App\Http\Requests\InstitutionEditRequest;
use App\Http\Requests\InstitutionStoreRequest;
use App\Models\Attestation;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
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
        $this->institutions = Institution::active()->with('activeCaps')->get();
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
    public function store(AttestationStoreRequest $request): \Inertia\Response
    {
        $attestation = null;
        $check = Attestation::where([
            'first_name' => $request->first_name, 'last_name' => $request->last_name, 'id_number' => $request->id_number,
            'dob' => $request->dob, 'institution_guid' => $request->institution_guid,
            'program_guid' => $request->program_guid, 'cap_guid' => $request->cap_guid])->first();
        if(is_null($check)){
            $attestation = Attestation::create($request->validated());
        }

        $attestations = $this->paginateAtte();
        return Inertia::render('Ministry::Attestations', ['results' => $attestations,
            'institutions' => $this->institutions, 'newAtte' => $attestation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttestationEditRequest $request): \Inertia\Response
    {
        //update only draft attestations
        $check = Attestation::where('id', $request->id)->where('status', 'Draft')->first();
        if(!is_null($check)){
            Attestation::where('id', $request->id)->update($request->validated());
        }
        $attestation = Attestation::with('institution')->where('id', $request->id)->first();
        $institution = Institution::where('id', $attestation->institution->id)->with(['caps', 'activeCaps', 'staff', 'attestations', 'programs'])->first();
        return Inertia::render('Ministry::Institution', ['page' => 'attestations', 'results' => $institution,
            'fedCaps' => $this->fedCaps, 'countries' => $this->countries]);
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
        $pdf = PDF::loadView('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils'));

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
