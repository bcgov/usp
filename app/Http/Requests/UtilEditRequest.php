<?php

namespace App\Http\Requests;

use App\Models\Util;
use Illuminate\Foundation\Http\FormRequest;

class UtilEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $util = Util::find($this->id);

        return $this->user()->can('update', $util);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.*' => 'Utility ID field is not valid.',
            'field_name.*' => 'Title is not valid.',
            'field_type.*' => 'Type is not valid.',
            'active_flag.*' => 'Active is not valid.',
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
            'field_name' => 'required',
            'field_type' => 'required',
            'field_description' => 'nullable',
            'active_flag' => 'required|boolean',

        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(['active_flag' => $this->toBoolean($this->active_flag)]);
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
