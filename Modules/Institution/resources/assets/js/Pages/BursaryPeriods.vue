<template>

<AuthenticatedLayout v-bind="$attrs">

    <div class="card">
        <div class="card-header">
            <div>Bursary Periods<button class="btn btn-success btn-sm float-end" data-bs-toggle="modal"
                    data-bs-target="#newBursaryPeriodModal">Add New</button></div>
        </div>

        <div class="card-body">
            <div v-if="bursaryPeriods.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col">Default Award</th>
                            <th scope="col">Period Budget</th>
                            <th scope="col">Budget Alloc. Type</th>
                            <th scope="col">BC Public Inst. %</th>
                            <th scope="col">BC Private Inst. %</th>
                            <th scope="col">Awarded?</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in bursaryPeriods">
                            <td><a :href="'/neb/bursary-periods/show/' + row.id">{{ row.bursary_period_start_date }}</a></td>
                            <td>{{ row.bursary_period_end_date }}</td>
                            <td>{{ formatCurrency(row.default_award) }}</td>
                            <td>{{ formatCurrency(row.period_budget) }}</td>
                            <td>{{ row.budget_allocation_type }}</td>
                            <td>{{ row.public_sector_budget }}%</td>
                            <td>{{ 100 - row.public_sector_budget }}%</td>
                            <td>
                                <span v-if="row.awarded" class="badge rounded-pill text-bg-success">YES</span>
                                <span v-else class="badge rounded-pill text-bg-info">NEW</span>
                            </td>
                            <td>
                                <template v-if="!row.awarded">
                                    <button class="btn btn-sm me-1 btn-secondary" @click="editBp(row)">Edit</button>
                                    <button class="btn btn-sm btn-danger" @click="deleteBp(row)">Delete</button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

        <div class="modal modal-lg fade" id="newBursaryPeriodModal" tabindex="-1" aria-labelledby="newBursaryPeriodModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newBursaryPeriodModalLabel">Add New Bursary Period</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="newBursaryPeriod">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-6">
                                        <Label for="newBursaryPeriodEndDate" class="form-label"
                                            value="Bursary Period Start Date" />
                                        <Input type="date" class="form-control" id="newBursaryPeriodStartDate"
                                            v-model="newBursaryPeriodForm.bursary_period_start_date" />
                                        <div v-if="errors.bursary_period_start_date" class="text-xs text-red-500">
                                            {{ errors.bursary_period_start_date.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <Label for="newBursaryPeriodEndDate" class="form-label"
                                            value="Bursary Period End Date" />
                                        <Input type="date" class="form-control" id="newBursaryPeriodEndDate"
                                            v-model="newBursaryPeriodForm.bursary_period_end_date" />
                                        <div v-if="errors.bursary_period_end_date" class="text-xs text-red-500">
                                            {{ errors.bursary_period_end_date.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="newBursaryPeriodDefaultAwardAmount" class="form-label"
                                            value="Defautl Award Amount" />
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="newBursaryPeriodDefaultAwardAmount"
                                                aria-label="Amount (to the nearest dollar)"
                                                v-model="newBursaryPeriodForm.default_award">
                                        </div>
                                        <div v-if="errors.default_award" class="text-xs text-red-500">
                                            {{ errors.default_award.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="newBursaryPeriodPeriodBudgetAmount" class="form-label"
                                            value="Period Budget Amount" />
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="newBursaryPeriodPeriodBudgetAmount"
                                                aria-label="Amount (to the nearest dollar)"
                                                v-model="newBursaryPeriodForm.period_budget">
                                        </div>
                                        <div v-if="errors.period_budget" class="text-xs text-red-500">
                                            {{ errors.period_budget.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="newBursaryPeriodBudgetAllocationType" class="form-label"
                                            value="Budget Allocation Type" />
                                        <Select class="form-select" v-model="newBursaryPeriodForm.budget_allocation_type">
                                            <option value="None">None</option>
                                            <option value="Sector">Sector</option>
                                            <option value="Nurse Type">Nurse Type</option>
                                        </Select>
                                        <div v-if="errors.budget_allocation_type" class="text-xs text-red-500">
                                            {{ errors.budget_allocation_type.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="newBursaryPeriodPublicPortion" class="form-label"
                                            value="Public Portion" />
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="newBursaryPeriodPublicPortion"
                                                aria-label="percentage" v-model="newBursaryPeriodForm.public_sector_budget">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div v-if="errors.public_sector_budget" class="text-xs text-red-500">
                                            {{ errors.public_sector_budget.join(', ') }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="newBursaryPeriodForm.processing">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div v-if="editBursaryPeriodForm != null" class="modal modal-lg fade" id="editBursaryPeriodModal" tabindex="-1"
            aria-labelledby="editBursaryPeriodModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBursaryPeriodModalLabel">Edit Bursary Period</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="editBursaryPeriod">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-6">
                                        <Label for="editBursaryPeriodEndDate" class="form-label"
                                            value="Bursary Period Start Date" />
                                        <Input type="date" class="form-control" id="editBursaryPeriodEndDate"
                                            v-model="editBursaryPeriodForm.bursary_period_start_date" />
                                        <div v-if="errors.bursary_period_start_date" class="text-xs text-red-500">
                                            {{ errors.bursary_period_start_date.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <Label for="editBursaryPeriodEndDate" class="form-label"
                                            value="Bursary Period End Date" />
                                        <Input type="date" class="form-control" id="editBursaryPeriodEndDate"
                                            v-model="editBursaryPeriodForm.bursary_period_end_date" />
                                        <div v-if="errors.bursary_period_end_date" class="text-xs text-red-500">
                                            {{ errors.bursary_period_end_date.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="editBursaryPeriodDefaultAwardAmount" class="form-label"
                                            value="Defautl Award Amount" />
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control"
                                                id="editBursaryPeriodDefaultAwardAmount"
                                                aria-label="Amount (to the nearest dollar)"
                                                v-model="editBursaryPeriodForm.default_award">
                                        </div>
                                        <div v-if="errors.default_award" class="text-xs text-red-500">
                                            {{ errors.default_award.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="editBursaryPeriodPeriodBudgetAmount" class="form-label"
                                            value="Period Budget Amount" />
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control"
                                                id="editBursaryPeriodPeriodBudgetAmount"
                                                aria-label="Amount (to the nearest dollar)"
                                                v-model="editBursaryPeriodForm.period_budget">
                                        </div>
                                        <div v-if="errors.period_budget" class="text-xs text-red-500">
                                            {{ errors.period_budget.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="editBursaryPeriodBudgetAllocationType" class="form-label"
                                            value="Budget Allocation Type" />
                                        <Select class="form-select" v-model="editBursaryPeriodForm.budget_allocation_type">
                                            <option value="None">None</option>
                                            <option value="Sector">Sector</option>
                                            <option value="Nurse Type">Nurse Type</option>
                                        </Select>
                                        <div v-if="errors.budget_allocation_type" class="text-xs text-red-500">
                                            {{ errors.budget_allocation_type.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <Label for="editBursaryPeriodPublicPortion" class="form-label"
                                            value="Public Portion" />
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="editBursaryPeriodPublicPortion"
                                                aria-label="percentage"
                                                v-model="editBursaryPeriodForm.public_sector_budget">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div v-if="errors.public_sector_budget" class="text-xs text-red-500">
                                            {{ errors.public_sector_budget.join(', ') }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="editBursaryPeriodForm.processing">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </AuthenticatedLayout>
</template>
<script>
import { Link, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Select from "@/Components/Select";
import FormSubmitAlert from "@/Components/FormSubmitAlert";

export default {
    name: 'BursaryPeriods',
    components: {
        Link, Input, Select, Label, FormSubmitAlert, AuthenticatedLayout, Head
    },
    props: {
        results: Object,
        errors: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            bursaryPeriods: [],
            formatter: new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }),
            newBursaryPeriodForm: useForm({
                formState: true,
                bursary_period_start_date: '',
                bursary_period_end_date: '',
                default_award: '',
                period_budget: '',
                budget_allocation_type: '',
                public_sector_budget: '',
            }),
            editBursaryPeriodForm: null,
            deleteBursaryPeriodForm: useForm({
                formState: true,
            }),
        }
    },
    computed: {
    },
    methods: {
        formatCurrency: function (value) {
            if (!value)
                return 0;

            return this.formatter.format(value.toString());
        },
        fetchBursaryPeriods: function () {
            let vm = this;
            // Make a request for a user with a given ID
            axios.get('/neb/bursary-periods/fetch')
                .then(function (response) {
                    // handle success
                    vm.bursaryPeriods = response.data.bp;
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
        },
        newBursaryPeriod: function () {
            let vm = this;
            this.newBursaryPeriodForm.formState = '';
            this.newBursaryPeriodForm.post('/neb/bursary-periods/store', {
                onSuccess: () => {
                    $("#newBursaryPeriodModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.newBursaryPeriodForm.reset();

                            vm.newBursaryPeriodForm.formState = true;
                            vm.fetchBursaryPeriods();
                        });
                },
                onFailure: () => {
                },
                onError: () => {
                    this.newBursaryPeriodForm.formState = false;
                },
                preserveState: true
            });
        },
        deleteBp: function (bp) {
            let check = confirm("Are you sure you wish to delete this bursary period?")
            if (check) {
                let vm = this;
                this.deleteBursaryPeriodForm = useForm(bp);
                this.deleteBursaryPeriodForm.post('/neb/bursary-periods/delete', {
                    onSuccess: () => {
                        vm.fetchBursaryPeriods();
                    },
                    onError: () => {
                        this.deleteBursaryPeriodForm.formState = false;
                    },
                });
            }

        },
        editBp: function (bp) {
            let vm = this;
            this.editBursaryPeriodForm = useForm(bp);
            // this.editBursaryPeriodForm.bursary_period_start_date = bp.bpsd;
            // this.editBursaryPeriodForm.bursary_period_end_date = bp.bped;
            this.editBursaryPeriodForm.formState = '';
            $("#editBursaryPeriodModal").modal('show')
                .on('hidden.bs.modal', function () {
                    vm.editBursaryPeriodForm.reset();

                    vm.editBursaryPeriodForm.formState = true;
                    vm.fetchBursaryPeriods();
                });
        },
        editBursaryPeriod: function () {
            let vm = this;
            this.editBursaryPeriodForm.put('/neb/bursary-periods/update', {
                onSuccess: () => {
                    $("#editBursaryPeriodModal").modal('hide');
                },
                onFailure: () => {
                },
                onError: () => {
                    this.editBursaryPeriodForm.formState = false;
                },
                // preserveState: true
            });
        }
    },
    mounted() {
        this.fetchBursaryPeriods();
    }
}

</script>
