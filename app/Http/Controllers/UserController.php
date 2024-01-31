<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjaxRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
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

        return $this->loginUser($request, $provider, Role::Institution_GUEST);
    }

    private function loginUser(Request $request, $provider, $type): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {

        if (! $request->has('code')) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $request->session()->put('oauth2state', $provider->getState());
            \Log::info('$authUrl: ' . $authUrl);
            \Log::info('$provider->getState(): ' . $provider->getState());

            return Redirect::to($authUrl);

            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (! $request->has('state') || ($request->state !== $request->session()->get('oauth2state'))) {
            $request->session()->forget('oauth2state');
            \Log::info('messed up state ' . $request->state . " !== " . $request->session()->get('oauth2state'));

            //Invalid state, make sure HTTP sessions are enabled
            return Inertia::render('Auth/Login', [
                'loginAttempt' => true,
                'hasAccess' => false,
                'status' => 'We could not log you in. Please contact RequestIT.',
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
                \Log::info('We got a token: ' . $token);
                \Log::info('$provider_user: ' . json_encode($provider_user));
            } catch (\Exception $e) {
                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Failed to get resource owner: '.$e->getMessage(),
                ]);
            }

            $user = null;
            if($type === Role::Ministry_GUEST){
                $user = User::where('idir_user_guid', 'ilike', $provider_user['idir_user_guid'])->first();
            }
            if($type === Role::Institution_GUEST){
                $user = User::where('bceid_user_guid', 'ilike', $provider_user['bceid_user_guid'])->first();
            }

            //if it is a new IDIR user, register the user first
            if (is_null($user)) {
                $this->newUser($provider_user, $type);

                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Please contact Admin to grant you access.',
                ]);

                //if the user has been disabled
            } elseif ($user->disabled === true) {
                return Inertia::render('Auth/Login', [
                    'loginAttempt' => true,
                    'hasAccess' => false,
                    'status' => 'Access denied. Please contact Admin.',
                ]);
            }

            //else the user has access
            Auth::login($user);
            if($type === Role::Ministry_GUEST){
                return Redirect::route('ministry.home');
            }
            if($type === Role::Institution_GUEST){
                return Redirect::route('institution.home');
            }

            return Redirect::route('home');
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
     * Display first page after login (dashboard page)
     */
    public function dashboard(Request $request)
    {
        return Inertia::render('Yeaf/Students');
    }

    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function login(Request $request)
    {
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function newUser($provider_user, $type)
    {
        $user = new User();
        $user->guid = Str::orderedUuid()->getHex();
        $user->first_name = $provider_user['given_name'];
        $user->last_name = $provider_user['family_name'];
        $user->email = $provider_user['email'];
        $user->disabled = false;
        $user->idir_user_guid = $provider_user['idir_user_guid'] ?? null;
        $user->bceid_user_guid = $provider_user['bceid_user_guid'] ?? null;
        $user->password = Hash::make($provider_user['email']);
        $user->save();
        $this->checkRoles($user, $type);

    }

    //new user to be assigned as guest
    private function checkRoles($user, $type)
    {
        if(is_null($user->roles()->first()))
        {
            $role = Role::where('name', $type)->first();
            $user->roles()->attach($role);
        }
    }
}
