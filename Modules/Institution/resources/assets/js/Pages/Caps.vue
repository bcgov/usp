<template>
    <Head title="Institution Caps" />

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            Institution Caps
                        </div>
                        <div class="card-body">
                            <div v-if="instCaps != null && instCaps.length > 0" class="table-responsive pb-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">Total Atte.</th>
                                            <th scope="col">Issued Atte.</th>
                                            <th scope="col">Draft Atte.</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in instCaps">
                                            <td>{{ row.start_date }}</td>
                                            <td>{{ row.end_date }}</td>
                                            <td>{{ row.total_attestations}}</td>
                                            <td>{{ row.issued_attestations}}</td>
                                            <td>{{ row.draft_attestations}}</td>
                                            <td>
                                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                                            </td>
                                            <td>{{ row.extra_comment}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h1 v-else class="lead">No results</h1>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            Institution Program Caps
                        </div>
                        <div class="card-body">

                            <div v-if="programCaps != null && programCaps.length > 0" class="table-responsive pb-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Program</th>
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">Total Atte.</th>
                                            <th scope="col">Issued Atte.</th>
                                            <th scope="col">Draft Atte.</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, i) in programCaps">
                                            <td v-if="row.program == null"></td>
                                            <td v-else>{{ row.program.program_name }}</td>
                                            <td>{{ row.start_date }}</td>
                                            <td>{{ row.end_date }}</td>
                                            <td>{{ row.total_attestations}}</td>
                                            <td>{{ row.issued_attestations}}</td>
                                            <td>{{ row.draft_attestations}}</td>
                                            <td>
                                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                                            </td>
                                            <td>{{ row.extra_comment}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h1 v-else class="lead">No results</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Link, Head } from '@inertiajs/vue3';

export default {
    name: 'Caps',
    components: {
        AuthenticatedLayout, Head, Link
    },
    props: {
        results: Object,
        institution: Object,
        instCaps: Object|null,
        programCaps: Object|null
    },
    data() {
        return {
        }
    },

    methods: {

    },
    computed: {

    },
    mounted() {
        this.attestationList = this.results.data;
    },
}
</script>
