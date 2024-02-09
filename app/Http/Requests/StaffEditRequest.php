<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StaffEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $staff = User::find($this->id);
        return $this->user()->can('update', $staff);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'disabled.*' => 'Status field is not valid.',
            'access_type.*' => 'Access Type field is not valid.',
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
            'access_type' => 'required|in:A,U,G',
            'disabled' => 'required|boolean',
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
