<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Institution" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container-fluid">
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
<!--                        <InstitutionCaps v-if="page === 'area-of-audit'" :results="results"></InstitutionCaps>-->
<!--                        <InstitutionStaff v-if="page === 'sanction-type'" :results="results"></InstitutionStaff>-->
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
// import MaintenanceStaff from "../Components/MaintenanceStaff";
// import MaintenanceStaffEdit from "../Components/MaintenanceStaffEdit";

export default {
    name: 'Institution',
    components: {
        InstitutionMenu,
        AuthenticatedLayout, Head, Link, InstitutionDetails
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
