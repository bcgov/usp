<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttestationStoreRequest;
use App\Http\Requests\InstitutionEditRequest;
use App\Http\Requests\InstitutionStoreRequest;
use App\Models\Attestation;
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attestations = $this->paginateInst();
        $institutions = Institution::active()->with('activeCaps')->get();
        return Inertia::render('Ministry::Attestations', ['status' => true, 'results' => $attestations,
            'institutions' => $institutions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttestationStoreRequest $request): \Inertia\Response
    {
        $attestation = null;
        $check = Attestation::where(['student_name' => $request->student_name, 'student_id_number' => $request->student_id_number,
            'student_dob' => $request->student_dob, 'institution_guid' => $request->institution_guid,
            'cap_guid' => $request->cap_guid])->first();
        if(is_null($check)){
            $attestation = Attestation::create($request->validated());
        }

        $attestations = $this->paginateInst();
        $institutions = Institution::active()->with('activeCaps')->get();

        return Inertia::render('Ministry::Attestations', ['results' => $attestations,
            'institutions' => $institutions, 'newAtte' => $attestation]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Institution $institution, $page = 'details')
    {
        $institution = Institution::where('id', $institution->id)->with(['caps', 'staff'])->first();
        $fedCaps = FedCap::active()->get();
        return Inertia::render('Ministry::Institution', ['page' => $page, 'results' => $institution,
            'fedCaps' => $fedCaps]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionEditRequest $request): RedirectResponse
    {
        Institution::where('id', $request->id)->update($request->validated());

        return Redirect::route('ministry.institutions.show', [$request->id]);
    }

    public function download(Request $request, Attestation $attestation)
    {
        $this->authorize('download', $attestation);
        $attestation = Attestation::where('id', $attestation->id)->with('institution')->first();
        $now_d = date('Y-m-d');
        $now_t = date('H:m:i');
        $utils = Util::getSortedUtils();
        $pdf = PDF::loadView('ministry::pdf', compact('attestation', 'now_d', 'now_t', 'utils'));

        return $pdf->download(mt_rand().'-'.$attestation->guid.'-attestation.pdf');
    }


    private function paginateInst()
    {
        $attestations = new Attestation();

        if (request()->filter_name !== null) {
            $attestations = $attestations->where('student_name', 'ILIKE', '%'.request()->filter_name.'%');
        }

        if (request()->sort !== null) {
            $attestations = $attestations->orderBy(request()->sort, request()->direction);
        } else {
            $attestations = $attestations->orderBy('created_at', 'desc');
        }

        return $attestations->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
