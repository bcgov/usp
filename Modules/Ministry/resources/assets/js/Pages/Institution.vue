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
                                <InstitutionMenu :page="page" :id="results.id" :name="results.name" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <InstitutionDetails v-bind="$attrs" v-if="page === 'details'" :results="results"></InstitutionDetails>
                        <InstitutionPrograms v-bind="$attrs" v-if="page === 'programs'" :results="results"></InstitutionPrograms>
                        <InstitutionCaps v-bind="$attrs" v-if="page === 'caps'" :instCap="instCap" :results="results" :fedCaps="fedCaps"></InstitutionCaps>
                        <InstitutionStaff v-bind="$attrs" v-if="page === 'staff'" :results="results"></InstitutionStaff>
                        <InstitutionAttestations v-bind="$attrs" v-if="page === 'attestations'" :countries="countries" :results="results"></InstitutionAttestations>
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
import InstitutionAttestations from "../Components/InstitutionAttestations";
import InstitutionPrograms from "../Components/InstitutionPrograms";

export default {
    name: 'Institution',
    components: {
        InstitutionAttestations,
        InstitutionMenu,
        AuthenticatedLayout, Head, Link, InstitutionDetails, InstitutionCaps, InstitutionStaff, InstitutionPrograms
    },
    props: {
        results: Object,
        page: String,
        fedCaps: Object,
        countries: Object,
        institutions: Object,
        instCap: Object
    }
}
</script>
