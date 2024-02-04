<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Federal Cap" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Federal Cap Menu
                            </div>
                            <div class="card-body">
                                <FedCapMenu :page="page" :id="results.id" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <FedCapDetails v-bind="$attrs" v-if="page === 'details'" :results="results"></FedCapDetails>
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
import FedCapMenu from "../Components/FedCapMenu";
import FedCapDetails from "../Components/FedCapDetails";
// import MaintenanceStaff from "../Components/MaintenanceStaff";
// import MaintenanceStaffEdit from "../Components/MaintenanceStaffEdit";

export default {
    name: 'FedCap',
    components: {
        FedCapMenu,
        AuthenticatedLayout, Head, Link, FedCapDetails
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
