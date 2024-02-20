<template>
    <form v-if="newFedCapForm != null" class="card-body">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <Label for="inputSd" class="form-label" value="Start Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputSd" v-model="newFedCapForm.start_date"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputEd" class="form-label" value="End Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputEd" v-model="newFedCapForm.end_date"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalAtte" v-model="newFedCapForm.total_attestations"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputStatus" class="form-label" value="Status"/>
                    <Select class="form-select" id="inputStatus" v-model="newFedCapForm.status">
                        <option value=""></option>
                        <option v-for="stat in $attrs.utils['Federal Cap Status']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>

                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comment"/>
                    <textarea class="form-control" id="inputComment" v-model="newFedCapForm.comment" rows="3"></textarea>
                </div>

                <div v-if="newFedCapForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="newFedCapForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in newFedCapForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button @click="submitForm" type="button" class="btn me-2 btn-outline-success float-end" :disabled="newFedCapForm.processing">
                Create Federal Cap
            </button>
        </div>
        <FormSubmitAlert :form-state="newFedCapForm.formState" :success-msg="newFedCapForm.formSuccessMsg"
                         :fail-msg="newFedCapForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'FedCapCreate',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        newFedCap: Object|null
    },
    data() {
        return {
            newFedCapForm: null,
            newFedCapFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                start_date: "",
                end_date: "",
                total_attestations: "",
                status: "",
                comment: "",
            },
        }
    },
    methods: {
        submitForm: function () {
            let check = confirm('You are about to create a new Federal Cap. This will disable the active Federal Cap and all Institution/Program Caps associated with it. Are you sure you want to continue?');
            if(!check){
                return false;
            }

            this.newFedCapForm.formState = null;
            this.newFedCapForm.post('/ministry/fed_caps', {
                onSuccess: (response) => {
                    $("#newFedCapModal").modal('hide');
                    this.newFedCapForm.reset(this.newFedCapFormData);
                    this.$inertia.visit('/ministry/fed_caps/' + this.newFedCap.id);
                },
                onError: () => {
                    this.newFedCapForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.newFedCapForm = useForm(this.newFedCapFormData);
    }
}
</script>
