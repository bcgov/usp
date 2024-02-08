<?php

namespace Modules\Institution\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffEditRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


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
                return $q->whereIn('name', [Role::Institution_USER, Role::Institution_GUEST]);
            })->orderBy('created_at', 'desc')->get();

        foreach ($staff as $user) {
            if ($user->roles->contains('name', Role::Institution_USER)) {
                $user->access_type = 'U';
            } else {
                $user->access_type = 'G';
            }
        }

        return Inertia::render('Institution::Maintenance', ['status' => true, 'results' => $staff, 'page' => 'staff']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response::render
     */
    public function staffShow(Request $request, User $user): \Inertia\Response
    {
        if ($user->roles->contains('name', Role::Institution_USER)) {
            $user->access_type = 'U';
        } else {
            $user->access_type = 'G';
        }

        return Inertia::render('Institution::Maintenance', ['status' => true, 'results' => $user, 'page' => 'staff-edit']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse::render
     */
    public function staffEdit(StaffEditRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('update', $user);

        $user->disabled = $request->input('disabled');
        $user->save();

        //reset roles
        $roles = Role::whereIn('name', [Role::Institution_USER, Role::Institution_GUEST])->get();
        foreach ($roles as $role) {
            $user->roles()->detach($role);
        }

        //if admin add admin role
        if ($request->access_type == 'U') {
            $role = Role::where('name', Role::Institution_USER)->first();
            $user->roles()->attach($role);
        } else {
            $role = Role::where('name', Role::Institution_GUEST)->first();
            $user->roles()->attach($role);
        }

        return Redirect::route('institution.maintenance.staff.list');
    }

}
