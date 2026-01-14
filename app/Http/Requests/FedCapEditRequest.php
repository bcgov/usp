<?php

namespace App\Http\Requests;

use App\Models\FedCap;
use Illuminate\Foundation\Http\FormRequest;

class FedCapEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $fedCap = FedCap::find($this->id);

        return $this->user()->can('update', $fedCap);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $totalAttestations = array_sum(array_column($this->caps, 'issued_attestations'));
        $totalGradAttestations = array_sum(array_column($this->caps, 'issued_reserved_graduate_attestations'));

        return [
            'id' => 'required',
            'guid' => 'required',
            'start_date' => 'required|unique:fed_caps,start_date,'.$this->id,
            'end_date' => 'required|unique:fed_caps,end_date,'.$this->id,
            'status' => 'required|in:Active,Completed,Cancelled',
            'total_attestations' => 'required|numeric|gte:'.$totalAttestations,
            'total_reserved_graduate_attestations' => 'required|numeric|gte:'.$totalGradAttestations,
            'over_allocation_percentage' => 'numeric|min:0.0|max:0.05',
            'comment' => 'nullable',
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
            'over_allocation_percentage' => $this->over_allocation_percentage ?? 0,
        ]);
    }
}
