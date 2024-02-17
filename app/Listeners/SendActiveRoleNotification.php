<?php

namespace App\Listeners;

use App\Events\StaffRoleChanged;
use App\Models\Role;

class SendActiveRoleNotification
{
    /**
     * Handle the event.
     */
    public function handle(StaffRoleChanged $event): void
    {
        // Get the user and new role from the event
        $user = $event->user;
        $newRole = $event->newRole;

        // do not send if the user is switched to guest or if it's an inactive user
        $rolesToCheck = [Role::Ministry_ADMIN, Role::Ministry_USER, Role::SUPER_ADMIN, Role::Institution_ADMIN, Role::Institution_USER];
        if ($user->roles()->pluck('name')->intersect($rolesToCheck)->isNotEmpty()
            && $user->disabled === false) {
            $request = [
                'template_id' => env('GCN_ROLE_UPDATE_MSG_TEMPLATE'),
                'email_address' => $user->email,
                'personalisation' => [
                    'name' => $user->first_name.' '.$user->last_name, 'role' => $newRole->name,
                ],
            ];
            $this->sendNotification($request);
        }

    }

    private function sendNotification($request)
    {
        $token = env('GCN_TOKEN');
        $header = ['Content-Type: application/json', 'Authorization: ApiKey-v1 '.$token];
        $curlOptions = [
            CURLOPT_URL => env('GCN_CURL_URL'),
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($request),
        ];

        $this->makeCurlCall($curlOptions);

        return null;
    }

    private function makeCurlCall($curlOptions)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $response = curl_exec($ch);

        $error = curl_error($ch);
        $result = ['header' => '',
            'body' => '',
            'curl_error' => '',
            'http_code' => '',
            'last_url' => ''];
        if ($error != '') {
            $result['curl_error'] = $error;
            curl_close($ch);

            return $result;
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        return $result;
    }
}
