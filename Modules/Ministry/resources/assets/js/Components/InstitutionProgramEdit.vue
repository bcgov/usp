<template>
    <form v-if="editForm != null" class="card-body" @submit.prevent="submitForm">
        <div class="modal-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputProgramName" class="form-label" value="Program Name" />
                    <Input type="text" class="form-control" id="inputProgramName" v-model="editForm.program_name" />
                </div>
                <div class="col-md-4">
                    <Label for="inputProgramType" class="form-label" value="Program Type" />
                    <Select class="form-select" id="inputProgramType" v-model="editForm.program_type">
                        <option></option>
                        <option v-for="stat in $attrs.utils['Program Type']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputCredential" class="form-label" value="Credential"/>
                    <Select class="form-select" id="inputCredential" v-model="editForm.credential">
                        <option></option>
                        <option v-for="stat in $attrs.utils['Program Credential']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>

                <div class="col-md-3">
                    <Label for="inputDurationHrs" class="form-label" value="Total Duration Hrs" />
                    <Input type="number" class="form-control" id="inputDurationHrs" v-model="editForm.total_duration_hrs" />
                </div>
                <div class="col-md-3">
                    <Label for="inputDurationWeeks" class="form-label" value="Total Duration Weeks" />
                    <Input type="number" class="form-control" id="inputDurationWeeks" v-model="editForm.total_duration_weeks" />
                </div>
                <div class="col-md-3">
                    <Label for="inputTuitionDomestic" class="form-label" value="Tuition Domestic" />
                    <Input type="number" step="0.01" class="form-control" id="inputTuitionDomestic" v-model="editForm.tuition_domestic" />
                </div>
                <div class="col-md-3">
                    <Label for="inputTuitionInt" class="form-label" value="Tuition International" />
                    <Input type="number" step="0.01" class="form-control" id="inputTuitionInt" v-model="editForm.tuition_international" />
                </div>

                <div class="col-md-3">
                    <Label for="inputExpReq" class="form-label" value="Work Exp. Required?"/>
                    <Select class="form-select" id="inputExpReq" v-model="editForm.work_experience_required">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputDeliveryInClass" class="form-label" value="Delivery In-class?"/>
                    <Select class="form-select" id="inputDeliveryInClass" v-model="editForm.delivery_in_class">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputDeliveryDistance" class="form-label" value="Delivery Distance?"/>
                    <Select class="form-select" id="inputDeliveryDistance" v-model="editForm.delivery_distance">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-3">
                    <Label for="inputDeliveryComb" class="form-label" value="Delivery Combined?"/>
                    <Select class="form-select" id="inputDeliveryComb" v-model="editForm.delivery_combined">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>

                <div class="col-md-2">
                    <Label for="inputNocCode" class="form-label" value="NOC Code" />
                    <Input type="text" class="form-control" id="inputNocCode" v-model="editForm.noc_code" />
                </div>
                <div class="col-md-2">
                    <Label for="inputCipCode" class="form-label" value="CIP Code" />
                    <Input type="text" class="form-control" id="inputCipCode" v-model="editForm.cip_code" />
                </div>
                <div class="col-md-3">
                    <Label for="inputActive" class="form-label" value="Active?" />
                    <Select class="form-select" id="inputActive" v-model="editForm.active_status">
                        <option value=""></option>
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-5">
                    <Label for="inputRestrictions" class="form-label" value="Restrictions"/>
                    <Select class="form-select" id="inputCredential" v-model="editForm.restrictions">
                        <option></option>
                        <option v-for="stat in $attrs.utils['Program Restriction']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>


                <div v-if="editForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="editForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in editForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn me-2 btn-outline-success float-end" :disabled="editForm.processing">
                Update Program
            </button>
        </div>
        <FormSubmitAlert :form-state="editForm.formState" :success-msg="editForm.formSuccessMsg"
                         :fail-msg="editForm.formFailMsg"></FormSubmitAlert>
    </form>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import {Link, useForm} from '@inertiajs/vue3';

export default {
    name: 'InstitutionProgramEdit',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        results: Object,
        program: Object
    },
    data() {
        return {
            editForm: null,
            editFormData: {
                formState: true,
                formSuccessMsg: 'Form was submitted successfully.',
                formFailMsg: 'There was an error submitting this form.',
                id: "",
                program_name: "",
                program_type: "",
                credential: "",
                total_duration_hrs: "",
                total_duration_weeks: "",
                tuition_domestic: "",
                tuition_international: "",
                work_experience_required: "",
                delivery_in_class: "",
                delivery_distance: "",
                delivery_combined: "",
                noc_code: "",
                cip_code: "",
                active_status: "",
                restrictions: ""
            },
        }
    },
    methods: {

        submitForm: function () {
            let vm = this;
            this.editForm.formState = null;
            this.editForm.put('/ministry/programs', {
                onSuccess: (response) => {
                    $("#editInstProgramModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.editForm.reset(vm.editForm);
                            vm.$inertia.visit('/ministry/institutions/' + vm.results.id + '/programs');
                            vm.$emit('close');
                        });
                },
                onError: () => {
                    this.editForm.formState = false;
                },
                preserveState: true
            });
        }
    },

    mounted() {
        this.editForm = useForm(this.program);
    }
}
</script>
