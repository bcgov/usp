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
        $globalFedCaps = [
            'list' => [],
            'default' => null,
        ];
        if (Auth::check()) {
            $user = User::find(Auth::id());

            // Sometimes Auth::id() is null and we need to enforce attaching the id to the cache
            if(!is_null(Auth::id())){
//                \Log::info('Updating Global Fed Caps for: ' . Auth::id());

                $globalFedCaps = Cache::remember('global_fed_caps_' . Auth::id(), now()->addHours(10), function () {
                    $fedCaps = FedCap::select('id', 'guid', 'start_date', 'end_date', 'status')
                        ->without(['caps'])
                        ->active()->orderBy('id')->get();
                    if($fedCaps->isEmpty()) {
                        return [
                            'list' => [],
                            'default' => null,
                        ];
                    }
                    return [
                        'list' => $fedCaps,
                        'default' => $fedCaps[0]->guid
                    ];
                });
            }
        }

        $sortedUtils = Cache::remember('sorted_utils', 180, function () {
            return Util::getSortedUtils();
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
