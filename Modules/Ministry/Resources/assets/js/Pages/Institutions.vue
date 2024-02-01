<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Institutions" />

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
                                Institutions
<!--                                <Link :href="'/vss/cases/create'" class="btn btn-sm btn-success float-end">New Case</Link>-->
                            </div>
                            <div class="card-body">
                                <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <InstitutionsHeader></InstitutionsHeader>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, i) in results.data">
                                            <td><Link :href="'/ministry/institutions/show/' + row.id">{{ row.dli }}</Link></td>
                                            <td>{{ row.name }}</td>
                                            <td>
                                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                                            </td>
                                            <td>{{ row.standing_status}}</td>
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
import InstitutionsHeader from '../Components/InstitutionsHeader.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from "@/Components/Pagination";

export default {
    name: 'Institutions',
    components: {
        AuthenticatedLayout, InstitutionSearchBox, InstitutionsHeader, Head, Link, Pagination
    },
    props: {
        results: Object
    }
}
</script>
