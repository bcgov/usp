<?php

namespace App\Http\Requests;

use App\Models\Attestation;
use Illuminate\Foundation\Http\FormRequest;
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cap_guid' => 'required|exists:caps,guid',
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
        $this->merge([
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
