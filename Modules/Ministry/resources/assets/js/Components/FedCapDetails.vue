<template>
    <div v-if="editForm != null" class="card mb-3">
        <div class="card-header">
            <div>Federal Cap Details</div>
        </div>

        <form class="card-body" @submit.prevent="submitForm">
            <div class="row g-3">

                <div class="col-md-4">
                    <Label for="inputSd" class="form-label" value="Start Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputSd" v-model="editForm.start_date"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputEd" class="form-label" value="End Date"/>
                    <Input type="date" min="2019-01-01" max="2040-12-31" placeholder="YYYY-MM-DD"
                           class="form-control" id="inputEd" v-model="editForm.end_date"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputTotalAtte" class="form-label" value="Total Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalAtte" v-model="editForm.total_attestations"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputTotalResGradAtte" class="form-label" value="Total Reserved Graduate Attest. Allowed"/>
                    <Input type="number" class="form-control" id="inputTotalResGradAtte" v-model="editForm.total_reserved_graduate_attestations"/>
                </div>
                <div class="col-md-4">
                    <Label for="inputOverAllocationPercentage" class="form-label" value="Over Allocation Percentage"/>
                    <Select class="form-select" id="inputOverAllocationPercentage" v-model="editForm.over_allocation_percentage">
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
                <div class="col-md-4">
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
