<template>
    <form v-if="newAtteForm != null" class="card-body" @submit.prevent="submitForm">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputName" class="form-label" value="Institution Name"/>
                    <input @change="enableCap" type="text" class="form-control" list="datalistOptionsInstName" id="inputName" placeholder="Type to search..."  v-model="selectedInstIndex" />
                    <datalist id="datalistOptionsInstName">
                        <option v-for="(inst, i) in institutions" :key="i" :value="inst.name"></option>
                    </datalist>
                </div>
                <div class="col-md-4">
                    <Label for="inputCap" class="form-label" value="Institution Cap"/>
                    <Select class="form-select" id="inputCap" v-model="newAtteForm.cap_guid" :disabled="selectedInst === ''">
                        <template v-if="selectedInst != ''">
                            <option></option>
                            <option v-for="c in selectedInst.active_caps" :value="c.guid">{{ c.start_date }} - {{ c.end_date}}</option>
                        </template>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputDli" class="form-label" value="Student Name"/>
                    <Input type="text" class="form-control" id="inputDli" v-model="newAtteForm.student_name"
                           :disabled="newAtteForm.cap_guid === ''"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputStudentId" class="form-label" value="Student ID"/>
                    <Input type="text" class="form-control" id="inputStudentId" v-model="newAtteForm.student_id_number"
                           :disabled="newAtteForm.cap_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputDob" class="form-label" value="Date of Birth"/>
                    <Input type="date" min="1930-01-01" max="2020-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputDob" v-model="newAtteForm.student_dob"
                           :disabled="newAtteForm.cap_guid === ''"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputExpiryDate" class="form-label" value="Expiry Date"/>
                    <Input type="date" min="2024-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputDob" v-model="newAtteForm.expiry_date"
                           :disabled="newAtteForm.cap_guid === ''"/>
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
        <div class="modal-footer">
            <button type="submit" class="btn me-2 btn-outline-success float-end" :disabled="newAtteForm.processing">
                Create Attestation
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
    name: 'AttestationCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        newAtte: Object|null,
        institutions: Object
    },
    data() {
        return {
            newAtteForm: null,
            newAtteFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                institution_guid: "",
                cap_guid: "",
                student_name: "",
                student_id_number: "",
                student_dob: "",
                expiry_date: "",
            },
            selectedInstIndex: '',
            selectedInst: '',
        }
    },
    methods: {
        enableCap: function (e){
            const inst = this.institutions.find(inst => inst.name === e.target.value);
            if (inst) {
                this.selectedInst = inst;
                this.newAtteForm.institution_guid = inst.guid;
            }
        },
        submitForm: function () {
            this.newAtteForm.formState = null;
            this.newAtteForm.post('/ministry/attestations', {
                onSuccess: (response) => {
                    $("#newAtteModal").modal('hide');
                    this.newAtteForm.reset(this.newAtteFormData);
                    this.$inertia.visit('/ministry/attestations/' + this.newAtte.id);
                    // console.log(response.props.institution)
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
    }
}
</script>
