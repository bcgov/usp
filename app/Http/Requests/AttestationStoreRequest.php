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
            'student_name' => 'required',
            'student_id_number' => 'required',
            'student_dob' => 'required|date_format:Y-m-d',
            'expiry_date' => 'required|date_format:Y-m-d',
            'status' => 'required',
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
            'student_name' => Str::title($this->student_name),
            'status' => 'Issued'
        ]);
    }

}
