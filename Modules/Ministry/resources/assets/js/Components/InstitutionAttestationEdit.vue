<template>
    <div class="modal-content">

        <div class="modal-header">
            <h5 v-if="attestation.status === 'Issued'" class="modal-title" id="editAtteModalLabel1">Download Attestation</h5>
            <h5 v-else class="modal-title" id="editAtteModalLabel">Edit Attestation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form v-if="editAtteForm != null" class="card-body p-0">
            <div class="modal-body">
                <div v-if="attestation.status === 'Issued' || attestation.status === 'Cancelled Draft'" class="row g-3">
                    <div class="col-md-6 text-break">
                        <Label for="inputFirstName" class="fw-bold" value="First Name"/>
                        {{ editAtteForm.first_name }}
                    </div>
                    <div class="col-md-6 text-break">
                        <Label for="inputLastName" class="fw-bold" value="Last Name"/>
                        {{ editAtteForm.last_name }}
                    </div>
                    <div class="col-md-12 text-break">
                        <Label for="inputAddress1" class="fw-bold" value="Address 1"/>
                        {{ editAtteForm.address1 }}
                    </div>
                    <div class="col-md-12 text-break">
                        <Label for="inputAddress2" class="fw-bold" value="Address 2"/>
                        {{ editAtteForm.address2 }}
                    </div>

                    <div class="col-md-4 text-break">
                        <Label for="inputProgram" class="fw-bold" value="Institution Program"/>
                        {{ $getProgramNameFromGuid(institution.programs, editAtteForm.program_guid) }}
                    </div>
                    <div class="col-md-4 text-break">
                        <Label for="inputStudentNumber" class="fw-bold" value="Student Number"/>
                        {{ editAtteForm.student_number }}
                    </div>
                    <div class="col-md-4 text-break">
                        <Label for="inputStudentId" class="fw-bold" value="Passport/Travel Doc. ID"/>
                        {{ editAtteForm.id_number }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputDob" class="fw-bold" value="Date of Birth"/>
                        {{ editAtteForm.dob }}
                    </div>

                    <div class="col-md-3 text-break">
                        <Label for="inputInPerson" class="fw-bold" value="> 50% in-person?"/>
                        {{ $getYesNo(editAtteForm.gt_fifty_pct_in_person) }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputEmail" class="fw-bold" value="Email"/>
                        {{ editAtteForm.email }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputCity" class="fw-bold" value="City"/>
                        {{ editAtteForm.city }}
                    </div>

                    <div class="col-md-3 text-break">
                        <Label for="inputZipCode" class="fw-bold" value="Postal Code"/>
                        {{ editAtteForm.zip_code }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputProvince" class="fw-bold" value="Province / State"/>
                        {{ editAtteForm.province }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputCountry" class="fw-bold" value="Country"/>
                        {{ editAtteForm.country }}
                    </div>
                    <div class="col-md-3 text-break">
                        <Label for="inputExpiryDate" class="fw-bold" value="Expiry Date"/>
                        {{ editAtteForm.expiry_date }}
                    </div>
                    <div class="col-md-12 text-break">
                        <Label for="studentConfirmationCheckbox" class="fw-bold" value="Student Confirmation"/>
                        {{ editAtteForm.student_confirmation ? 'Yes' : 'No' }}
                    </div>

                    <a v-if="attestation.status === 'Issued'" :href="'/ministry/attestations/download/' + attestation.id" target="_blank" class="btn btn-lg btn-outline-secondary mb-3">
                        {{attestation.issued_by_name}}<br/><i class="bi bi-box-arrow-down"></i>
                    </a>
                </div>
                <div v-else class="row g-3">
                    <div class="col-md-6">
                        <Label for="inputFirstName" class="form-label" value="First Name" required="true"/>
                        <Input type="text" class="form-control" id="inputFirstName" v-model="editAtteForm.first_name"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-6">
                        <Label for="inputLastName" class="form-label" value="Last Name" required="true"/>
                        <Input type="text" class="form-control" id="inputLastName" v-model="editAtteForm.last_name"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-12">
                        <Label for="inputAddress1" class="form-label" value="Address 1" required="true"/>
                        <Input type="text" class="form-control" id="inputAddress1" v-model="editAtteForm.address1"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-12">
                        <Label for="inputAddress2" class="form-label" value="Address 2"/>
                        <Input type="text" class="form-control" id="inputAddress2" v-model="editAtteForm.address2"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>

                    <div class="col-md-4">
                        <Label for="inputProgram" class="form-label" value="Institution Program" required="true"/>
                        <Select class="form-select" id="inputProgram" v-model="editAtteForm.program_guid" :disabled="institution === ''">
                            <template v-if="institution !== ''">
                                <option></option>
                                <template v-for="c in institution.programs">
                                    <option v-if="c.active_status" :value="c.guid">{{ c.program_name}}</option>
                                </template>
                            </template>
                        </Select>
                    </div>
                    <div class="col-md-4">
                        <Label for="inputStudentNumber" class="form-label" value="Student Number"/>
                        <Input type="text" class="form-control" id="inputStudentNumber" v-model="editAtteForm.student_number"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-4">
                        <Label for="inputStudentId" class="form-label" value="Passport/Travel Doc. ID"/>
                        <Input type="text" class="form-control" id="inputStudentId" v-model="editAtteForm.id_number"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-3">
                        <Label for="inputDob" class="form-label" value="Date of Birth" required="true"/>
                        <Input type="date" min="1930-01-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                               class="form-control" id="inputDob" v-model="editAtteForm.dob"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>

                    <div class="col-md-3">
                        <Label for="inputInPerson" class="form-label" value="> 50% in-person?" required="true"/>
                        <Select class="form-select" id="inputInPerson" v-model="editAtteForm.gt_fifty_pct_in_person" :disabled="institution === ''">
                            <option></option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </Select>
                    </div>
                    <div class="col-md-3">
                        <Label for="inputEmail" class="form-label" value="Email" required="true"/>
                        <Input type="email" class="form-control" id="inputEmail" v-model="editAtteForm.email"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-3">
                        <Label for="inputCity" class="form-label" value="City" required="true"/>
                        <Input type="text" class="form-control" id="inputCity" v-model="editAtteForm.city"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>

                    <div class="col-md-3">
                        <Label for="inputZipCode" class="form-label" value="Postal Code"/>
                        <Input type="text" class="form-control" id="inputZipCode" v-model="editAtteForm.zip_code"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-3">
                        <Label for="inputProvince" class="form-label" value="Province / State"/>
                        <Input type="text" class="form-control" id="inputProvince" v-model="editAtteForm.province"
                               :disabled="editAtteForm.program_guid === ''"/>
                    </div>
                    <div class="col-md-3">
                        <Label for="inputCountry" class="form-label" value="Country" required="true"/>
                        <input type="text" class="form-control" list="datalistOptionsInputCountry" id="inputCountry"
                               placeholder="Type to search..."  v-model="editAtteForm.country"
                               :disabled="editAtteForm.program_guid === ''" />
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
                            <input class="form-check-input" type="checkbox" v-model="editAtteForm.student_confirmation" id="studentConfirmationCheckbox">
                            <label class="form-check-label" for="studentConfirmationCheckbox">
                                We confirm that the applicant has been informed that the personal information contained in this application will be shared with Immigration, Refugee and Citizenship Canada and British Columbia’s Ministry of Post-Secondary Education and Future Skills for operational and program evaluation purposes.
                            </label>
                        </div>
                    </div>

                    <div v-if="editAtteForm.errors != undefined" class="row">
                        <div class="col-12">
                            <div v-if="editAtteForm.hasErrors == true" class="alert alert-danger mt-3">
                                <ul>
                                    <li v-for="err in editAtteForm.errors">{{ err }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div v-if="attestation.status === 'Draft'" class="modal-footer justify-content-between">
                <button @click="submitForm('Issued')" type="button" class="btn btn-sm btn-secondary" :disabled="editAtteForm.processing">
                    Issue Attestation
                </button>
                <button @click="submitForm('Draft')" type="button" class="btn btn-sm btn-success" :disabled="editAtteForm.processing">
                    Save Draft Attestation
                </button>
            </div>
            <FormSubmitAlert :form-state="editAtteForm.formState" :success-msg="editAtteForm.formSuccessMsg"
                             :fail-msg="editAtteForm.formFailMsg"></FormSubmitAlert>
        </form>
    </div>
</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionAttestationEdit',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        attestation: Object,
        institution: Object,
        countries: Object,
        cap: Object
    },
    data() {
        return {
            editAtteForm: null,
            editAtteFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                id: "",
                institution_guid: "",
                cap_guid: "",
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
                expiry_date: "",
                gt_fifty_pct_in_person: ""
            },
        }
    },
    methods: {
        submitForm: function (status) {
            let vm = this;

            // Reset error messages
            this.editAtteForm.errors = [];
            this.editAtteForm.hasErrors = false;

            this.editAtteForm.status = status;

            // Student confirmation (checkbox) is required for issuing an attestation.
            if ((this.editAtteForm.status === 'Issued') && (!this.editAtteForm.student_confirmation)) {
                this.editAtteForm.errors.push('Please confirm that the applicant has been informed that the personal information contained in this application will be shared.');
                this.editAtteForm.hasErrors = true;
                return;
            }

            if(this.editAtteForm.status !== 'Draft'){
                let check = confirm('You are about to Save & Lock this record. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }

            this.editAtteForm.formState = null;
            this.editAtteForm.put('/ministry/attestations/institution', {
                onSuccess: (response) => {
                    $("#editAtteModal").modal('hide');
                    this.editAtteForm.reset(this.editAtteFormData);
                    this.$inertia.visit('/ministry/institutions/' + this.institution.id + '/attestations');
                },
                onError: () => {
                    this.editAtteForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.editAtteForm = useForm(this.attestation);
    }
}
</script>
