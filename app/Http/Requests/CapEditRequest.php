<?php

namespace App\Http\Requests;

use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;

class CapEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $cap = Cap::find($this->id);
        return $this->user()->can('update', $cap);
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
            'fed_cap_guid' => 'required|exists:fed_caps,guid',
            'institution_guid' => 'required|exists:institutions,guid',
            'program_guid' => 'nullable|exists:programs,guid',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:Active,Pending',
            'total_attestations' => 'required|numeric',
            'comment' => 'nullable',
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
        $fedCap = FedCap::where('guid', $this->fed_cap_guid)->first();
        $institution = Institution::where('guid', $this->institution_guid)->first();
        $program = Program::where('guid', $this->program_guid)->first();
        $this->merge([
            'program_guid' => $program?->guid,
            'start_date' => $fedCap->start_date,
            'end_date' => $fedCap->end_date,
            'fed_cap_guid' => $fedCap->guid,
            'institution_guid' => $institution->guid,
            'last_touch_by_user_guid' => $this->user()->guid,
            'total_attestations' => ($this->total_attestations > $fedCap->total_attestations ?
                $fedCap->total_attestations : $this->total_attestations),
        ]);
    }
}
