<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramEditRequest;
use App\Http\Requests\ProgramStoreRequest;
use App\Models\Country;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Response;

class ProgramController extends Controller
{
    public function fetchPrograms(Request $request, ?Program $program = null)
    {
        $body = Program::where(['institution_guid' => $request->input('institution_guid'), 'active_status' => true])->get();
        if (! is_null($program)) {
            $body = $program;
        }

        return Response::json(['status' => true, 'body' => $body]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgramStoreRequest $request): \Inertia\Response
    {
        Program::create($request->validated());
        $institution = Institution::where('id', $request->institution_id)->with(['caps', 'staff.user.roles', 'attestations', 'programs'])->first();
        $fedPrograms = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'programs', 'results' => $institution,
            'fedPrograms' => $fedPrograms, 'countries' => Country::orderBy('name')->get()]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgramEditRequest $request): \Inertia\Response
    {
        Program::where('id', $request->id)->update($request->validated());
        $institution = Institution::where('guid', $request->institution_guid)->with(['caps', 'staff.user.roles', 'attestations', 'programs'])->first();
        $fedPrograms = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'programs', 'results' => $institution,
            'fedPrograms' => $fedPrograms, 'countries' => Country::orderBy('name')->get()]);
    }
}
