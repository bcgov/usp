<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionEditRequest;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ministry::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show(Institution $institution, $page = 'details')
    {
        return Inertia::render('Ministry::Institution', ['page' => $page, 'results' => $institution]);
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
    public function update(InstitutionEditRequest $request): RedirectResponse
    {
        // Redirect or return a response
        Institution::where('id', $request->id)->update($request->validated());
        $institution = Institution::find($request->id);

        return Redirect::route('ministry.institutions.show', [$institution->id]);

//        return Inertia::render('Ministry::Institution', ['page' => 'details', 'results' => $institution]);
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

        if (request()->filter_name !== null) {
            $institutions = $institutions->where('name', 'ILIKE', '%'.request()->filter_name.'%');
        }

        if (request()->sort !== null) {
            $institutions = $institutions->orderBy(request()->sort, request()->direction);
        } else {
            $institutions = $institutions->orderBy('created_at', 'desc');
        }

        return $institutions->paginate(25)->onEachSide(1)->appends(request()->query());
    }
}
