<?php

namespace App\Http\Requests;

use App\Models\FedCap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class FedCapStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', FedCap::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guid' => 'required|unique:fed_caps,guid',
            'start_date' => 'required|date_format:Y-m-d|unique:fed_caps,start_date',
            'end_date' => 'required|date_format:Y-m-d|unique:fed_caps,end_date',
            'status' => 'required|in:Active,Completed,Cancelled',
            'total_attestations' => 'required|numeric',
            'total_reserved_graduate_attestations' => 'required|numeric|lte:total_attestations',
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
            'guid' => Str::orderedUuid()->getHex(),
            'last_touch_by_user_guid' => $this->user()->guid,
            'over_allocation_percentage' => $this->over_allocation_percentage ?? 0,
        ]);
    }
}
