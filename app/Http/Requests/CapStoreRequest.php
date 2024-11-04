<?php

namespace App\Http\Requests;

use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Institution;
use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CapStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Cap::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guid' => 'required|unique:caps,guid',
            'fed_cap_guid' => 'required|exists:fed_caps,guid',
            'institution_guid' => 'required|exists:institutions,guid',
            'program_guid' => 'nullable|exists:programs,guid',
            'parent_cap_guid' => 'nullable|exists:caps,guid',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'active_status' => 'required|boolean',
            'total_attestations' => 'required|numeric',
            'total_reserved_graduate_attestations' => 'required|numeric|lte:total_attestations',
            'issued_attestations' => 'required|numeric',
            'issued_reserved_graduate_attestations' => 'required|numeric',
            'draft_attestations' => 'required|numeric',
            'draft_reserved_graduate_attestations' => 'required|numeric',
            'comment' => 'nullable',
            'external_comment' => 'nullable',
            'last_touch_by_user_guid' => 'required|exists:users,guid',
            'confirmed' => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $institution = Institution::find($this->institution_id);
        $fedCap = FedCap::find($this->fed_cap_id);
        $program = Program::find($this->program_id);
        $instCap = Cap::where('institution_guid', $institution->guid)
            ->where('fed_cap_guid', $fedCap->guid)
            ->where('program_guid', null)
            ->where('active_status', true)
            ->whereColumn('issued_attestations', '<', 'total_attestations')
            ->first();

        $this->merge([
            'program_guid' => $program?->guid,
            'parent_cap_guid' => $instCap?->guid,
            'start_date' => $fedCap->start_date,
            'end_date' => $fedCap->end_date,
            'fed_cap_guid' => $fedCap->guid,
            'institution_guid' => $institution->guid,
            'guid' => Str::orderedUuid()->getHex(),
            'last_touch_by_user_guid' => $this->user()->guid,
            'total_attestations' => ($this->total_attestations > $fedCap->total_attestations ?
                $fedCap->total_attestations : $this->total_attestations),
            'total_reserved_graduate_attestations' => ($this->total_reserved_graduate_attestations > $this->total_attestations ?
                $this->total_attestations : $this->total_reserved_graduate_attestations),
            'issued_attestations' => 0,
            'draft_attestations' => 0,
            'issued_reserved_graduate_attestations' => 0,
            'draft_reserved_graduate_attestations' => 0,
            'active_status' => $this->toBoolean($this->active_status),
            'confirmed' => $this->toBoolean($this->confirmed),
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
