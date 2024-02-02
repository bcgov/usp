<?php

namespace App\Http\Requests;

use App\Models\Cap;
use App\Models\FedCap;
use App\Models\Institution;
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
        $fedCap = FedCap::find($this->fed_cap_id);
        $institution = Institution::find($this->institution_id);
        $this->merge([
            'start_date' => $fedCap->start_date,
            'end_date' => $fedCap->end_date,
            'fed_cap_guid' => $fedCap->guid,
            'institution_guid' => $institution->guid,
            'guid' => Str::orderedUuid()->getHex(),
            'last_touch_by_user_guid' => $this->user()->guid,
            'total_attestations' => ($this->total_attestations > $fedCap->total_attestations ?
                $fedCap->total_attestations : $this->total_attestations),
        ]);
    }
}
