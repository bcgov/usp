<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Events\FederalCapCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\FedCapEditRequest;
use App\Http\Requests\FedCapStoreRequest;
use App\Models\FedCap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Response;

class FedCapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fedCaps = $this->paginateInst();

        return Inertia::render('Ministry::FedCaps', ['results' => $fedCaps]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FedCapStoreRequest $request): \Inertia\Response
    {
        $fedCap = FedCap::create($request->validated());
        event(new FederalCapCreated($fedCap));

        $fedCaps = $this->paginateInst();

        return Inertia::render('Ministry::FedCaps', ['results' => $fedCaps, 'newfedCap' => $fedCap]);
    }

    /**
     * Show the specified resource.
     */
    public function show(FedCap $fedCap, $page = 'details')
    {
        return Inertia::render('Ministry::FedCap', ['page' => $page, 'results' => $fedCap]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FedCapEditRequest $request): RedirectResponse
    {
        FedCap::where('id', $request->id)->update($request->validated());

        return Redirect::route('ministry.fed_caps.show', [$request->id]);
    }

    /**
     * Show the specified resource.
     */
    public function fetchFedcapInst(Request $request)
    {
        $fedCap = FedCap::where('guid', $request->input('fed_cap_guid'))
            ->with('institutionCaps')->orderBy('created_at', 'desc')->first();

        return Response::json(['status' => true, 'body' => $fedCap]);
    }


    /**
     * Show the specified resource.
     */
    public function setDefault(Request $request)
    {
        $fedCap = FedCap::where('guid', $request->input('fed_cap_guid'))->first();

        Cache::forget('global_fed_caps_' . Auth::id());

        // Sometimes Auth::id() is null and we need to enforce attaching the id to the cache
        if(!is_null(Auth::id())){
//            \Log::info('updating global fed caps for: ' . Auth::id());

            Cache::remember('global_fed_caps_' . Auth::id(), now()->addHours(10), function () use ($fedCap) {
                $fedCaps = FedCap::select('id', 'guid', 'start_date', 'end_date', 'status')
                    ->without(['caps'])
                    ->active()->orderBy('id')->get();
                return [
                    'list' => $fedCaps,
                    'default' => $fedCap->guid
                ];
            });
        }


        return Response::json(['status' => true, 'body' => $fedCap]);
    }

    private function paginateInst()
    {
        $fedCaps = new FedCap();

        if (request()->filter_status !== null) {
            $fedCaps = $fedCaps->where('status', request()->filter_status);
        }
        if (request()->filter_sd !== null) {
            $fedCaps = $fedCaps->where('start_date', request()->filter_sd);
        }
        if (request()->filter_ed !== null) {
            $fedCaps = $fedCaps->where('end_date', request()->filter_ed);
        }

        if (request()->sort !== null) {
            $fedCaps = $fedCaps->orderBy(request()->sort, request()->direction);
        } else {
            $fedCaps = $fedCaps->orderBy('created_at', 'desc');
        }

        return $fedCaps->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
