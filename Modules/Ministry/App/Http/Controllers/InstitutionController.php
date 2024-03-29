<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionEditRequest;
use App\Http\Requests\InstitutionStoreRequest;
use App\Models\Attestation;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Response;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $institutions = $this->paginateInst();

        return Inertia::render('Ministry::Institutions', ['status' => true, 'results' => $institutions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstitutionStoreRequest $request): \Inertia\Response
    {
        $institution = Institution::create($request->validated());
        $institutions = $this->paginateInst();

        return Inertia::render('Ministry::Institutions', ['page' => 'details', 'results' => $institutions,
            'newInst' => $institution]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Institution $institution, $page = 'details')
    {
        $institution = Institution::where('id', $institution->id)->with(
            ['caps.program', 'activeCaps', 'staff.user.roles', 'attestations', 'programs']
        )->first();
        $fedCaps = FedCap::active()->get();
        $institutions = Institution::whereHas('activeCaps')->active()->with('activeCaps')->get();

        return Inertia::render('Ministry::Institution', ['page' => $page, 'results' => $institution,
            'institutions' => $institutions, 'fedCaps' => $fedCaps, 'countries' => Country::orderBy('name')->get(), ]);
    }

    /**
     * Show the specified resource.
     */
    public function fetchAttestations(Request $request)
    {
        $attestations = Attestation::where('institution_guid', $request->input('institution_guid'))
            ->with('institution.activeCaps', 'institution.programs')->orderBy('created_at', 'desc')->get();

        return Response::json(['status' => true, 'body' => $attestations]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionEditRequest $request): RedirectResponse
    {
        Institution::where('id', $request->id)->update($request->validated());

        return Redirect::route('ministry.institutions.show', [$request->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    private function paginateInst()
    {
        $institutions = new Institution();
        $institutions = $institutions->with('activeCaps');

        if (request()->filter_name !== null) {
            $institutions = $institutions->where('name', 'ILIKE', '%'.request()->filter_name.'%');
        }

        if (request()->sort !== null) {
            $institutions = $institutions->orderBy(request()->sort, request()->direction);
        } else {
            $institutions = $institutions->orderBy('name');
        }

        return $institutions->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
