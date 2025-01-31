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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Response;
use Auth;

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
            ['capsByFedcap.program', 'activeCaps', 'staff.user.roles', 'programs']
        )->first();
        $fedCaps = FedCap::active()->get();

        $countries = Cache::remember('countries', 380, function () {
            return Country::where('active', true)->orderBy('name')->get();
        });

        return Inertia::render('Ministry::Institution', ['page' => $page, 'results' => $institution,
            'fedCaps' => $fedCaps, 'countries' => $countries, ]);
    }

    /**
     * Show the specified resource.
     */
    public function fetchAttestations(Request $request)
    {
        $attestations = Attestation::where('institution_guid', $request->input('g'))
            ->where('fed_cap_guid', Cache::get('global_fed_caps_' . Auth::id())['default'])
            ->with('institution.activeCaps', 'institution.programs')
            ->orderBy('created_at', 'desc')
            ->paginate(25)->onEachSide(1)->appends(request()->query());

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

    private function paginateInst()
    {
        $institutions = Institution::with('activeCaps');

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
