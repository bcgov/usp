<template>
    <form v-if="editInstitutionCapForm != null" class="card-body" @submit.prevent="submitForm">
        <div class="modal-body">
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <Label for="inputSd" class="form-label" value="Federal Cap"/>
                    <Select @change="updateFedCap" class="form-select" id="inputSd" v-model="editInstitutionCapForm.fed_cap_guid">
                        <option></option>
                        <option v-for="f in fedCaps" :value="f.guid">{{ f.start_date }} - {{ f.end_date}}</option>
                    </Select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <Label for="inputStatus" class="form-label" value="Status"/>
                    <Select class="form-select" id="inputStatus" v-model="editInstitutionCapForm.active_status">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>

                <div class="col-md-4">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <div class="input-group mb-3">
                        <Input type="number" class="form-control" id="inputTotalAtte" aria-describedby="basic-inputTotalAtte" @keyup="validateTotal" v-model="editInstitutionCapForm.total_attestations"/>
                        <span v-if="selectedFedCap != ''" class="input-group-text" id="basic-inputTotalAtte">/{{ remainingCap }}</span>
                    </div>
                    <div v-if="validationError" class="text-danger mt-1">{{ validationError }}</div>
                </div>

                <div class="col-md-4">
                    <Label for="inputTotalAtte" class="form-label" value="Total Reserved Graduate Attest. Allowed"/>
                    <div class="input-group mb-3">
                        <Input type="number" class="form-control" id="inputTotalResGradAtte" aria-describedby="basic-inputTotalResGradAtte" @keyup="validateTotalResGrad" v-model="newInstitutionCapForm.total_reserved_graduate_attestations"/>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div v-if="allowProgramCap && activeInstCap !== null" class="col-md-12">
                    <Label for="inputProgram" class="form-label" value="Institution Program (optional)"/>
                    <Select class="form-select" id="inputProgram" v-model="editInstitutionCapForm.program_guid">
                        <option value=""></option>
                        <option v-for="c in results.programs" :value="c.guid">{{ c.program_name}}</option>
                    </Select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comment"/>
                    <textarea class="form-control" id="inputComment" v-model="editInstitutionCapForm.comment" rows="3"></textarea>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <Label for="inputExternalComment" class="form-label" value="External Comment"/>
                    <textarea class="form-control" id="inputExternalComment" v-model="editInstitutionCapForm.external_comment" rows="3"></textarea>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div v-if="editInstitutionCapForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="editInstitutionCapForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in editInstitutionCapForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-success" :disabled="editInstitutionCapForm.processing">
                Update Institution Cap
            </button>
        </div>
        <FormSubmitAlert :form-state="editInstitutionCapForm.formState" :success-msg="editInstitutionCapForm.formSuccessMsg"
                         :fail-msg="editInstitutionCapForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionCapEdit',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        fedCaps: Object,
        results: Object,
        cap: Object,
        activeInstCap: Object|null
    },
    data() {
        return {
            editInstitutionCapForm: null,
            editInstitutionCapFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                institution_id: "",
                fed_cap_guid: "",
                program_guid: "",
                total_attestations: "",
                total_reserved_graduate_attestations: "",
                active_status: "",
                comment: "",
                external_comment: "",
            },
            selectedFedCap: '',
            allowProgramCap: false
        }
    },
    computed: {
        remainingCap() {
            if (this.selectedFedCap !== '') {
                let overAllocationPercentage = parseFloat(this.selectedFedCap.over_allocation_percentage);
                let remainingCap = Math.floor(this.selectedFedCap.remaining_cap + (this.selectedFedCap.total_attestations * overAllocationPercentage));
                return remainingCap;
            }
            return '';
        },
    },
    methods: {
        validateTotal: function (){
            this.validationError = null;
            if(this.selectedFedCap !== ''){
                let maxAllowedRemainingCap = this.remainingCap;
                if(parseInt(this.editInstitutionCapForm.total_attestations) > maxAllowedRemainingCap){
                    this.editInstitutionCapForm.total_attestations = maxAllowedRemainingCap;
                }

                // Ensure the input value not below issued/declined institution cap
                if (this.activeInstCap && this.selectedFedCap && this.activeInstCap.fed_cap_guid === this.selectedFedCap.guid) {
                    let minAllowedCap = parseInt(
                        (this.activeInstCap.inst_active_cap_stat && this.activeInstCap.inst_active_cap_stat.issued !== undefined)
                            ? this.activeInstCap.inst_active_cap_stat.issued
                            : (this.activeInstCap.issued_attestations || 0)
                        );

                    if (parseInt(this.editInstitutionCapForm.total_attestations) < minAllowedCap) {
                        this.validationError = 'Minimum Total Attest. Allowed is ' + minAllowedCap + '. (Total Issued + Declined Attestations)';
                    }
                }
            }
        },
        validateTotalResGrad: function (){
            if(this.selectedFedCap !== ''){
                if(parseInt(this.editInstitutionCapForm.total_reserved_graduate_attestations) > parseInt(this.editInstitutionCapForm.total_attestations) ){
                    this.editInstitutionCapForm.total_reserved_graduate_attestations = parseInt(this.editInstitutionCapForm.total_attestations) ;
                }
            }
        },
        updateFedCap: function (e){
            // Find the selected school by name
            const cap = this.fedCaps.find(fedCap => fedCap.id == e.target.value);

            // If found, update the form property
            if (cap) {
                this.selectedFedCap = cap;
            }
        },
        submitForm: function () {
            let vm = this;
            this.editInstitutionCapForm.formState = null;
            this.editInstitutionCapForm.put('/ministry/caps', {
                onSuccess: (response) => {
                    $("#editInstCapModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.editInstitutionCapForm.reset(vm.editInstitutionCapFormData);
                            vm.$inertia.visit('/ministry/institutions/' + vm.results.id + '/caps');
                            vm.$emit('close');
                        });
                },
                onError: () => {
                    this.editInstitutionCapForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.editInstitutionCapForm = useForm(this.cap);
        this.editInstitutionCapForm.institution_guid = this.results.guid;

        for(let i=0; i<this.fedCaps.length; i++){
            if(this.fedCaps[i].guid === this.editInstitutionCapForm.fed_cap_guid){
                this.editInstitutionCapForm.fed_cap_guid = this.fedCaps[i].guid;
                break;
            }
        }

    }
}
</script>
