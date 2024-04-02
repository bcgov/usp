<?php

namespace App\Http\Requests;

use App\Models\Faq;
use Illuminate\Foundation\Http\FormRequest;

class FaqEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $faq = Faq::find($this->id);

        return $this->user()->can('update', $faq);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.*' => 'FAQ ID field is not valid.',
            'question.*' => 'Question field is not valid.',
            'answer.*' => 'Answer field is not valid.',
            'order.*' => 'Order field is not valid.',
            'active_status.*' => 'Active field is not valid.',
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
            'question' => 'required',
            'answer' => 'required',
            'order' => 'required|integer',
            'active_status' => 'required|boolean',

        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(['active_status' => $this->toBoolean($this->active_status)]);
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
