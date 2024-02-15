<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapEditRequest;
use App\Http\Requests\CapStoreRequest;
use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Institution;
use Inertia\Inertia;

class CapController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CapStoreRequest $request): \Inertia\Response
    {
        $conditions = ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'active_status' => true,
            'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid];

        //add a check to see if there is already a cap associated with this program
        if ($request->has('program_id') && $request->program_id != '') {
            $conditions['program_guid'] = $request->program_guid;
        } else {
            $conditions['program_guid'] = null;
        }

        $check = Cap::where($conditions)->first();

        //if there is no cap, then create new one
        if (is_null($check)) {
            Cap::create($request->validated());
        }

        //there is one, then disable it, and create a new one
        else {
            $check->active_status = false;
            $check->save();

            $comment = 'Previous cap Start Date: '.$check->start_date.', End Date: '.$check->end_date.
                ', Total: '.$check->total_attestations.', Issued Attestations: '.$check->issued_attestations;

            $cap = Cap::create($request->validated());
            $cap->comment = is_null($check->comment) ? $cap->comment . ". " . $comment : $check->comment.'. '.$cap->comment . ". " . $comment;
            $cap->save();
        }

        $institution = Institution::where('id', $request->institution_id)->with(['caps', 'staff.user.roles'])->first();
        $fedCaps = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'caps', 'results' => $institution,
            'fedCaps' => $fedCaps]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CapEditRequest $request): \Inertia\Response
    {
        $conditions = ['start_date' => $request->start_date, 'end_date' => $request->end_date, 'active_status' => true,
            'fed_cap_guid' => $request->fed_cap_guid, 'institution_guid' => $request->institution_guid];

        //add a check to see if there is already a cap associated with this program
        if ($request->has('program_guid') && $request->program_guid != '') {
            $conditions['program_guid'] = $request->program_guid;
        } else {
            $conditions['program_guid'] = null;
        }

        $check = Cap::where('id', '!=', $request->id)->where($conditions)->first();
        if (is_null($check)) {
            Cap::where('id', $request->id)->update($request->validated());
        }
        $institution = Institution::where('guid', $request->institution_guid)->with(['caps', 'staff.user.roles'])->first();
        $fedCaps = FedCap::active()->get();

        return Inertia::render('Ministry::Institution', ['page' => 'caps', 'results' => $institution,
            'fedCaps' => $fedCaps]);
    }
}
