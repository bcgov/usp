<template>
    <form :id="randomId" v-if="newAtteForm != null" class="card-body">
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <Label class="form-label" value="Institution Program" required="true"/>
                    <Select class="form-select" v-model="newAtteForm.program_guid">
                        <option></option>
                        <option v-for="c in programs" :value="c.guid">{{ c.program_name}}</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Student Number <strong v-if="duplicate" class="text-danger">DUPLICATE</strong></label>
                    <Input @focusout="checkDuplicate" type="text" class="form-control" v-model="newAtteForm.student_number"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label class="form-label" value="Passport/Travel Doc. ID"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.id_number"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-6">
                    <Label class="form-label" value="First Name" required="true"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.first_name"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-6">
                    <Label class="form-label" value="Last Name" required="true"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.last_name"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-12">
                    <Label class="form-label" value="Address 1" required="true"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.address1"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-12">
                    <Label class="form-label" value="Address 2"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.address2"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="City" required="true"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.city"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="Postal Code"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.zip_code"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Province / State"/>
                    <Input type="text" class="form-control" v-model="newAtteForm.province"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Country" required="true"/>
                    <input type="text" class="form-control" list="datalistOptionsInputCountry"
                           placeholder="Type to search..."  v-model="newAtteForm.country"  :disabled="newAtteForm.program_guid === ''" />
                    <datalist id="datalistOptionsInputCountry">
                        <option v-for="cntry in countries" :value="cntry.name">{{ cntry.name }}</option>
                    </datalist>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="> 50% in-person?" required="true"/>
                    <Select class="form-select" v-model="newAtteForm.gt_fifty_pct_in_person" :disabled="newAtteForm.program_guid === ''">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Date of Birth" required="true"/>
                    <Input type="date" min="1930-01-01" :max="$getFormattedDate()" placeholder="YYYY-MM-DD"
                           class="form-control" v-model="newAtteForm.dob"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>
                <div class="col-md-3">
                    <Label class="form-label" value="Email" required="true"/>
                    <Input type="email" class="form-control" v-model="newAtteForm.email"
                           :disabled="newAtteForm.program_guid === ''"/>
                </div>

                <div class="col-md-3">
                    <Label class="form-label" value="Expiry Date"/>
                    <Input type="text"
                           class="form-control" v-model="instCap.end_date"
                           disabled readonly/>
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
            <button type="button" class="btn btn-secondary btn-sm" @click="submitForm('Draft')" :disabled="newAtteForm.processing">Save Draft</button>
            <button type="button" class="btn btn-success btn-sm" @click="submitForm('Issued')" :disabled="newAtteForm.processing">Issue Attestation</button>
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
    name: 'AttestationCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        newAtte: Object|null,
        institution: Object,
        countries: Object,
        error: String|null,
        programs: Object,
        instCap: Object
    },
    data() {
        return {
            duplicate: false,
            randomId: '',
            newAtteForm: null,
            newAtteFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                program_guid: "",
                first_name: "",
                last_name: "",
                id_number: "",
                student_number: "",
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
        checkDuplicate: function (){
            this.duplicate = false;

            if(this.newAtteForm.student_number == '') return false;
            let vm = this;
            let data = {
                student_number: this.newAtteForm.student_number,
            }
            axios.post('/institution/api/check/duplicate_student', data)
                .then(function (response) {
                    if(response.data.body.count > 0){
                        vm.duplicate = true;
                    }
                })
        },
        submitForm: function (status) {
            this.newAtteForm.status = status;
            if(this.newAtteForm.status !== 'Draft'){
                let check = confirm('You are about to Save & Lock this record. Are you sure you want to continue?');
                if(!check){
                    return false;
                }
            }
            this.newAtteForm.formState = null;
            this.newAtteForm.post('/institution/attestations', {
                onSuccess: (response) => {
                    $("#newAtteModal").modal('hide');
                    this.newAtteForm.reset(this.newAtteFormData);

                    this.$inertia.visit('/institution/attestations');
                    // console.log(response.props.institution)
                },
                onError: () => {
                    this.newAtteForm.formState = false;
                },
                preserveState: true
            });
        },

        randomizer: function ()
        {
            if(this.newAtteForm != null){
                // Count the existing elements inside the form
                const form = document.getElementById(this.randomId);
                // const existingElementsCount = form.children.length;
                const existingElementsCount = form.querySelectorAll('input, select, textarea');

                // Generate random elements
                const generatedElementsCount = Math.floor(Math.random() * 5) + 1; // Generate between 1 to 5 elements
                for (let i = 0; i < generatedElementsCount; i++) {
                    const elementType = Math.random() < 0.5 ? 'input' : 'select'; // Randomly choose between input and select
                    const element = document.createElement(elementType);
                    if(elementType === 'input') element.type = 'text';
                    // Set other attributes for the generated elements as needed
                    element.style.display = 'none'; // Hide the generated elements
                    // Randomly place the generated elements before randomly chosen original elements
                    const randomIndex = Math.floor(Math.random() * existingElementsCount);
                    form.insertBefore(element, form.children[randomIndex]);
                }
            }

        }
    },

    mounted() {
        this.newAtteForm = useForm(this.newAtteFormData);
        this.randomId = "" + Math.random() + "";

        let vm = this;
        setTimeout(function(){vm.randomizer();}, 1000);

    }
}
</script>
