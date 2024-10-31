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
                                <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newFedCapModal">New Federal Cap</button>
                            </div>
                            <div class="card-body">
                                <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <FedCapsHeader></FedCapsHeader>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, i) in results.data">
                                            <td><Link :href="'/ministry/fed_caps/' + row.id">{{ row.start_date }}</Link></td>
                                            <td>{{ row.end_date }}</td>
                                            <td>{{ row.total_attestations}}</td>
                                            <td>{{ row.total_reserved_graduate_attestations}}</td>
                                            <td>
                                                <span v-if="row.status === 'Active'" class="badge rounded-pill text-bg-success">Active</span>
                                                <span v-if="row.status === 'Completed'" class="badge rounded-pill text-bg-primary">Completed</span>
                                                <span v-if="row.status === 'Cancelled'" class="badge rounded-pill text-bg-warning">Cancelled</span>
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


        <div class="modal modal-lg fade" id="newFedCapModal" tabindex="-1" aria-labelledby="newFedCapModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newFedCapModalLabel">Add New Federal Cap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <FedCapCreate v-bind="$attrs" :newFedCap="newFedCap"></FedCapCreate>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import InstitutionSearchBox from '../Components/InstitutionSearch.vue';
import FedCapsHeader from '../Components/FedCapsHeader.vue';
import FedCapCreate from '../Components/FedCapCreate.vue';
import Pagination from "@/Components/Pagination";
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'FedCaps',
    components: {
        AuthenticatedLayout, InstitutionSearchBox, FedCapsHeader, Head, Link, Pagination, FedCapCreate
    },
    props: {
        results: Object,
        newFedCap: Object|null
    }
}
</script>
