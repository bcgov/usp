<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Federal Caps" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Institution Search
                            </div>
                            <div class="card-body">
                                <InstitutionSearchBox />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card mb-3">
                            <div class="card-header">
                                Federal Caps
                                <Link :href="'/ministry/fed_caps/new'" class="btn btn-sm btn-success float-end">New Cap</Link>
                            </div>
                            <div class="card-body">
                                <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <FedCapsHeader></FedCapsHeader>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, i) in results.data">
                                            <td><Link :href="'/ministry/fed_caps/' + row.guid">{{ row.start_date }}</Link></td>
                                            <td>{{ row.end_date }}</td>
                                            <td>{{ row.total_attestations}}</td>
                                            <td>
                                                <span v-if="row.status" class="badge rounded-pill text-bg-success">Active</span>
                                                <span v-else class="badge rounded-pill text-bg-secondary">{{ row.status }}</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination :links="results.links" :active-page="results.current_page" />
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
import InstitutionSearchBox from '../Components/InstitutionSearch.vue';
import FedCapsHeader from '../Components/FedCapsHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from "@/Components/Pagination";

export default {
    name: 'Institutions',
    components: {
        AuthenticatedLayout, InstitutionSearchBox, FedCapsHeader, Head, Link, Pagination
    },
    props: {
        results: Object
    }
}
</script>
