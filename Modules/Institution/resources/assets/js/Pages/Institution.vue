<template>
    <Head title="Institution" />

    <AuthenticatedLayout v-bind="$attrs">

            <div v-if="results != null" class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Institution Menu
                            </div>
                            <div class="card-body">
                                <InstitutionMenu :page="page" :id="results.id" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <InstitutionDetails v-bind="$attrs" v-if="page === 'details'" :results="results"></InstitutionDetails>
                        <InstitutionStaff v-bind="$attrs" v-if="page === 'staff'" :results="results"></InstitutionStaff>
                    </div>
                </div>
            </div>
    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/vue3';
import InstitutionMenu from "../Components/InstitutionMenu";
import InstitutionDetails from "../Components/InstitutionDetails";
import InstitutionStaff from "../Components/InstitutionStaff";

export default {
    name: 'Institution',
    components: {
        InstitutionMenu,
        AuthenticatedLayout, Head, Link, InstitutionDetails, InstitutionStaff
    },
    props: {
        results: Object,
        page: String,
    },
    data() {
        return {
        }
    },
}
</script>
