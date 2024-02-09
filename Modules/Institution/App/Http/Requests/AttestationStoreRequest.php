<?php

namespace Modules\Institution\App\Http\Requests;

use App\Models\Attestation;
use App\Models\Cap;
use App\Models\FedCap;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AttestationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Attestation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guid' => 'required|unique:institutions,guid',
            'institution_guid' => 'required|exists:institutions,guid',
            'cap_guid' => 'required|exists:caps,guid',
            'program_guid' => 'required|exists:programs,guid',
            'first_name' => 'required',
            'last_name' => 'required',
            'id_number' => 'nullable',
            'dob' => 'required|date_format:Y-m-d',
            'email' => 'required|email',
            'address1' => 'required',
            'address2' => 'nullable',
            'city' => 'required',
            'zip_code' => 'nullable',
            'province' => 'nullable',
            'country' => 'required',
            'expiry_date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:Draft,Issued,Received,Denied',
            'last_touch_by_user_guid' => 'required|exists:users,guid',
            'created_by_user_guid' => 'required|exists:users,guid',
            'gt_fifty_pct_in_person' => 'required|boolean'
        ];
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $fedCap = FedCap::where('status', 'Active')->first();
        $user = User::find(Auth::user()->id);
        $institution = $user->institution;
        $activeCaps = $institution->activeCaps;
        $now = now();
        $instCap = null;
        $instCaps = Cap::where('institution_guid', $institution->guid)
            ->where('fed_cap', $fedCap->guid)
            ->where('program_guid', null)
            ->where('status', 'Active')
            ->get();

        foreach ($instCaps as $cap){
            if($now->gte($cap->start_date) && $now->lte($cap->end_date)) {

            }
        }

        $instCap = null;
        $programCap = null;
        foreach ($activeCaps as $cap){
            //the cap must be active
            //now() must fall in between start/end dates
            //if the cap does not have a program_guid then this is the $instCap
            //if the cap has a program_guid matching $this->program_guid then this is $programCap

            // Check if the cap is active
            if ($cap->status === 'Active' &&
                // Check if the current time falls within the cap's start/end dates
                ($now->gte($cap->start_date) && $now->lte($cap->end_date))) {

                // Check if the cap does not have a program_guid
                if ($cap->program_guid === null) {
                    // This is the institution cap
                    $instCap = $cap;
                } elseif ($cap->program_guid === $this->program_guid) {
                    // This is the program cap matching $this->program_guid
                    $programCap = $cap;
                }
            }
        }
//        'institution_guid' => 'required|exists:institutions,guid',
//            'cap_guid' => 'required|exists:caps,guid',
//            'program_guid' => 'required|exists:programs,guid',

        $this->merge([
            'guid' => Str::orderedUuid()->getHex(),
            'institution_guid' => $institution->guid,
            'created_by_user_guid' => $this->user()->guid,
            'last_touch_by_user_guid' => $this->user()->guid,
            'id_number' => Str::upper($this->id_number),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'city' => Str::title($this->city),
            'zip_code' => Str::upper($this->zip_code),
            'province' => Str::title($this->province),
            'status' => 'Draft',
            'gt_fifty_pct_in_person' => $this->toBoolean($this->gt_fifty_pct_in_person),
        ]);

    }

    /**
     * Convert to boolean
     *
     * @return bool
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
