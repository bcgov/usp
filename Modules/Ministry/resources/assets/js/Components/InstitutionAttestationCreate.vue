<template>
    <form v-if="newAtteForm != null" class="card-body">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputProgram" class="form-label" value="Institution Program" required="true"/>
                    <Select class="form-select" id="inputProgram" v-model="newAtteForm.program_guid" :disabled="institution === ''">
                        <template v-if="institution !== ''">
                            <option></option>
                            <template v-for="c in institution.programs">
                                <option v-if="c.active_status" :value="c.guid">{{ c.program_name}}</option>
                            </template>
                        </template>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputInPerson" class="form-label" value="> 50% in-person?" required="true"/>
                    <Select class="form-select" id="inputInPerson" v-model="newAtteForm.gt_fifty_pct_in_person" :disabled="newAtteForm.program_guid === ''">
                        <option></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputStudentNumber" class="form-label" value="Student Number"/>
                    <Input type="text" class="form-control" id="inputStudentNumber" v-model="newAtteForm.student_number"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-6">
                    <Label for="inputFirstName" class="form-label" value="First Name" required="true"/>
                    <Input type="text" class="form-control" id="inputFirstName" v-model="newAtteForm.first_name"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-6">
                    <Label for="inputLastName" class="form-label" value="Last Name" required="true"/>
                    <Input type="text" class="form-control" id="inputLastName" v-model="newAtteForm.last_name"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-12">
                    <Label for="inputAddress1" class="form-label" value="Address 1" required="true"/>
                    <Input type="text" class="form-control" id="inputAddress1" v-model="newAtteForm.address1"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-12">
                    <Label for="inputAddress2" class="form-label" value="Address 2"/>
                    <Input type="text" class="form-control" id="inputAddress2" v-model="newAtteForm.address2"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label for="inputStudentId" class="form-label" value="Passport/Travel Doc. ID"/>
                    <Input type="text" class="form-control" id="inputStudentId" v-model="newAtteForm.id_number"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputDob" class="form-label" value="Date of Birth" required="true"/>
                    <Input type="date" min="1930-01-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputDob" v-model="newAtteForm.dob"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputEmail" class="form-label" value="Email" required="true"/>
                    <Input type="email" class="form-control" id="inputEmail" v-model="newAtteForm.email"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputCity" class="form-label" value="City" required="true"/>
                    <Input type="text" class="form-control" id="inputCity" v-model="newAtteForm.city"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label for="inputZipCode" class="form-label" value="Postal Code"/>
                    <Input type="text" class="form-control" id="inputZipCode" v-model="newAtteForm.zip_code"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputProvince" class="form-label" value="Province / State"/>
                    <Input type="text" class="form-control" id="inputProvince" v-model="newAtteForm.province"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputCountry" class="form-label" value="Country" required="true"/>
                    <input type="text" class="form-control" list="datalistOptionsInputCountry" id="inputCountry"
                           placeholder="Type to search..."  v-model="newAtteForm.country"  :disabled="newAtteForm.program_guid === ''" />
                    <datalist id="datalistOptionsInputCountry">
                        <option v-for="cntry in countries" :value="cntry.name">{{ cntry.name }}</option>
                    </datalist>
                </div>
                <div class="col-md-3">
                    <Label for="inputExpiryDate" class="form-label" value="Expiry Date"/>
                    <Input type="text"
                           class="form-control" id="inputExpiryDate" v-model="cap.end_date"
                           disabled readonly/>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="newAtteForm.student_confirmation" id="studentConfirmationCheckbox">
                        <label class="form-check-label" for="studentConfirmationCheckbox">
                            We confirm that the applicant has been informed that the personal information contained in this application will be shared with Immigration, Refugee and Citizenship Canada and British Columbia’s Ministry of Post-Secondary Education and Future Skills for operational and program evaluation purposes.
                        </label>
                    </div>
                </div>

                <div v-if="newAtteForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="newAtteForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in newAtteForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer justify-content-between">
            <button @click="submitForm('Issued')" type="button" class="btn btn-sm btn-secondary" :disabled="newAtteForm.processing">
                Issue Attestation
            </button>
            <button @click="submitForm('Draft')" type="button" class="btn btn-sm btn-success" :disabled="newAtteForm.processing">
                Save Draft Attestation
            </button>
        </div>
        <FormSubmitAlert :form-state="newAtteForm.formState" :success-msg="newAtteForm.formSuccessMsg"
                         :fail-msg="newAtteForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionAttestationCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        newAtte: Object|null,
        institution: Object,
        countries: Object,
        cap: Object
    },
    data() {
        return {
            newAtteForm: null,
            newAtteFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                institution_guid: "",
                program_guid: "",
                first_name: "",
                last_name: "",
                id_number: "",
                student_number: "",
                student_confirmation: false,
                dob: "",
                address1: "",
                address2: "",
                email: "",
                city: "",
                zip_code: "",
                province: "",
                country: "",
                status: "",
                expiry_date: this.cap.end_date,
                gt_fifty_pct_in_person: ""
            },
        }
    },
    methods: {
        submitForm: function (status) {

            // Reset error messages
            this.newAtteForm.errors = [];
            this.newAtteForm.hasErrors = false;

            this.newAtteForm.status = status;

            // Student confirmation (checkbox) is required for issuing an attestation.
            if ((this.newAtteForm.status !== 'Draft') && (!this.newAtteForm.student_confirmation)) {
                this.newAtteForm.errors.push('Please confirm that the applicant has been informed that the personal information contained in this application will be shared.');
                this.newAtteForm.hasErrors = true;
                return;
            }

            if(this.newAtteForm.status !== 'Draft'){
                let check = confirm('You are about to Save & Lock this record. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }
            this.newAtteForm.formState = null;
            this.newAtteForm.post('/ministry/attestations/institution', {
                onSuccess: (response) => {
                    $("#newAtteModal").modal('hide');
                    this.newAtteForm.reset(this.newAtteFormData);
                    this.$inertia.visit('/ministry/institutions/' + this.institution.id + '/attestations');

                },
                onError: () => {
                    this.newAtteForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.newAtteForm = useForm(this.newAtteFormData);
        this.newAtteForm.institution_guid = this.institution.guid;
    }
}
</script>
