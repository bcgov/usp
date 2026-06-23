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

        if ($institution !== null) {
            $this->attachCapStats(
                collect($institution->activeCaps)->merge($institution->capsByFedcap)
            );
        }

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

        $paginated = $institutions->paginate(25)->onEachSide(1)->appends(request()->query());

        $this->attachCapStats($paginated->getCollection()->flatMap->activeCaps);

        return $paginated;
    }

    /**
     * Attach institution-level cap statistics to the given caps using a single
     * pair of aggregate queries instead of the per-cap accessors on the Cap
     * model (which run two COUNT queries every time a cap is serialized).
     *
     * The computed values are assigned to the Cap model's preset properties
     * (consumed by its `inst_active_cap_stat` / `inst_active_res_grad_cap_stat`
     * accessors) so the frontend requires no changes, while the per-cap COUNT
     * queries those accessors would otherwise run are avoided entirely.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Cap>  $caps
     */
    private function attachCapStats($caps): void
    {
        if ($caps->isEmpty()) {
            return;
        }

        $institutionGuids = $caps->pluck('institution_guid')->filter()->unique()->values()->all();
        $fedCapGuids = $caps->pluck('fed_cap_guid')->filter()->unique()->values()->all();

        if (empty($institutionGuids) || empty($fedCapGuids)) {
            return;
        }

        $issuedCounts = Attestation::query()
            ->selectRaw('institution_guid, fed_cap_guid, COUNT(*) as total')
            ->whereIn('status', ['Issued', 'Declined'])
            ->whereIn('institution_guid', $institutionGuids)
            ->whereIn('fed_cap_guid', $fedCapGuids)
            ->groupBy('institution_guid', 'fed_cap_guid')
            ->get()
            ->keyBy(fn ($row) => $row->institution_guid.'|'.$row->fed_cap_guid);

        $gradCounts = Attestation::query()
            ->selectRaw('institution_guid, fed_cap_guid, COUNT(*) as total')
            ->whereIn('status', ['Issued', 'Declined'])
            ->whereIn('institution_guid', $institutionGuids)
            ->whereIn('fed_cap_guid', $fedCapGuids)
            ->whereHas('program', function ($query) {
                $query->where('program_graduate', true);
            })
            ->groupBy('institution_guid', 'fed_cap_guid')
            ->get()
            ->keyBy(fn ($row) => $row->institution_guid.'|'.$row->fed_cap_guid);

        foreach ($caps as $cap) {
            $key = $cap->institution_guid.'|'.$cap->fed_cap_guid;
            $issued = (int) ($issuedCounts[$key]->total ?? 0);
            $issuedGrad = (int) ($gradCounts[$key]->total ?? 0);

            $cap->presetInstActiveCapStat = [
                'total' => $cap->total_attestations,
                'issued' => $issued,
                'remain' => $cap->total_attestations - $issued,
            ];

            $cap->presetInstActiveResGradCapStat = [
                'total_reserved_graduate' => $cap->total_reserved_graduate_attestations,
                'issued_reserved_graduate' => $issuedGrad,
            ];
        }
    }
}
