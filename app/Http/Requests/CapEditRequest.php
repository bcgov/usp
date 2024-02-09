<?php

namespace App\Http\Requests;

use App\Models\Attestation;
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
        $fedCap = FedCap::where('guid', $this->fed_cap_guid)->first();
        $institution = Institution::where('guid', $this->institution_guid)->first();

        //if it's an inst limit, the edited limit cannot be less than what's already issued under the inst cap and all children programs.
        //the edited limit cannot be less than the inst cap BECAUSE:
        // now you have to edit any program caps that could be greater than the new limit.
        // also, editing children limits requires checking for issued attestations so the new limit for the children does not go below the number of issued.
        if(is_null($this->program_guid)) {
            $instCapGuids = Cap::select('guid')
                ->where('institution_guid', $institution->guid)
                ->where('fed_cap', $fedCap->guid)
                ->where('status', 'Active')
                ->where('issued_attestations', '<', 'total_attestations')
                ->get();
            $noAttes = Attestation::whereIn('cap_guid', $instCapGuids)->count();
        }

        //if it's a program limit, the edited limit cannot be less than what's already issued of attestations for that program
        //the edited limit cannot be more than the inst cap
        else{
            $instCap = Cap::select('total_attestations')->where('guid', $this->parent_cap_guid)->first();
            $noAttes = Attestation::where('cap_guid', $this->guid)->count();

            $noAttes = $noAttes > $instCap->total_attestations ? $instCap->total_attestations : $noAttes;
        }


        return [
            'id' => 'required',
            'guid' => 'required',
            'fed_cap_guid' => 'required|exists:fed_caps,guid',
            'institution_guid' => 'required|exists:institutions,guid',
            'program_guid' => 'nullable|exists:programs,guid',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:Active,Pending',
            'total_attestations' => 'required|numeric|gt:' . $noAttes,
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
        $fedCap = FedCap::where('guid', $this->fed_cap_guid)->first();
        $institution = Institution::where('guid', $this->institution_guid)->first();
        $program = Program::where('guid', $this->program_guid)->first();
        $instCap = Cap::where('institution_guid', $institution->guid)
            ->where('fed_cap', $fedCap->guid)
            ->where('program_guid', null)
            ->where('status', 'Active')
            ->where('issued_attestations', '<', 'total_attestations')
            ->first();


        $this->merge([
            'program_guid' => $program?->guid,
            'parent_cap_guid' => $instCap?->guid,
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
