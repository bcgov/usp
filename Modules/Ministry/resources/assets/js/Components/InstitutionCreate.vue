<template>
    <form v-if="newInstForm != null" class="card-body" @submit.prevent="submitForm">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputName" class="form-label" value="Name"/>
                    <Input type="text" class="form-control" id="inputName" v-model="newInstForm.name"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputAddress1" class="form-label" value="Address 1"/>
                    <Input type="text" class="form-control" id="inputAddress1" v-model="newInstForm.address1"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputBceidBizGuid" class="form-label" value="Address 1"/>
                    <Input type="text" class="form-control" id="inputBceidBizGuid" v-model="newInstForm.bceid_business_guid"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputDli" class="form-label" value="DLI #"/>
                    <Input type="text" class="form-control" id="inputDli" v-model="newInstForm.dli"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputPrimaryContact" class="form-label" value="Primary Contact"/>
                    <Input type="text" class="form-control" id="inputPrimaryContact"
                           v-model="newInstForm.primary_contact"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputPrimaryEmail" class="form-label" value="Primary Email"/>
                    <Input type="email" class="form-control" id="inputPrimaryEmail" v-model="newInstForm.primary_email"/>
                </div>

                <div class="col-md-4">
                    <Label for="inputCity" class="form-label" value="City"/>
                    <Input type="text" class="form-control" id="inputCity" v-model="newInstForm.city"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputPostalCode" class="form-label" value="Postal Code"/>
                    <Input type="text" class="form-control" id="inputPostalCode" v-model="newInstForm.postal_code"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputProvince" class="form-label" value="Province"/>
                    <Select class="form-select" id="inputProvince" v-model="newInstForm.province">
                        <option></option>
                        <option v-for="prov in $attrs.utils['Provinces']" :value="prov.field_name">{{ prov.field_name }}</option>
                    </Select>
                </div>

                <div class="col-md-3">
                    <Label for="inputPublic" class="form-label" value="Public?"/>
                    <Select class="form-select" id="inputPublic" v-model="newInstForm.public">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputActiveStatus" class="form-label" value="Active?"/>
                    <Select class="form-select" id="inputActiveStatus" v-model="newInstForm.active_status">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputStandingStatus" class="form-label" value="Status - in good standing?"/>
                    <Select class="form-select" id="inputStandingStatus" v-model="newInstForm.standing_status">
                        <option value=""></option>
                        <option v-for="restr in $attrs.utils['Institution Restriction']" :value="restr.field_name">{{ restr.field_name }}</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputInfoSharing" class="form-label" value="Info Sharing Agreement?" />
                    <Select class="form-select" id="inputInfoSharing" v-model="newInstForm.info_sharing_agreement">
                        <option value="true">Provided</option>
                        <option value="false">Not Provided</option>
                    </Select>
                </div>
                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comments" />
                    <textarea class="form-control" id="inputComment" v-model="newInstForm.comment"></textarea>
                </div>

                <div v-if="newInstForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="newInstForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in newInstForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-success" :disabled="newInstForm.processing">
                Create Institution
            </button>
        </div>
        <FormSubmitAlert :form-state="newInstForm.formState" :success-msg="newInstForm.formSuccessMsg"
                         :fail-msg="newInstForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        newInst: Object|null
    },
    data() {
        return {
            newInstForm: null,
            newInstFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                dli: "",
                bceid_business_guid: "",
                name: "",
                legal_name: "",
                address1: "",
                address2: "",
                primary_contact: "",
                primary_email: "",
                city: "",
                postal_code: "",
                province: "",
                public: false,
                active_status: true,
                standing_status: null,
                comment: ''
            },
        }
    },
    methods: {
        submitForm: function () {
            this.newInstForm.formState = null;
            this.newInstForm.post('/ministry/institutions', {
                onSuccess: (response) => {
                    $("#newInstModal").modal('hide');
                    this.newInstForm.reset(this.newInstFormData);
                    this.$inertia.visit('/ministry/institutions/' + this.newInst.id);
                    // console.log(response.props.institution)
                },
                onError: () => {
                    this.newInstForm.formState = false;
                },
                preserveState: true
            });
        }
    },
    mounted() {
        this.newInstForm = useForm(this.newInstFormData);
    }
}
</script>
