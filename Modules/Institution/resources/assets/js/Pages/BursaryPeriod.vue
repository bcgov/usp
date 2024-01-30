<template>

    <AuthenticatedLayout v-bind="$attrs">

        <div class="card">
            <div class="card-header">
                <div>Bursary Period
                    <template v-if="periodStats !== ''">
                        <span class="badge bg-primary fw-light">{{ periodStats.info.bursary_period_start_date }}</span> to <span
                            class="badge bg-primary fw-light">{{ periodStats.info.bursary_period_end_date }}</span>
                    </template>
                    <h5 v-if="periodStats !== '' && periodStats.info.awarded === false" class="float-end"><span
                            class="badge bg-info fw-light">New</span></h5>
                    <h5 v-if="periodStats !== '' && periodStats.info.awarded === true" class="float-end"><span
                            class="badge bg-success fw-light">Awarded</span></h5>
                </div>
            </div>

            <div class="card-body">
                <div class="row justify-content-center text-center">
                    <template v-if="periodStats !== '' && periodStats.info.awarded === false">
                        <div class="col-auto">
                            <form @submit.prevent="startNeb">
                                <button type="submit" class="btn btn-secondary fw-light">
                                    <span v-if="startNebForm.formSubmitting">Processing <small>{{ startNebForm.step + 1
                                    }}/{{ startNebForm.totalSteps + 1 }}</small></span>
                                    <span v-else-if="startNebForm.step == startNebForm.totalSteps">Processing
                                        Complete</span>
                                    <span v-else>Start Process</span>
                                </button>
                            </form>
                        </div>

                        <div v-if="periodStats.sectorPri > 0 || periodStats.sectorPub > 0" class="col-auto">
                            <form @submit.prevent="endNeb">
                                <button type="submit" class="btn btn-secondary fw-light">
                                    <span v-if="endNebForm.formSubmitting">Processing <small>{{ endNebForm.step + 1
                                    }}/{{ endNebForm.totalSteps + 1 }}</small></span>
                                    <span v-else-if="endNebForm.step == endNebForm.totalSteps">Processing Complete</span>
                                    <span v-else>Start Finalizing Process</span>
                                </button>
                            </form>

                        </div>
                        <div class="col-1"></div>
                    </template>
                    <template v-if="eligibilityList != null && eligibilityList.data.length > 0">
                        <div class="col-auto"><button class="btn btn-success fw-light" @click="exportList('el')">Export
                                Eligible</button></div>
                        <div class="col-auto"><button class="btn btn-success fw-light" @click="exportList('in')">Export
                                Ineligible</button></div>
                        <div class="col-auto"><button class="btn btn-success fw-light" @click="exportList('aw')">Export
                                Awarded</button></div>
                        <div class="col-auto"><button class="btn btn-success fw-light" @click="exportList('aw_txt')">Export
                                Text Awarded</button></div>
                    </template>
                </div>





                <!-- MANAGE NEB -->
                <div>
                    <hr />

                    <div v-if="eligibilityList != null" class="row justify-content-center text-center mt-5">
                        <div v-if="eligibilityList.data.length > 0" class="col-12">
                            <div class="row mb-3">
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">ELIGIBLE</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.eligible }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">INELIGIBLE</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.ineligible }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">AWARDED</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.awarded }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">DENIED</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.denied }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">PUBLIC SEC.</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.sectorPub }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">PRIVATE SEC.</h5>
                                            <p class="card-text h1 fw-light">{{ periodStats.sectorPri }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr />

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col" v-for="(v, k, i) in eligibilityList.data[0]">
                                                <button type="button" class="btn btn-link" @click="sortByColumn(k)">{{
                                                    formatHeader(k) }}</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="el in eligibilityList.data">
                                            <template v-for="(v, k, i) in el">
                                                <th v-if="k == 'id'" scope="row">{{ v }}</th>
                                                <td v-else>{{ v }}</td>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                                <nav aria-label="Eligibility List Pagination">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item"
                                            :class="eligibilityList.current_page === 1 ?? 'disabled'">
                                            <a class="page-link"
                                                @click.prevent="jumpToPage(eligibilityList.first_page)">First</a>
                                        </li>
                                        <li class="page-item"
                                            :class="eligibilityList.current_page === 1 ?? 'disabled'">
                                            <a class="page-link"
                                                @click.prevent="jumpToPage(eligibilityList.current_page - 1)">Previous</a>
                                        </li>
                                        <li class="page-item"><span class="page-link">{{ eligibilityList.current_page }}</span></li>
                                        <li class="page-item"
                                            :class="eligibilityList.current_page === eligibilityList.last_page ?? 'disabled'">
                                            <a class="page-link"
                                                @click.prevent="jumpToPage(eligibilityList.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item"
                                            :class="eligibilityList.current_page === eligibilityList.last_page ?? 'disabled'">
                                            <a class="page-link"
                                                @click.prevent="jumpToPage(eligibilityList.last_page)">Last</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
<script>
import { Link, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/Authenticated.vue';

export default {
    name: 'BursaryPeriod',
    components: {
        Link, AuthenticatedLayout, Head
    },
    props: {
        id: String | Number,
        results: Object | null,
        stats: Object | null
    },
    data() {
        return {
            showNebForm: useForm({ bursary_period_id: '', page: 1, sort_by: 'id', sort_dir: 'asc' }),

            sortedData: [],
            eligibilityList: null,

            periodStats: '',
            endNebForm: useForm({
                formState: true,
                bursary_period_id: '',
                step: 0,
                formSubmitting: false,
                totalSteps: 2
            }),
            startNebForm: useForm({
                formState: true,
                bursary_period_id: '',
                step: 0,
                formSubmitting: false,
                totalSteps: 11
            }),

        }
    },
    methods: {
        sortByColumn(columnName) {
            //if we are switching sort direction
            if (columnName == this.showNebForm.sort_by) {
                this.showNebForm.sort_dir = this.showNebForm.sort_dir == 'asc' ? 'desc' : 'asc';
            }
            this.showNebForm.sort_by = columnName;
            this.showNeb();
        },

        formatHeader: function (value) {
            if (!value)
                return "-";

            // Split the input value by underscores
            const words = value.split('_');

            // Capitalize the first letter of each word and join them with a space
            const formattedValue = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ');

            return formattedValue;
        },

        exportList: function (type) {
            window.location.href = '/neb/export-neb/' + type + '/' + this.showNebForm.bursary_period_id;
        },

        jumpToPage: function (page) {
            if (page < this.eligibilityList.first_page || page > this.eligibilityList.last_page) return;
            this.showNebForm.page = page
            this.showNeb();
        },


        showNeb: function () {
            this.showNebForm.formSubmitting = true;
            this.showNebForm.post('/neb/bursary-periods/fetch-neb', {
                onSuccess: (response) => {
                    this.showNebForm.formSubmitting = false;
                    this.showNebForm.page = response.props.results.current_page;
                    if (this.showNebForm.page === 1) {
                        this.periodStats = response.props.stats;
                    }
                    this.eligibilityList = response.props.results;
                    this.startNebForm.bursary_period_id = this.id;
                    this.endNebForm.bursary_period_id = this.id;

                },
                onError: () => {
                    this.showNebForm.formSubmitting = false;
                },
            }, {preserveState: true}
            );
        },

        //finalize functions
        endNeb: function () {
            let check = confirm("Are you sure you wish to finalize awards?")
            if (check) {
                this.finalizeStep(0);
            }
        },
        finalizeStep: function (step) {
            if (step <= this.endNebForm.totalSteps) {
                $(".card-body button").prop('disabled', true)

                this.endNebForm.step = step;
                this.endNebForm.formSubmitting = true;
                this.endNebForm.post('/neb/finalize-neb', {
                    onSuccess: () => {
                        this.endNebForm.formSubmitting = false;
                        this.finalizeStep(step + 1);
                    },
                    onError: () => {
                        this.endNebForm.formSubmitting = false;
                    },
                    preserveState: true

                });

            } else {
                $(".card-body button").prop('disabled', false)
                console.log("Process Completed.")
                window.location.href = "/neb/bursary-periods/show/" + this.showNebForm.bursary_period_id;
            }
        },


        //create neb functions
        startNeb: function () {
            let check = confirm("Are you sure you wish to create a NEB Eligibility List (existing list will be deleted)?")
            if (check) {
                this.starStep(0);
            }
        },
        starStep: function (step) {
            if (step <= this.startNebForm.totalSteps) {
                $(".card-body button").prop('disabled', true)

                this.startNebForm.step = step;
                this.startNebForm.formSubmitting = true;
                this.startNebForm.post('/neb/process-neb', {
                    onSuccess: () => {
                        this.startNebForm.formSubmitting = false;
                        this.starStep(step + 1);
                    },
                    onError: () => {
                        this.startNebForm.formSubmitting = false;
                    },
                    preserveState: true

                });

            } else {
                $(".card-body button").prop('disabled', false)
                console.log("Process Completed.")
                window.location.href = "/neb/bursary-periods/show/" + this.showNebForm.bursary_period_id;
            }
        },
    },
    mounted() {
        this.showNebForm.bursary_period_id = this.id;
        this.showNeb();
    }
}

</script>
