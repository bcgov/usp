<?php

namespace Modules\Institution\App\Http\Requests;

use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AttestationEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(Session::has('read-only')) return false;
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
            'fed_cap_guid' => 'required|exists:fed_caps,guid',
            'institution_guid' => 'required|exists:institutions,guid',
            'program_guid' => 'required|exists:programs,guid',
            'program_name' => 'required|exists:programs,program_name',
            'first_name' => 'required',
            'last_name' => 'required',
            'id_number' => 'nullable',
            'student_number' => 'nullable',
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
            'gt_fifty_pct_in_person' => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $user = User::find(Auth::user()->id);
        $program = Program::where('guid', $this->program_guid)->first();

        //get the inst active cap.
        $cap = Cap::where('institution_guid', $user->institution->guid)->active()->where('program_guid', null)->first();

        //now check if there is a cap against the program
        $progCap = Cap::where('institution_guid', $user->institution->guid)->active()->where('program_guid', $this->program_guid)->first();

        //if there is a program cap then use it as the cap_guid not the institution cap
        if (! is_null($progCap)) {
            $cap = $progCap;
        }

        $this->merge([
            'institution_guid' => $user->institution->guid,
            'cap_guid' => $cap->guid,
            'fed_cap_guid' => $cap->fed_cap_guid,
            'created_by_user_guid' => $this->user()->guid,
            'last_touch_by_user_guid' => $this->user()->guid,
            'id_number' => Str::upper($this->id_number),
            'student_number' => Str::upper($this->student_number),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'city' => Str::title($this->city),
            'country' => Str::title($this->country),
            'zip_code' => Str::upper($this->zip_code),
            'province' => Str::title($this->province),
            'gt_fifty_pct_in_person' => $this->toBoolean($this->gt_fifty_pct_in_person),
            'expiry_date' => $cap->end_date,
            'program_name' => $program->program_name,
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
