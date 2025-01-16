<template>
    <form v-if="editAtteForm != null" class="card-body">
        <div class="modal-body">
            <div v-if="attestation.status !== 'Draft'" class="row g-3">

                <div class="col-md-4 text-break">
                    <Label for="inputProgram" class="fw-bold" value="Institution Program"/>
                    {{ $getProgramNameFromGuid(programs, editAtteForm.program_guid) }}
                </div>
                <div class="col-md-4 text-break">
                    <Label for="inputStudentNumber" class="fw-bold" value="Student Number"/>
                    {{ editAtteForm.student_number }}
                </div>
                <div class="col-md-4 text-break">
                    <Label for="inputStudentId" class="fw-bold" value="Passport/Travel Doc. ID"/>
                    {{ editAtteForm.id_number }}
                </div>

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
                    <Label for="inputInPerson" class="fw-bold" value="> 50% in-person?"/>
                    {{ $getYesNo(editAtteForm.gt_fifty_pct_in_person) }}
                </div>
                <div class="col-md-3 text-break">
                    <Label for="inputDob" class="fw-bold" value="Date of Birth"/>
                    {{ editAtteForm.dob }}
                </div>
                <div class="col-md-3 text-break">
                    <Label for="inputEmail" class="fw-bold" value="Email"/>
                    {{ editAtteForm.email }}
                </div>
                <div class="col-md-3 text-break">
                    <Label for="inputExpiryDate" class="fw-bold" value="Expiry Date"/>
                    {{ editAtteForm.expiry_date }}
                </div>
                <div class="col-md-12 text-break">
                    <Label for="studentConfirmationCheckbox" class="fw-bold" value="Student Confirmation"/>
                    {{ editAtteForm.student_confirmation ? 'Yes' : 'No' }}
                </div>

            </div>
            <div v-else class="row g-3">

                <div class="col-md-4">
                    <Label class="form-label" value="Institution Program" required="true"/>
                    <Select class="form-select" v-model="editAtteForm.program_guid">
                        <option></option>
                        <option v-for="c in programs" :value="c.guid">{{ c.program_name}} ({{ c.program_graduate ? 'Graduate' : 'Undergraduate' }})</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label class="form-label" value="Student Number"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.student_number"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label class="form-label" value="Passport/Travel Doc. ID"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.id_number"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-6">
                    <Label class="form-label" value="First Name" required="true"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.first_name"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-6">
                    <Label class="form-label" value="Last Name" required="true"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.last_name"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-12">
                    <Label class="form-label" value="Address 1" required="true"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.address1"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-12">
                    <Label class="form-label" value="Address 2"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.address2"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="City" required="true"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.city"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="Postal Code"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.zip_code"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Province / State"/>
                    <Input type="text" class="form-control" v-model="editAtteForm.province"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Country" required="true"/>
                    <input type="text" class="form-control" list="datalistOptionsInputCountry"
                           placeholder="Type to search..."  v-model="editAtteForm.country"  :disabled="editAtteForm.program_guid === ''" />
                    <datalist id="datalistOptionsInputCountry">
                        <option v-for="cntry in countries" :value="cntry.name">{{ cntry.name }}</option>
                    </datalist>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="> 50% in-person?" required="true"/>
                    <Select class="form-select" v-model="editAtteForm.gt_fifty_pct_in_person" :disabled="institution === ''">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Date of Birth" required="true"/>
                    <Input type="date" min="1930-01-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                           class="form-control" v-model="editAtteForm.dob"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Email" required="true"/>
                    <Input type="email" class="form-control" v-model="editAtteForm.email"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Expiry Date"/>
                    <Input type="text"
                           class="form-control" v-model="instCap.end_date"
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
            <button type="button" class="btn btn-danger btn-sm me-auto" @click="submitForm('Cancelled Draft')" :disabled="editAtteForm.processing">Delete Attestation</button>
            <button type="button" class="btn btn-secondary btn-sm" @click="submitForm('Draft')" :disabled="editAtteForm.processing">Save Draft</button>
            <button type="button" class="btn btn-success btn-sm" @click="submitForm('Issued')" :disabled="editAtteForm.processing">Issue Attestation</button>
        </div>
        <FormSubmitAlert :form-state="editAtteForm.formState" :success-msg="editAtteForm.formSuccessMsg"
                         :fail-msg="editAtteForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'AttestationEdit',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        attestation: Object,
        institution: Object,
        countries: Object,
        error: String|null,
        programs: Object,
        instCap: Object
    },
    data() {
        return {
            editAtteForm: null,
            editAtteFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                id: "",
                cap_guid: "",
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
                expiry_date: "",
                gt_fifty_pct_in_person: ""
            },
        }
    },
    methods: {

        submitForm: function (status) {

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

            if(this.editAtteForm.status === 'Issued'){
                let check = confirm('You are about to Save & Lock this record. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }

            if(this.editAtteForm.status === 'Cancelled Draft'){
                let check = confirm('You are about to Cancel this Draft. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }

            this.editAtteForm.formState = null;
            this.editAtteForm.put('/institution/attestations', {
                onSuccess: (response) => {
                    $("#editAtteModal").modal('hide');

                    this.editAtteForm.reset(this.editAtteFormData);
                    window.location.href = '/institution/attestations';
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
