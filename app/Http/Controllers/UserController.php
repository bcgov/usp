<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjaxRequest;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\InstitutionStaff;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Ministry\App\Http\Controllers\FedCapController;
use Response;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;

class UserController extends Controller
{
    /**
     * Display first page after login (dashboard page)
     */
    public function home(Request $request)
    {
        return Inertia::render('Home');
    }

    public function appLogin(Request $request)
    {
        $provider = new Keycloak([
            'authServerUrl' => env('KEYCLOAK_SERVER_URL'),
            'realm' => env('KEYCLOAK_REALM'),
            'clientId' => env('KEYCLOAK_CLIENT_ID'),
            'clientSecret' => env('KEYCLOAK_CLIENT_SECRET'),
            'redirectUri' => env('KEYCLOAK_REDIRECT_URI'),
        ]);
        // This is needed to have the provider logout url formatted correctly
        $provider->setVersion('24.0.0');

        return $this->loginUser($request, $provider, Role::Ministry_GUEST);
    }

    public function bceidLogin(Request $request)
    {
        $provider = new Keycloak([
            'authServerUrl' => env('KEYCLOAK_BCEID_SERVER_URL'),
            'realm' => env('KEYCLOAK_REALM'),
            'clientId' => env('KEYCLOAK_BCEID_CLIENT_ID'),
            'clientSecret' => env('KEYCLOAK_BCEID_CLIENT_SECRET'),
            'redirectUri' => env('KEYCLOAK_BCEID_REDIRECT_URI'),
        ]);
        $provider->setVersion('24.0.0');

        return $this->loginUser($request, $provider, Role::Institution_GUEST);
    }

    private function loginUser(Request $request, $provider, $type): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {

        if (! $request->has('code')) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $request->session()->put('oauth2state', $provider->getState());
//            \Log::info('$authUrl: '.$authUrl);
//            \Log::info('$provider->getState(): '.$provider->getState());

            return Redirect::to($authUrl);

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (! $request->has('state') || ($request->state !== $request->session()->get('oauth2state'))) {
            $request->session()->forget('oauth2state');
            \Log::info('messed up state '.$request->state.' !== '.$request->session()->get('oauth2state'));

            //Invalid state, make sure HTTP sessions are enabled
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'We could not log you in. Please contact RequestIT@gov.bc.ca',
            ]);
        } else {
            // Try to get an access token (using the authorization coe grant)
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $request->code,
                ]);
            } catch (\Exception $e) {
                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Failed to get access token: '.$e->getMessage(),
                ]);
            }

            // Optional: Now you have a token you can look up a users profile data
            try {
                // We got an access token, let's now get the user's details
                $provider_user = $provider->getResourceOwner($token);
                $provider_user = $provider_user->toArray();
//                \Log::info('We got a token: '.$token);
//                \Log::info('$provider_user: '.json_encode($provider_user));
                $tokenValues = $token->getValues();
                if (isset($tokenValues['id_token'])) {
                    $idToken = $tokenValues['id_token'];
                    $returnUrl = env('KEYCLOAK_LOGOUT_URL1') . '?retnow=1&returl=' . urlencode(env('KEYCLOAK_LOGOUT_URL2').'?id_token_hint=' . $idToken . '&post_logout_redirect_uri=' . env('KEYCLOAK_LOGOUT_URL3'));
                    $request->session()->put('kc_logout_uri', $returnUrl);
                }
                \Log::info('KC Logout : '.$provider->getLogoutUrl(['access_token' => $token]));
//                \Log::info('idToken: ');
//                \Log::info($token->getValues());
                \Log::info('We got a token: '.$token);
                \Log::info('$provider_user: '.json_encode($provider_user));

            } catch (\Exception $e) {
                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Failed to get resource owner: '.$e->getMessage(),
                ]);
            }

            $user = null;
            $failMsg = null;
            if ($type === Role::Ministry_GUEST) {
                $user = User::where('idir_user_guid', 'ilike', $provider_user['idir_user_guid'])->first();
                $failMsg = 'Welcome back! Please contact Ministry Admin to grant you access.';
            }
            if ($type === Role::Institution_GUEST) {
                $user = User::where('bceid_user_guid', 'ilike', $provider_user['bceid_user_guid'])->first();
                $failMsg = 'Welcome back! Please contact Institution Admin to grant you access.';

                // Added later to capture business name
/*                if (!is_null($user) && is_null($user->bceid_business_name)) {
                    $user->update(['bceid_business_name' => $provider_user['bceid_business_name']]);
                }*/
            }

            //if it is a new IDIR or BCeID user, register the user first
            if (is_null($user)) {
                $valid = $this->newUser($provider_user, $type);
                if ($valid == '200') {
                    return Inertia::render('Auth/Login', [
                        'loginAttempt' => true,
                        'hasAccess' => false,
                        'status' => 'Please contact Admin to grant you access.',
                    ]);
                } else {
                    return Inertia::render('Auth/Login', [
                        'loginAttempt' => true,
                        'hasAccess' => false,
                        'status' => $valid,
                    ]);
                }

                //if the user has been disabled
            } elseif (!is_null($user) && $user->disabled === true) {
                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Access denied. Please contact Admin.',
                ]);
            }


            $user->name = $provider_user['name'];
            $user->save();
            \Log::info('We got a name: '.$provider_user['name']);

            //else the user has access
            if ($type === Role::Ministry_GUEST) {
                //check if the user is a guest
                $rolesToCheck = [Role::Ministry_GUEST];
                if ($user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()) {
                    return Inertia::render('Auth/Login', [
                        'loginAttempt' => true,
                        'hasAccess' => false,
                        'status' => $failMsg,
                    ]);
                }

                Auth::login($user);

                // Set default Fed Cap
                $this->setDefaultFedCap();

                return Redirect::route('ministry.home');
            }

            if ($type === Role::Institution_GUEST) {
                //check if the user is a guest
                $rolesToCheck = [Role::Institution_GUEST];
                if ($user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()) {
                    return Inertia::render('Auth/Login', [
                        'loginAttempt' => true,
                        'hasAccess' => false,
                        'status' => $failMsg,
                    ]);
                }

                // Set default Fed Cap
                $this->setDefaultFedCap();

                Auth::login($user);

                return Redirect::route('institution.dashboard');
            }

            // Set default Fed Cap
            $this->setDefaultFedCap();

            return Redirect::route('login');
        }
    }

    /**
     * fetch active support users
     */
    public function activeUsers(AjaxRequest $request)
    {
        $users = User::whereEndDate(null)->whereDisabled(false)->get();

        return Response::json(['status' => true, 'users' => $users]);
    }

    /**
     * fetch cancelled support users
     */
    public function cancelledUsers(AjaxRequest $request)
    {
        $users = User::where('end_date', '!=', null)->whereDisabled(true)->get();

        return Response::json(['status' => true, 'users' => $users]);
    }

    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function login(Request $request)
    {
        Cache::forget('global_fed_caps_' . Auth::id());
        return Inertia::render('Auth/Login', [
            'loginAttempt' => false,
            'hasAccess' => false,
            'status' => session('status'),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        if(Auth::check()){
            Cache::forget('global_fed_caps_' . Auth::id());
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function newUser($provider_user, $type)
    {
        $valid = '200';
        if (isset($provider_user['idir_username']) && $provider_user['idir_username']) {
            $check = User::where('idir_username', Str::upper($provider_user['idir_username']))->first();
            if (! is_null($check)) {
                $valid = 'This IDIR is already in use. Please contact the admin.';
            }
        } elseif (isset($provider_user['bceid_username']) && $provider_user['bceid_username']) {
            $check = User::where('bceid_username', Str::upper($provider_user['bceid_username']))->first();
            if (! is_null($check)) {
                $valid = 'This BCeID is already in use. Please contact the admin.';
            }
        }

        if ($valid === '200') {
            $user = new User();
            $user->guid = Str::orderedUuid()->getHex();
            $user->name = Str::title($provider_user['name']);
            $user->first_name = Str::title($provider_user['given_name']);
            $user->last_name = Str::title($provider_user['family_name']);
            $user->email = Str::lower($provider_user['email']);
            $user->disabled = false;
            $user->idir_username = isset($provider_user['idir_username']) ? Str::upper($provider_user['idir_username']) : null;
            $user->bceid_username = isset($provider_user['bceid_username']) ? Str::upper($provider_user['bceid_username']) : null;
            $user->idir_user_guid = isset($provider_user['idir_user_guid']) ? Str::upper($provider_user['idir_user_guid']) : null;
            $user->bceid_user_guid = isset($provider_user['bceid_user_guid']) ? Str::upper($provider_user['bceid_user_guid']) : null;
            $user->bceid_business_guid = isset($provider_user['bceid_business_guid']) ? Str::upper($provider_user['bceid_business_guid']) : null;
            $user->password = Hash::make(Str::lower($provider_user['email']));
            $user->save();
            $this->checkRoles($user, $type);

            if(isset($provider_user['bceid_business_guid'])) {
                \Log::info('isset $provider_user');
                $this->checkInstitutionStaff($user, $provider_user);
            }else{
                \Log::info('net set $provider_user');
            }
        }

        return $valid;
    }

    private function checkInstitutionStaff($user, $provider_user)
    {
        $user = User::find($user->id);
        $institution = Institution::where('bceid_business_guid', $user->bceid_business_guid)->first();
        $institutionStaff = InstitutionStaff::where('bceid_user_guid', $user->bceid_user_guid)->with('institution')->first();

        // If the ministry did not setup any user with that bceid_business_guid then don't auto register
        if (! is_null($institution) && is_null($institutionStaff)) {
            \Log::info('$institution is not null and staff is');

            $staff = new InstitutionStaff();
            $staff->guid = Str::orderedUuid()->getHex();
            $staff->user_guid = $user->guid;
            $staff->institution_guid = $institution->guid;
            $staff->bceid_business_guid = $user->bceid_business_guid;
            $staff->bceid_user_guid = $user->bceid_user_guid;
            $staff->bceid_user_id = Str::upper($provider_user['bceid_username']);
            $staff->bceid_user_name = Str::title($provider_user['name']);
            $staff->bceid_user_email = Str::lower($provider_user['email']);
            $staff->status = 'Active';
            $staff->save();
        }else{
            \Log::info('$institution no go');
        }

        if(is_null($institution)){
            \Log::info('no institution for bceid_business_guid: ' . $user->bceid_business_guid);
        }
        if(is_null($institutionStaff)){
            \Log::info('no staff for bceid_business_guid: ' . $user->bceid_business_guid);
        }


    }

    //new user to be assigned as guest
    private function checkRoles($user, $type)
    {
        if (is_null($user->roles()->first())) {
            $role = Role::where('name', $type)->first();
            $user->roles()->attach($role);
        }
    }

    /**
     * Set the default Fed Cap at user login based on current date
     * @return bool
     */
    private function setDefaultFedCap()
    {
        $currentDate = Carbon::now();
        $fedCap = FedCap::where('start_date', '<=', $currentDate->format('Y-m-d'))
            ->where('end_date', '>=', $currentDate->format('Y-m-d'))
            ->first();

        if ($fedCap) {
            $data = [
                'fed_cap_guid' => $fedCap->guid
            ];
            $request = new Request($data);

            $controller = app()->make('Modules\Ministry\App\Http\Controllers\FedCapController');
            $response = app()->call([$controller, 'setDefault'], ['request' => $request]);

            if ($response) {
                return true;
            }
        }

        return false;
    }

    // This function will attempt to login the user coming from PDEX
    public function pdexLogin(Request $request)
    {

        //if any of the formData keys are missing don't login the user
        $token = $request->input('token');
        $refreshToken = $request->input('refresh_token');
        $userType = $request->input('user_type');
        $userId = $request->input('ud');
        $logoutUrl = $request->input('logoutUrl');
        \Log::info('pdexLogin called with userType: ' . $userType . ', userId: ' . $userId . ', logoutUrl: ' . $logoutUrl);
        \Log::info('Received request: ' . json_encode($request->all()));

        if (empty($token) || empty($userType) || empty($userId) || empty($logoutUrl)) {
            return response()->json(['error' => 'Missing data 2239'], 400);
        }
        switch($userType) {
            case 'idir':
                $type = Role::Ministry_GUEST;
                break;
            case 'bceid':
                $type = Role::Institution_GUEST;
                break;
            default:
                $type = null;
        }

        // Proceed with the login logic using the validated formData
        $decodedToken = $this->decodeJWT($token);
        if (isset($decodedToken['error'])) {
            return response()->json(['error' => $decodedToken['error']], 400);
        }
        if (empty($decodedToken['payload']['sub'])) {
            return response()->json(['error' => 'Missing data 2242'], 400);
        }
        if($decodedToken['payload']['aud'] !== env('PDEX_JWT_AUDIENCE')) {
            \Log::error('Invalid audience: ' . $decodedToken['payload']['aud']);
            return response()->json(['error' => 'Missing data 2243'], 400);
        }
        \Log::info('Decoded JWT Token: ' . json_encode($decodedToken));

        $request->session()->put('kc_logout_uri', $logoutUrl);
        $user = null;
        $failMsg = null;
        if ($type === Role::Ministry_GUEST) {
            $user = User::where('idir_user_guid', 'ilike', $decodedToken['payload']['idir_user_guid'])->first();
            $failMsg = 'Welcome back! Please contact Ministry Admin to grant you access.';
        }
        if ($type === Role::Institution_GUEST) {
            $user = User::where('bceid_user_guid', 'ilike', $decodedToken['payload']['bceid_user_guid'])->first();
            $failMsg = 'Welcome back! Please contact Institution Admin to grant you access.';
        }

        //if it is a new IDIR or BCeID user, register the user first
        if (is_null($user)) {
            $valid = $this->newUser($decodedToken['payload'], $type);
            if ($valid == '200') {
                return Inertia::render('Auth/LoginPdex', [
                    'pdexLoginUrl' => env('PDEX_LOGIN_URL'),
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Please contact Admin to grant you access.',
                ]);
            } else {
                return Inertia::render('Auth/LoginPdex', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => $valid,
                ]);
            }

            //if the user has been disabled
        } elseif (!is_null($user) && $user->disabled === true) {
            return Inertia::render('Auth/LoginPdex', [
                'pdexLoginUrl' => env('PDEX_LOGIN_URL'),
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'Access denied. Please contact Admin.',
            ]);
        }


        $user->name = $decodedToken['payload']['name'];
        $user->save();
        \Log::info('We got a name: '.$decodedToken['payload']['name']);

        //else the user has access
        if ($type === Role::Ministry_GUEST) {
            //check if the user is a guest
            $rolesToCheck = [Role::Ministry_GUEST];
            if ($user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()) {
                return Inertia::render('Auth/LoginPdex', [
                    'pdexLoginUrl' => env('PDEX_LOGIN_URL'),
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => $failMsg,
                ]);
            }

            Auth::login($user);

            // Set default Fed Cap
            $this->setDefaultFedCap();

            return Redirect::route('ministry.home');
        }

        if ($type === Role::Institution_GUEST) {
            //check if the user is a guest
            $rolesToCheck = [Role::Institution_GUEST];
            if ($user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()) {
                return Inertia::render('Auth/LoginPdex', [
                    'pdexLoginUrl' => env('PDEX_LOGIN_URL'),
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => $failMsg,
                ]);
            }

            // Set default Fed Cap
            $this->setDefaultFedCap();

            Auth::login($user);

            return Redirect::route('institution.dashboard');
        }

        // Set default Fed Cap
        $this->setDefaultFedCap();

        return Inertia::render('Auth/LoginPdex', [
            'pdexLoginUrl' => env('PDEX_LOGIN_URL'),
            'loginAttempt' => true,
            'hasAccess' => false,
            'status' => "Login failed. Please try again.",
        ]);

    }

    // Decode JWT token to see its contents (without verification for debugging)
    private function decodeJWT($token)
    {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) === 3) {
            try {
                // Decode the payload (second part)
                $payload = json_decode(base64_decode(str_pad(strtr($tokenParts[1], '-_', '+/'), strlen($tokenParts[1]) % 4, '=', STR_PAD_RIGHT)), true);
                
                // Decode the header (first part)
                $header = json_decode(base64_decode(str_pad(strtr($tokenParts[0], '-_', '+/'), strlen($tokenParts[0]) % 4, '=', STR_PAD_RIGHT)), true);
                
                $tokenInfo = [
                    'header' => $header,
                    'payload' => $payload,
                    'raw_token_length' => strlen($token),
                    'token_parts_count' => count($tokenParts)
                ];
            } catch (\Exception $e) {
                \Log::error('Failed to decode JWT token: ' . $e->getMessage());
                $tokenInfo = [
                    'error' => 'Missing data 2240',
                    'raw_token_length' => strlen($token)
                ];
            }
        } else {
            \Log::error('Invalid JWT format');
            $tokenInfo = [
                'error' => 'Missing data 2241',
                'token_parts_count' => count($tokenParts),
                'raw_token_length' => strlen($token)
            ];
        }

        return $tokenInfo;
    }

}
