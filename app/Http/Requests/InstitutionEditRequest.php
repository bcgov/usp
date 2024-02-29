<?php

namespace App\Http\Requests;

use App\Models\Institution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class InstitutionEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $institution = Institution::find($this->id);

        // Check if the authenticated user has the necessary permissions to edit the institution.
        // You can access the authenticated user using the Auth facade or $this->user() method.
        return $this->user()->can('update', $institution);
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
            'id' => 'required',
            'guid' => 'required',
            'dli' => 'required|unique:institutions,dli,'.$this->id,
            'name' => 'required|unique:institutions,name,'.$this->id,
            //'bceid_business_guid' => 'required|unique:institutions,bceid_business_guid,'.$this->id,
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
            'last_touch_by_user_guid' => 'required|exists:users,guid',
            'comment' => 'nullable',
            'info_sharing_agreement' => 'required|boolean',
            'category' => 'nullable|exists:utils,field_name',
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
            'active_status' => $this->toBoolean($this->active_status),
            'public' => $this->toBoolean($this->public),
            'last_touch_by_user_guid' => $this->user()->guid,
            'postal_code' => Str::upper(str_replace(' ', '', $this->postal_code)),
            'primary_email' => Str::lower(str_replace(' ', '', $this->primary_email)),
            'info_sharing_agreement' => $this->toBoolean($this->info_sharing_agreement),
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
