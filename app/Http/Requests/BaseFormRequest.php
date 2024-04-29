<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * Prepare the data for validation by sanitizing inputs.
     */
    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Sanitize input data.
     */
    protected function sanitize()
    {
        $input = $this->all();
        $sanitized = array_map(function ($value) {
            // Apply sanitization logic here
            return is_string($value) ? strip_tags($value) : $value;
        }, $input);
        $this->replace($sanitized);
    }

    /**
     * Handle authorization for the request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
