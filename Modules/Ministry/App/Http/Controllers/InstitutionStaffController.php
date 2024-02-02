<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionStaffEditRequest;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\InstitutionStaff;
use Inertia\Inertia;

class InstitutionStaffController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(InstitutionStaffEditRequest $request): \Inertia\Response
    {
        InstitutionStaff::where('id', $request->id)->update($request->validated());

        $institution = Institution::where('guid', $request->institution_guid)->with(['caps', 'staff'])->first();
        $fedCaps = FedCap::active()->get();
        return Inertia::render('Ministry::Institution', ['page' => 'staff', 'results' => $institution,
            'fedCaps' => $fedCaps]);
    }
}
