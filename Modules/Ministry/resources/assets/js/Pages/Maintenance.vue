<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Maintenance" />

    <BreezeAuthenticatedLayout>

        <div class="mt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4 mt-3">
                        <div class="card">
                            <div class="card-header">
                                Maintenance Menu
                            </div>
                            <div class="card-body">
                                <MaintenanceMenu :page="page" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 mt-3">
                        <MaintenanceReferralSources v-if="page === 'referral-source'" :results="results"></MaintenanceReferralSources>
                        <MaintenanceAreas v-if="page === 'area-of-audit'" :results="results"></MaintenanceAreas>
                        <MaintenanceSanctionTypes v-if="page === 'sanction-type'" :results="results"></MaintenanceSanctionTypes>
                        <MaintenanceNatureOffences v-if="page === 'nature-offence'" :results="results"></MaintenanceNatureOffences>
                        <MaintenanceSchools v-if="page === 'school'" :results="results"></MaintenanceSchools>

                        <MaintenanceStaff v-if="page === 'staff'" :results="results"></MaintenanceStaff>
                        <MaintenanceStaffEdit v-if="page === 'staff-edit'" :results="results"></MaintenanceStaffEdit>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>

</template>
<script>
import BreezeAuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/vue3';
import MaintenanceMenu from "../Components/MaintenanceMenu";
import MaintenanceSchools from "../Components/MaintenanceSchools";
import MaintenanceStaff from "../Components/MaintenanceStaff";
import MaintenanceStaffEdit from "../Components/MaintenanceStaffEdit";

export default {
    name: 'Maintenance',
    components: {
        MaintenanceMenu,
        BreezeAuthenticatedLayout, Head, Link, MaintenanceSchools, MaintenanceStaff, MaintenanceStaffEdit
    },
    props: {
        results: Object,
        page: String,
    },
    data() {
        return {
        }
    },
    methods: {
    },
    watch: {
    },
    computed: {
    },
    mounted() {
    }
}
</script>
