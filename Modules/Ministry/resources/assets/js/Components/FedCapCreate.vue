<template>
    <form v-if="newFedCapForm != null" class="card-body">
        <div class="modal-body">
            <div class="row g-3 mb-3">

                <div class="col-md-6">
                    <Label for="inputSd" class="form-label" value="Start Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputSd" v-model="newFedCapForm.start_date"/>
                </div>
                <div class="col-md-6">
                    <Label for="inputEd" class="form-label" value="End Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputEd" v-model="newFedCapForm.end_date"/>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-6 col-md-12">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalAtte" v-model="newFedCapForm.total_attestations"/>
                </div>
                <div class="col-lg-6 col-md-12">
                    <Label for="inputTotalResGradAtte" class="form-label" value="Total Reserved Graduate Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalResGradAtte" v-model="newFedCapForm.total_reserved_graduate_attestations"/>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <Label for="inputOverAllocationPercentage" class="form-label" value="Over Allocation Percentage"/>
                    <Select class="form-select" id="inputOverAllocationPercentage" v-model="newFedCapForm.over_allocation_percentage">
                        <option value="0">0%</option>
                        <option value="0.01">1%</option>
                        <option value="0.02">2%</option>
                        <option value="0.03">3%</option>
                        <option value="0.04">4%</option>
                        <option value="0.05">5%</option>
                        <option value="0.06">6%</option>
                        <option value="0.07">7%</option>
                        <option value="0.08">8%</option>
                        <option value="0.09">9%</option>
                        <option value="0.1">10%</option>
                    </Select>
                </div>
                <div class="col-md-6">
                    <Label for="inputStatus" class="form-label" value="Status"/>
                    <Select class="form-select" id="inputStatus" v-model="newFedCapForm.status">
                        <option value=""></option>
                        <option v-for="stat in $attrs.utils['Federal Cap Status']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comment"/>
                    <textarea class="form-control" id="inputComment" v-model="newFedCapForm.comment" rows="3"></textarea>
                </div>
            </div>
            <div class="row g-3 mb-3">
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
            <button @click="submitForm" type="button" class="btn btn-sm btn-success" :disabled="newFedCapForm.processing">
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
                total_reserved_graduate_attestations: "",
                over_allocation_percentage: "0",
                status: "",
                comment: "",
            },
        }
    },
    methods: {
        submitForm: function () {
            // Reset error messages
            this.newFedCapForm.errors = [];
            this.newFedCapForm.hasErrors = false;

            // Making sur the value entered for Graduate Attest. is lower than Total Attest. value entered.
            if (parseInt(this.newFedCapForm.total_reserved_graduate_attestations) >= parseInt(this.newFedCapForm.total_attestations)) {
                this.newFedCapForm.errors.push('Value entered for "Total Reserved Graduate Attest. Allowed" must be lower than "Total Attest. Allowed" value.');
                this.newFedCapForm.hasErrors = true;
                return;
            }
            let check = confirm('You are about to create a new Federal Cap. This will disable the active Federal Cap and all Institution/Program Caps associated with it. Are you sure you want to continue?');
            if(!check){
                return false;
            }

            this.newFedCapForm.formState = null;
            this.newFedCapForm.post('/ministry/fed_caps', {
                onSuccess: (response) => {
                    this.newFedCapForm.errors = [];
                    this.newFedCapForm.hasErrors = false;
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
