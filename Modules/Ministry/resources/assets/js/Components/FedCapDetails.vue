<template>
    <div v-if="editForm != null" class="card mb-3">
        <div class="card-header">
            <div>Federal Cap Details</div>
        </div>

        <form class="card-body" @submit.prevent="submitForm">
            <div class="row g-3">

                <div class="col-md-3">
                    <Label for="inputSd" class="form-label" value="Start Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputSd" v-model="editForm.start_date"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputEd" class="form-label" value="End Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputEd" v-model="editForm.end_date"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalAtte" v-model="editForm.total_attestations"/>
                </div>
                <div class="col-md-3">
                    <Label for="inputStatus" class="form-label" value="Status"/>
                    <Select class="form-select" id="inputStatus" v-model="editForm.status">
                        <option value=""></option>
                        <option v-for="stat in $attrs.utils['Federal Cap Status']" :value="stat.field_name">{{ stat.field_name }}</option>
                    </Select>
                </div>

                <div class="col-12">
                    <Label for="inputComment" class="form-label" value="Comment"/>
                    <textarea class="form-control" id="inputComment" v-model="editForm.comment" rows="3"></textarea>
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
            <div class="card-footer mt-3">
                <button type="button" class="btn me-2 btn-secondary" @click="back">Back</button>
                <button type="submit" class="btn me-2 btn-success float-end" :disabled="editForm.processing">Update Federal Cap</button>
            </div>
            <FormSubmitAlert :form-state="editForm.formState"
                             :success-msg="'Institution record was updated successfully.'"></FormSubmitAlert>
        </form>
    </div>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    name: 'FedCapDetails',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        results: Object,
    },
    data() {
        return {
            editForm: '',
        }
    },
    methods: {
        back: function()
        {
            window.history.back();
        },
        submitForm: function ()
        {
            this.editForm.formState = null;
            this.editForm.put('/ministry/fed_caps', {
                onSuccess: () => {
                    this.editForm.formState = true;
                },
                onError: () => {
                    this.editForm.formState = false;
                },
                preserveState: true
            });
        },
    },

    mounted() {
        this.editForm = useForm(this.results);
    }
}
</script>
