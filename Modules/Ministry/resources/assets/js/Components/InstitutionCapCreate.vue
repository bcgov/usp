<template>
    <form v-if="newInstitutionCapForm != null" class="card-body">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputSd" class="form-label" value="Federal Cap"/>
                    <Select @change="updateFedCap" class="form-select" id="inputSd" v-model="newInstitutionCapForm.fed_cap_id">
                        <option></option>
                        <option v-for="f in fedCaps" :value="f.id">{{ f.start_date }} - {{ f.end_date}}</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputStatus" class="form-label" value="Active?"/>
                    <Select class="form-select" id="inputStatus" v-model="newInstitutionCapForm.active_status">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>

                <div class="col-md-4">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <div class="input-group mb-3">
                        <Input type="number" class="form-control" id="inputTotalAtte" aria-describedby="basic-inputTotalAtte" @keyup="validateTotal" v-model="newInstitutionCapForm.total_attestations"/>
                        <span v-if="selectedFedCap != ''" class="input-group-text" id="basic-inputTotalAtte">/{{ selectedFedCap.remaining_cap }}</span>
                    </div>
                </div>
                <div v-if="allowProgramCap && activeInstCap !== null" class="col-md-12">
                    <Label for="inputProgram" class="form-label" value="Institution Program (optional)"/>
                    <Select class="form-select" id="inputProgram" v-model="newInstitutionCapForm.program_id">
                        <option value=""></option>
                        <option v-for="c in results.programs" :value="c.id">{{ c.program_name}}</option>
                    </Select>
                </div>

                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comment"/>
                    <textarea class="form-control" id="inputComment" v-model="newInstitutionCapForm.comment" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <Label for="inputExternalComment" class="form-label" value="External Comment"/>
                    <textarea class="form-control" id="inputExternalComment" v-model="newInstitutionCapForm.external_comment" rows="3"></textarea>
                </div>

                <div v-if="newInstitutionCapForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="newInstitutionCapForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in newInstitutionCapForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button @click="submitForm" type="button" class="btn me-2 btn-outline-success float-end" :disabled="newInstitutionCapForm.processing">
                Create Institution Cap
            </button>
        </div>
        <FormSubmitAlert :form-state="newInstitutionCapForm.formState" :success-msg="newInstitutionCapForm.formSuccessMsg"
                         :fail-msg="newInstitutionCapForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionCapCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        fedCaps: Object,
        results: Object,
        activeInstCap: Object|null
    },
    data() {
        return {
            newInstitutionCapForm: null,
            newInstitutionCapFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                institution_id: "",
                fed_cap_id: "",
                program_id: "",
                total_attestations: "",
                active_status: "",
                comment: "",
                external_comment: ""
            },
            selectedFedCap: '',
            allowProgramCap: false
        }
    },
    methods: {
        validateTotal: function (){
            if(this.selectedFedCap !== ''){
                if(parseInt(this.newInstitutionCapForm.total_attestations) > this.selectedFedCap.remaining_cap){
                    this.newInstitutionCapForm.total_attestations = this.selectedFedCap.remaining_cap;
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
            let check = confirm('You are about to create a new Institution Cap. The new amount will override the previous cap and disable the old active Institution Cap. Are you sure you want to continue?');
            if(!check){
                return false;
            }
            if(this.newInstitutionCapForm.fed_cap_id === '' || this.newInstitutionCapForm.total_attestations === ''){
                return false;
            }

            this.newInstitutionCapForm.formState = null;
            this.newInstitutionCapForm.post('/ministry/caps', {
                onSuccess: (response) => {
                    $("#newInstCapModal").modal('hide');
                    this.newInstitutionCapForm.reset(this.newInstitutionCapFormData);

                    this.$inertia.visit('/ministry/institutions/' + this.newInstitutionCapForm.institution_id + '/caps');
                    // console.log(response.props.institution)
                },
                onError: () => {
                    this.newInstitutionCapForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.newInstitutionCapForm = useForm(this.newInstitutionCapFormData);
        this.newInstitutionCapForm.institution_id = this.results.id;
    }
}
</script>
