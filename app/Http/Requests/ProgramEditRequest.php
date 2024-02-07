<?php

namespace App\Http\Requests;

use App\Models\Program;
use App\Models\Institution;
use Illuminate\Foundation\Http\FormRequest;

class ProgramEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $program = Program::find($this->id);
        return $this->user()->can('update', $program);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'guid' => 'required',
            'institution_guid' => 'required|exists:institutions,guid',
            'program_name' => 'required',
            'program_type' => 'required',
            'credential' => 'required',
            'total_duration_hrs' => 'nullable|numeric',
            'total_duration_weeks' => 'nullable|numeric',
            'tuition_domestic' => 'nullable|numeric',
            'tuition_international' => 'nullable|numeric',
            'work_experience_required' => 'nullable|boolean',
            'delivery_in_class' => 'nullable|boolean',
            'delivery_distance' => 'nullable|boolean',
            'delivery_combined' => 'nullable|boolean',
            'noc_code' => 'nullable',
            'cip_code' => 'nullable',
            'active' => 'required|boolean',
            'restrictions' => 'nullable',
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
            'active' => $this->toBoolean($this->active),
            'delivery_combined' => isset($this->delivery_combined) ? $this->toBoolean($this->delivery_combined) : null,
            'delivery_distance' => isset($this->delivery_distance) ? $this->toBoolean($this->delivery_distance) : null,
            'delivery_in_class' => isset($this->delivery_in_class) ? $this->toBoolean($this->delivery_in_class) : null,
            'work_experience_required' => isset($this->work_experience_required) ? $this->toBoolean($this->work_experience_required) : null,

            'last_touch_by_user_guid' => $this->user()->guid,
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
