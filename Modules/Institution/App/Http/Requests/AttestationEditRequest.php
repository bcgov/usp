<?php

namespace Modules\Institution\App\Http\Requests;

use App\Models\Attestation;
use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AttestationEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $attestation = Attestation::find($this->id);
        return $this->user()->can('update', $attestation);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cap_guid' => 'required|exists:caps,guid',
            'guid' => 'required|unique:institutions,guid',
            'institution_guid' => 'required|exists:institutions,guid',
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

        $capGuid = null;
        //check for a program cap
        $program = Program::where('guid', $this->program_guid)->with('cap')->first();
        if(!is_null($program->cap)){
            $capGuid = $program->cap->guid;
        }

        //if no program cap active, use the institution active cap
        else{
            $now = now();
            $activeCaps = Cap::where('institution_guid', $institution->guid)
                ->where('fed_cap_guid', $fedCap->guid)
                ->where('program_guid', null)
                ->whereColumn('start_date', '>=', $now)
                ->whereColumn('end_date', '<', $now)
                ->where('status', 'Active')
                ->get();

            foreach ($activeCaps as $cap){
                //the cap must be active
                //now() must fall in between start/end dates
                //if the cap does not have a program_guid then this is $instCap
                //if the cap has a program_guid matching $this->program_guid then this is $programCap

                // Check if the current time falls within the cap's start/end dates
                if ($cap->issued_attestations < $cap->total_attestations) {
                    $capGuid = $cap->guid;
                }
            }
        }

        $this->merge([
            'guid' => Str::orderedUuid()->getHex(),
            'institution_guid' => $institution->guid,
            'cap_guid' => $capGuid,
            'created_by_user_guid' => $this->user()->guid,
            'last_touch_by_user_guid' => $this->user()->guid,
            'id_number' => Str::upper($this->id_number),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'city' => Str::title($this->city),
            'zip_code' => Str::upper($this->zip_code),
            'province' => Str::title($this->province),
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
