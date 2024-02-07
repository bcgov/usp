<template>
    <form v-if="editAtteForm != null" class="card-body" @submit.prevent="submitForm">
        <div class="modal-body">
            <div v-if="attestation.status !== 'Draft'" class="text-center">
                <a :href="'/ministry/attestations/download/' + attestation.id" target="_blank" class="btn btn-lg btn-outline-secondary mb-3">
                    <i class="bi bi-box-arrow-down"></i>
                </a>
                <p>Download Attestation</p>
            </div>
            <div v-else class="row g-3">

                <div class="col-md-4">
                    <Label for="inputCap" class="form-label" value="Institution Cap"/>
                    <Select class="form-select" id="inputCap" v-model="editAtteForm.cap_guid" :disabled="institution === ''">
                        <option></option>
                        <option v-for="c in institution.active_caps" :value="c.guid">{{ c.start_date }} - {{ c.end_date}}</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputProgram" class="form-label" value="Institution Program"/>
                    <Select class="form-select" id="inputProgram" v-model="editAtteForm.program_guid" :disabled="institution === ''">
                        <template v-if="institution !== ''">
                            <option></option>
                            <option v-for="c in institution.programs" :value="c.guid">{{ c.program_name}}</option>
                        </template>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputStatus" class="form-label" value="Status"/>
                    <Select class="form-select" id="inputStatus" v-model="editAtteForm.status" :disabled="institution === ''">
                        <template v-if="institution !== ''">
                            <option v-for="stat in $attrs.utils['Attestation Status']" :value="stat.field_name">{{ stat.field_name }}</option>
                        </template>
                    </Select>
                </div>

                <div class="col-md-4">
                    <Label for="inputFirstName" class="form-label" value="First Name"/>
                    <Input type="text" class="form-control" id="inputFirstName" v-model="editAtteForm.first_name"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputLastName" class="form-label" value="Last Name"/>
                    <Input type="text" class="form-control" id="inputLastName" v-model="editAtteForm.last_name"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputStudentId" class="form-label" value="Passport/Travel Doc. ID"/>
                    <Input type="text" class="form-control" id="inputStudentId" v-model="editAtteForm.id_number"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputDob" class="form-label" value="Date of Birth"/>
                    <Input type="date" min="1930-01-01" max="2020-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputDob" v-model="editAtteForm.dob"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputAddress1" class="form-label" value="Address 1"/>
                    <Input type="text" class="form-control" id="inputAddress1" v-model="editAtteForm.address1"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputAddress2" class="form-label" value="Address 2"/>
                    <Input type="text" class="form-control" id="inputAddress2" v-model="editAtteForm.address2"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputEmail" class="form-label" value="Email"/>
                    <Input type="email" class="form-control" id="inputEmail" v-model="editAtteForm.email"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputCity" class="form-label" value="City"/>
                    <Input type="text" class="form-control" id="inputCity" v-model="editAtteForm.city"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputZipCode" class="form-label" value="Zip Code"/>
                    <Input type="text" class="form-control" id="inputZipCode" v-model="editAtteForm.zip_code"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputProvince" class="form-label" value="Province / State"/>
                    <Input type="text" class="form-control" id="inputProvince" v-model="editAtteForm.province"
                           :disabled="editAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputCountry" class="form-label" value="Country"/>
                    <input type="text" class="form-control" list="datalistOptionsInputCountry" id="inputCountry"
                           placeholder="Type to search..."  v-model="editAtteForm.country"
                           :disabled="editAtteForm.program_guid === ''" />
                    <datalist id="datalistOptionsInputCountry">
                        <option v-for="cntry in countries" :value="cntry.name">{{ cntry.name }}</option>
                    </datalist>
                </div>
                <div class="col-md-4">
                    <Label for="inputExpiryDate" class="form-label" value="Expiry Date"/>
                    <Input type="date" min="2024-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputExpiryDate" v-model="editAtteForm.expiry_date"
                           :disabled="editAtteForm.program_guid === ''"/>
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
        <div v-if="attestation.status === 'Draft'" class="modal-footer">
            <button type="submit" class="btn me-2 btn-outline-success float-end" :disabled="editAtteForm.processing">
                Save Attestation
            </button>
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
    name: 'InstitutionAttestationEdit',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        attestation: Object,
        institution: Object,
        countries: Object
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
                dob: "",
                address1: "",
                address2: "",
                email: "",
                city: "",
                zip_code: "",
                province: "",
                country: "",
                status: "",
                expiry_date: ""
            },
        }
    },
    methods: {
        submitForm: function () {
            if(this.editAtteForm.status !== 'Draft'){
                let check = confirm('You are about to Save & Lock this record. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }

            this.editAtteForm.formState = null;
            this.editAtteForm.put('/ministry/attestations', {
                onSuccess: (response) => {
                    $("#editAtteModal").modal('hide');

                    this.editAtteForm.reset(this.editAtteFormData);
                    this.$inertia.visit('/ministry/institutions/' + this.institution.id + '/attestations');
                    // console.log(response.props.institution)
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
        // this.editAtteForm.institution_guid = this.institution.guid;
    }
}
</script>
