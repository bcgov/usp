<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
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
                        <InstitutionDetails v-if="page === 'details'" :results="results"></InstitutionDetails>
                        <InstitutionCaps v-if="page === 'caps'" :results="results" :fedCaps="fedCaps"></InstitutionCaps>
                        <InstitutionStaff v-if="page === 'staff'" :results="results"></InstitutionStaff>
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
import InstitutionCaps from "../Components/InstitutionCaps";
import InstitutionStaff from "../Components/InstitutionStaff";

export default {
    name: 'Institution',
    components: {
        InstitutionMenu,
        AuthenticatedLayout, Head, Link, InstitutionDetails, InstitutionCaps, InstitutionStaff
    },
    props: {
        results: Object,
        page: String,
        fedCaps: Object
    },
    data() {
        return {
        }
    },
}
</script>
