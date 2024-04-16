<?php

namespace App\Http\Middleware;

use App\Models\FedCap;
use App\Models\User;
use App\Models\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        $user = null;
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
        }

        $sortedUtils = Cache::remember('sorted_utils', 180, function () {
            return Util::getSortedUtils();
        });

        $globalFedCaps = Cache::remember('global_fed_caps', now()->addHours(10), function () {
            $fedCaps = FedCap::select('id', 'guid', 'start_date', 'end_date', 'status')
                ->without(['caps'])
                ->active()->orderBy('id')->get();
            return [
                'list' => $fedCaps,
                'default' => $fedCaps[0]->guid
                ];
        });

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'roles' => is_null($user) ? null : $user->roles,
                'readOnly' => Session::has('read-only'),
            ],
            'fedCapsData' => [
                'list' => $globalFedCaps['list'],
                'default' => $globalFedCaps['default'],
            ],
            'utils' => $sortedUtils,
            'ziggy' => function () {
                return (new Ziggy)->toArray();
            },
            'logoutUrl' => env('KEYCLOAK_LOGOUT_URL'),
        ]);
    }
}
