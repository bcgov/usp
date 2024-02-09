<?php

namespace App\Http\Requests;

use App\Models\InstitutionStaff;
use Illuminate\Foundation\Http\FormRequest;

class InstitutionStaffEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $institutionStaff = InstitutionStaff::find($this->id);
        return $this->user()->can('update', $institutionStaff);
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
            'status' => 'required|in:Active,Inactive,pending',
            'last_touch_by_user_guid' => 'required|exists:users,guid',
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
        ]);
    }
}
