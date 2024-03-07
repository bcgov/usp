<?php

namespace Modules\Ministry\App\Http\Controllers;

use App\Events\StaffRoleChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\UtilEditRequest;
use App\Http\Requests\UtilStoreRequest;
use App\Models\Attestation;
use App\Models\Institution;
use App\Models\Role;
use App\Models\User;
use App\Models\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Response;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function staffList(Request $request): \Inertia\Response
    {
        $staff = User::with('roles')
            ->whereHas('roles', function ($q) {
                return $q->whereIn('name', [Role::Ministry_ADMIN, Role::Ministry_USER, Role::Ministry_GUEST]);
            })->orderBy('created_at', 'desc')->get();

        foreach ($staff as $user) {
            if ($user->roles->contains('name', Role::Ministry_ADMIN)) {
                $user->access_type = 'A';
            } elseif ($user->roles->contains('name', Role::Ministry_USER)) {
                $user->access_type = 'U';
            } else {
                $user->access_type = 'G';
            }
        }

        return Inertia::render('Ministry::Maintenance', ['status' => true, 'results' => $staff, 'page' => 'staff']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse::render
     */
    public function updateStatus(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $user);

        $user->disabled = $request->input('disabled');
        $user->save();

        return Redirect::route('ministry.maintenance.staff.list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse::render
     */
    public function updateRole(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $user);

        $newRole = Role::where('name', Role::Ministry_GUEST)->first();
        if ($request->input('role') === 'Admin') {
            $newRole = Role::where('name', Role::Ministry_ADMIN)->first();
        }
        if ($request->input('role') === 'User') {
            $newRole = Role::where('name', Role::Ministry_USER)->first();
        }

        //reset roles
        $roles = Role::whereIn('name', [Role::Ministry_ADMIN, Role::Ministry_USER, Role::Ministry_GUEST])->get();
        foreach ($roles as $role) {
            $user->roles()->detach($role);
        }

        $user->roles()->attach($newRole);
        event(new StaffRoleChanged($user, $newRole));

        return Redirect::route('ministry.maintenance.staff.list');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function utilList(Request $request): \Inertia\Response
    {
        $utils = Util::orderBy('field_name', 'asc')->get();

        $cat_utils = [];
        $cat_titles = [];
        foreach ($utils as $util) {
            $cat_utils[$util->field_type][] = $util;
        }
        foreach ($cat_utils as $k => $v) {
            $cat_titles[] = $k;
        }
        sort($cat_titles);

        return Inertia::render('Ministry::Maintenance', ['status' => true, 'results' => $cat_utils,
            'categories' => $cat_titles, 'page' => 'utils']);
    }

    /**
     * Update a utility resource.
     *
     * @return \Illuminate\Http\RedirectResponse::render
     */
    public function utilUpdate(UtilEditRequest $request, Util $util): \Illuminate\Http\RedirectResponse
    {
        $util->update($request->validated());

        return Redirect::route('ministry.maintenance.utils.list');
    }

    /**
     * Store a utility resource.
     *
     * @return \Illuminate\Http\RedirectResponse::render
     */
    public function utilStore(UtilStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        Util::create($request->validated());

        return Redirect::route('ministry.maintenance.utils.list');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportsSummary(Request $request): \Inertia\Response
    {

//        $publicReport = [
//            'total' => 0,
//            'issued' => 0,
//            'draft' => 0,
//        ];
//        $publicInst = Institution::whereIn('category', ['College', 'Teaching University', 'University'])->with('activeInstCaps','attestations')->orderBy('name')->get();
//        foreach ($publicInst as $inst){
//            if(!array_key_exists($inst->category, $publicReport)){
//                $publicReport[$inst->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
//            }
//            $publicReport[$inst->category]['instList'][$inst->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
//            foreach ($inst->activeInstCaps as $cap){
//                $publicReport[$inst->category]['instList'][$inst->name]['total'] = $cap->total_attestations;
//                $publicReport[$inst->category]['instList'][$inst->name]['issued'] = $cap->issued_attestations;
//                $publicReport[$inst->category]['instList'][$inst->name]['draft'] = $cap->draft_attestations;
//
//                $publicReport[$inst->category]['total'] += $cap->total_attestations;
//                $publicReport[$inst->category]['issued'] += $cap->issued_attestations;
//                $publicReport[$inst->category]['draft'] += $cap->draft_attestations;
//
//                $publicReport['total'] += $cap->total_attestations;
//                $publicReport['issued'] += $cap->issued_attestations;
//                $publicReport['draft'] += $cap->draft_attestations;
//            }
//        }
//
//
//        $privateReport = [
//            'total' => 0,
//            'issued' => 0,
//            'draft' => 0,
//        ];
//        $privateInst = Institution::whereNotIn('category', ['College', 'Teaching University', 'University'])->with('activeInstCaps','attestations')->orderBy('name')->get();
//        foreach ($privateInst as $inst){
//            if(!array_key_exists($inst->category, $privateReport)){
//                $privateReport[$inst->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
//            }
//            $privateReport[$inst->category]['instList'][$inst->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
//            foreach ($inst->activeInstCaps as $cap){
//                $privateReport[$inst->category]['instList'][$inst->name]['total'] = $cap->total_attestations;
//                $privateReport[$inst->category]['instList'][$inst->name]['issued'] = $cap->issued_attestations;
//                $privateReport[$inst->category]['instList'][$inst->name]['draft'] = $cap->draft_attestations;
//
//                $privateReport[$inst->category]['total'] += $cap->total_attestations;
//                $privateReport[$inst->category]['issued'] += $cap->issued_attestations;
//                $privateReport[$inst->category]['draft'] += $cap->draft_attestations;
//
//                $privateReport['total'] += $cap->total_attestations;
//                $privateReport['issued'] += $cap->issued_attestations;
//                $privateReport['draft'] += $cap->draft_attestations;
//            }
//        }

        return Inertia::render('Ministry::Reports', ['results' => null, 'page' => 'summary']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportsDetail(Request $request): \Inertia\Response
    {

        return Inertia::render('Ministry::Reports', ['results' => null, 'page' => 'detail']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportsSummaryFetch(Request $request)
    {

        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $publicReport = ['total' => 0, 'issued' => 0, 'draft' => 0,];
        $privateReport = ['total' => 0, 'issued' => 0, 'draft' => 0,];
        $results = Attestation::whereBetween('created_at', [$fromDate, $toDate])->get();

        foreach ($results as $att){
            //public
            if( in_array($att->institution->category, ['College', 'Teaching University', 'University']) ){
                if(!array_key_exists($att->institution->category, $publicReport)){
                    $publicReport[$att->institution->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
                }
                if(!array_key_exists($att->institution->name, $publicReport[$att->institution->category]['instList'])){
                    $publicReport[$att->institution->category]['instList'][$att->institution->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
                }
                $publicReport[$att->institution->category]['instList'][$att->institution->name]['total'] += 1;
                if($att->status === 'Issued')
                    $publicReport[$att->institution->category]['instList'][$att->institution->name]['issued'] += 1;
                else
                    $publicReport[$att->institution->category]['instList'][$att->institution->name]['draft'] += 1;

                $publicReport[$att->institution->category]['total'] += 1;
                if($att->status === 'Issued')
                    $publicReport[$att->institution->category]['issued'] += 1;
                else
                    $publicReport[$att->institution->category]['draft'] += 1;

                $publicReport['total'] += 1;
                if($att->status === 'Issued')
                    $publicReport['issued'] += 1;
                else
                    $publicReport['draft'] += 1;
            }else{
                if(!array_key_exists($att->institution->category, $privateReport)){
                    $privateReport[$att->institution->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
                }
                if(!array_key_exists($att->institution->name, $privateReport[$att->institution->category]['instList'])){
                    $privateReport[$att->institution->category]['instList'][$att->institution->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
                }
                $privateReport[$att->institution->category]['instList'][$att->institution->name]['total'] += 1;
                if($att->status === 'Issued')
                    $privateReport[$att->institution->category]['instList'][$att->institution->name]['issued'] += 1;
                else
                    $privateReport[$att->institution->category]['instList'][$att->institution->name]['draft'] += 1;

                $privateReport[$att->institution->category]['total'] += 1;
                if($att->status === 'Issued')
                    $privateReport[$att->institution->category]['issued'] += 1;
                else
                    $privateReport[$att->institution->category]['draft'] += 1;

                $privateReport['total'] += 1;
                if($att->status === 'Issued')
                    $privateReport['issued'] += 1;
                else
                    $privateReport['draft'] += 1;
            }
        }

        return Response::json(['status' => true, 'body' => ['publicReport' => $publicReport,
            'privateReport' => $privateReport]]);

    }

}
