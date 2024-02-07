<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapEditRequest;
use App\Http\Requests\CapStoreRequest;
use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CapController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(CapStoreRequest $request): \Inertia\Response
    {
        $conditions = ['start_date' => $request->start_date, 'end_date' => $request->end_date,
            'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid];
        if($request->has('program_id') && $request->program_id != ''){
            $conditions['program_guid'] = $request->program_guid;
        }
        $check = Cap::where($conditions)->first();
        if(is_null($check)){
            Cap::create($request->validated());
        }
        $institution = Institution::where('id', $request->institution_id)->with(['caps', 'staff'])->first();
        $fedCaps = FedCap::active()->get();
        return Inertia::render('Ministry::Institution', ['page' => 'caps', 'results' => $institution,
            'fedCaps' => $fedCaps]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CapEditRequest $request): \Inertia\Response
    {
        $check = Cap::where('id', '!=', $request->id)
            ->where(['start_date' => $request->start_date, 'end_date' => $request->end_date,
            'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid])->first();
        if(is_null($check)){
            Cap::where('id', $request->id)->update($request->validated());
        }
        $institution = Institution::where('guid', $request->institution_guid)->with(['caps', 'staff'])->first();
        $fedCaps = FedCap::active()->get();
        return Inertia::render('Ministry::Institution', ['page' => 'caps', 'results' => $institution,
            'fedCaps' => $fedCaps]);
    }
}
