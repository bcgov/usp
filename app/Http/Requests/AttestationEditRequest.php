<?php

namespace App\Http\Requests;

use Auth;
use App\Models\Attestation;
use App\Models\Cap;
use App\Models\Institution;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
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

    public function message()
    {
        return 'The :attribute must be a date before today.';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
            'student_confirmation' => 'boolean',
            'dob' => 'required|date_format:Y-m-d|before:today',
            'email' => 'required|email',
            'address1' => 'required',
            'address2' => 'nullable',
            'city' => 'required',
            'zip_code' => 'nullable',
            'province' => 'nullable',
            'country' => 'required',
            'expiry_date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:Draft,Issued,Received,Declined,Cancelled Draft',
            'last_touch_by_user_guid' => 'required|exists:users,guid',
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
        //use the program_guid to check for the cap_guid.
        $program = Program::where('guid', $this->program_guid)->first();
        //get the inst active cap.
        $inst = Institution::where('guid', $program->institution_guid)->active()->first();

        $cap = Cap::where('fed_cap_guid', Cache::get('global_fed_caps_' . Auth::id())['default'])->active()
            ->where('program_guid', null)
            ->where('institution_guid', $inst->guid)
            ->first();
//        $cap = Cap::where('institution_guid', $inst->guid)->active()->where('program_guid', null)->first();
        //now check if there is a cap against the program
        $progCap = Cap::where('institution_guid', $inst->guid)->active()->where('program_guid', $this->program_guid)->first();
        //if there is a program cap then use it as the cap_guid not the institution cap
        if (! is_null($progCap)) {
            $cap = $progCap;
        }

        //normalize name fields
        $first_name = $this->normalizeName($this->first_name);
        $last_name = $this->normalizeName($this->last_name);

        $this->merge([
            'last_touch_by_user_guid' => $this->user()->guid,
            'cap_guid' => $cap->guid,
            'fed_cap_guid' => $cap->fed_cap_guid,
            'id_number' => Str::upper($this->id_number),
            'student_number' => Str::upper($this->student_number),
            'student_confirmation' => $this->toBoolean($this->student_confirmation),
            'first_name' => Str::upper($first_name),
            'last_name' => Str::upper($last_name),
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

    /**
     * Normalize name fields, replacing 'n/a' or 'n\a' with '-' and removing slashes.
     *
     * @param string $name
     * @return string
     */
    private function normalizeName($name)
    {
        if (in_array(Str::lower($name), ['n/a', 'n\a'])) {
            return '-';
        }
        return str_replace(['\\', '/'], '', $name);
    }
}
