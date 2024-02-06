<?php

namespace App\Http\Requests;

use App\Models\Institution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class InstitutionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Institution::class);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
        ];
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
            'dli' => 'required|unique:institutions,dli',
            'name' => 'required|unique:institutions,name',
            'bceid_business_guid' => 'required|unique:institutions,bceid_business_guid',
            'legal_name' => 'nullable',
            'address1' => 'required',
            'address2' => 'nullable',

            'primary_contact' => 'required',
            'primary_email' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'province' => 'required',
            'public' => 'required|boolean',
            'active_status' => 'required|boolean',
            'standing_status' => 'nullable',
            'last_touch_by_user_guid' => 'required:exists,users,guid',
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
            'active_status' => $this->toBoolean($this->active_status),
            'public' => $this->toBoolean($this->public),
            'last_touch_by_user_guid' => $this->user()->guid,
            'postal_code' => Str::upper(str_replace(' ', '', $this->postal_code)),
            'primary_email' => Str::lower(str_replace(' ', '', $this->primary_email)),
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

