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
        return Inertia::render('Ministry::Reports', ['results' => null, 'page' => 'summary']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportsDetail(Request $request): \Inertia\Response
    {
        $institutions = Institution::select('guid', 'name', 'category')->with('activeCaps')->orderBy('name')->get();
        $categories = Institution::select('category')->whereNotNull('category')->groupBy('category')->orderBy('category')->get();
        return Inertia::render('Ministry::Reports', ['results' => ['institutions' => $institutions, 'categories' => $categories], 'page' => 'detail']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportSources(Request $request): \Inertia\Response
    {
        return Inertia::render('Ministry::Reports', ['results' => null, 'page' => 'sources']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function reportSourcesFetch(Request $request, $from, $to, $type)
    {
        $fromDate = $from;
        $toDate = $to . " 23:59:59";

        if($type === 'attestation'){
            $query = "select now(), s.institution_guid, s.fed_guid, s.first_name, s.last_name, s.status, s.issue_date, s.country, i.guid, i.name from attestations s
join institutions i on i.guid = s.institution_guid where s.created_at between '$fromDate' and '$toDate'";
        }
        if($type === 'cap'){
            $query = "select now(), s.institution_guid, s.fed_cap_guid, s.total_attestations, s.issued_attestations, s.draft_attestations, s.active_status, i.guid,
       i.name, i.category, i.dli, i.info_sharing_agreement, fc.guid, fc.start_date, fc.end_date, fc.total_attestations as fc_total_attestations, fc.status from caps s
join institutions i on i.guid = s.institution_guid
join fed_caps fc on fc.guid = s.fed_cap_guid where fc.status='Active'";
        }
        if($type === 'staff'){
            $query = "select now(), s.institution_guid, s.bceid_user_name, i.guid, i.name from institution_staff s
join institutions i on i.guid = s.institution_guid where s.created_at between '$fromDate' and '$toDate'";
        }
        if($type === 'ircc'){
            $query = "select i.name, i.dli, p.program_name, a.student_number, a.id_number, a.fed_guid, a.guid, a.first_name, a.last_name, a.address1,
a.city, a.country, a.dob, a.issue_date, a.expiry_date from attestations a
join institutions i on i.guid = a.institution_guid
join programs p on p.guid = a.program_guid where a.created_at between '$fromDate' and '$toDate'";
        }

        $csvData = [];
        $csvDataHeader = [];
        $rows = \DB::select($query);
        if(sizeof($rows) == 0) return "No results for the date range selected.";

        foreach ($rows[0] as $k => $v){
            $csvDataHeader[] = $k;
        }

        foreach ($rows as $d) {
            $row = [];
            foreach ($d as $v){
                $row[] = $v;
            }
            $csvData[] = $row;
        }
        $output = fopen('php://temp', 'w');
        // Write CSV headers
        fputcsv($output, $csvDataHeader);

        // Write CSV rows
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $response = Response::make(stream_get_contents($output), 200);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename=' . $request->type . "_data.csv");
        fclose($output);

        return $response;

    }

    /**
     * Display a listing of the resource.
     */
    public function reportsSummaryFetch(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date . " 23:59:59";

        $publicReport = ['total' => 0, 'issued' => 0, 'draft' => 0];
        $privateReport = ['total' => 0, 'issued' => 0, 'draft' => 0];


        //add missing inst that have not issued any att.

        $institutions = Institution::whereHas('activeCaps')->get();
        foreach ($institutions as $inst){
            $instType = $this->getReportType($inst->category);
            if($instType === 'public')
                $this->addEmptyInst($inst, $publicReport);
            if($instType === 'private')
                $this->addEmptyInst($inst, $privateReport);
        }

        $results = Attestation::whereBetween('created_at', [$fromDate, $toDate])->get();

        foreach ($results as $att) {
            $reportType = $this->getReportType($att->institution->category);

            if($reportType === 'public')
                $this->updateReport($att, $publicReport);
            if($reportType === 'private')
                $this->updateReport($att, $privateReport);
        }

        return response()->json([
            'status' => true,
            'body' => [
                'publicReport' => $publicReport,
                'privateReport' => $privateReport
            ]
        ]);
    }

    private function getReportType($category)
    {
        return in_array($category, ['College', 'Teaching University', 'University']) ? 'public' : 'private';
    }

    private function updateReport($att, &$report)
    {
        $instName = $att->institution->name;
        $status = $att->status === 'Issued' ? 'issued' : 'draft';

        if(!array_key_exists($att->institution->category, $report)){
            $report[$att->institution->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
            $report['total'] += $att->institution->activeCaps[0]->total_attestations;
            $report[$att->institution->category]['total'] += $att->institution->activeCaps[0]->total_attestations;
        }
        if(!array_key_exists($att->institution->name, $report[$att->institution->category]['instList'])){
            $report[$att->institution->category]['instList'][$att->institution->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
        }
        $report[$att->institution->category]['instList'][$instName]['total'] = $att->institution->activeCaps[0]->total_attestations;
        $report[$att->institution->category]['instList'][$instName][$status]++;
        $report[$att->institution->category][$status]++;
        $report[$status]++;

        ksort($report[$att->institution->category]['instList']);

    }

    private function addEmptyInst($inst, &$report)
    {
        if(!array_key_exists($inst->category, $report)){
            $report[$inst->category] = ['instList' => [], 'total' => 0, 'issued' => 0, 'draft' => 0];
        }
        if(!array_key_exists($inst->name, $report[$inst->category]['instList'])){
            $report[$inst->category]['instList'][$inst->name] = ['total' => 0, 'issued' => 0, 'draft' => 0];
        }
        $report[$inst->category]['instList'][$inst->name]['total'] = $inst->activeCaps[0]->total_attestations;
        $report[$inst->category]['total'] += $inst->activeCaps[0]->total_attestations;
        $report['total'] += $inst->activeCaps[0]->total_attestations;

        ksort($report[$inst->category]['instList']);
    }


    /**
     * Display a listing of the resource.
     */
    public function reportsDetailFetch(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date . " 23:59:59";

        $publicReport = ['total' => 0, 'issued' => 0, 'draft' => 0];
        $privateReport = ['total' => 0, 'issued' => 0, 'draft' => 0];


        //add missing inst that have not issued any att.

        $institutions = Institution::whereHas('activeCaps')->get();
        foreach ($institutions as $inst){
            $instType = $this->getReportType($inst->category);
            if($instType === 'public')
                $this->addEmptyInst($inst, $publicReport);
            if($instType === 'private')
                $this->addEmptyInst($inst, $privateReport);
        }

        $results = Attestation::whereBetween('created_at', [$fromDate, $toDate])->get();

        foreach ($results as $att) {
            $reportType = $this->getReportType($att->institution->category);

            if($reportType === 'public')
                $this->updateReport($att, $publicReport);
            if($reportType === 'private')
                $this->updateReport($att, $privateReport);
        }

        return response()->json([
            'status' => true,
            'body' => [
                'publicReport' => $publicReport,
                'privateReport' => $privateReport
            ]
        ]);
    }
}
