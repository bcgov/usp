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
        return [
            'id' => 'required',
            'guid' => 'required',
            'start_date' => 'required|unique:fed_caps,start_date,'.$this->id,
            'end_date' => 'required|unique:fed_caps,end_date,'.$this->id,
            'status' => 'required|in:Active,Completed,Cancelled',
            'total_attestations' => 'required|numeric',
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
        ]);
    }
}
