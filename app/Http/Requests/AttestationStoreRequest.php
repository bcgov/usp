<?php

namespace App\Http\Requests;

use App\Models\Attestation;
use Illuminate\Foundation\Http\FormRequest;
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
            'last_touch_by_user_guid' => 'required:exists,users,guid',
            'created_by_user_guid' => 'required:exists,users,guid',
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
            'guid' => Str::orderedUuid()->getHex(),
            'created_by_user_guid' => $this->user()->guid,
            'last_touch_by_user_guid' => $this->user()->guid,
            'id_number' => Str::upper($this->id_number),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'city' => Str::title($this->city),
            'zip_code' => Str::upper($this->zip_code),
            'province' => Str::title($this->province),
            'status' => 'Draft'
        ]);
    }

}
