<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FedCapEditRequest;
use App\Http\Requests\FedCapStoreRequest;
use App\Models\FedCap;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ministry::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FedCapStoreRequest $request): \Inertia\Response
    {
        $fedCap = FedCap::create($request->validated());
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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('ministry::edit');
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
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
