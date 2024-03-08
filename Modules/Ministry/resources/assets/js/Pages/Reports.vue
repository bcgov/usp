<style scoped>
[type='checkbox']:checked, [type='radio']:checked {
    background-size: initial;
}
</style>
<template>
    <Head title="Reports" />

    <BreezeAuthenticatedLayout v-bind="$attrs">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Reports Menu
                            </div>
                            <div class="card-body">
                                <ReportsMenu :page="page" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <ReportsSummary v-if="page === 'summary'"></ReportsSummary>
                        <ReportsDetail v-if="page === 'detail'" :results="results"></ReportsDetail>
                        <ReportSources v-if="page === 'sources'"></ReportSources>
                    </div>
                </div>
            </div>
    </BreezeAuthenticatedLayout>

</template>
<script>
import BreezeAuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/vue3';
import ReportsMenu from "../Components/ReportsMenu";
import ReportsSummary from "../Components/ReportsSummary";
import ReportsDetail from "../Components/ReportsDetail";
import ReportSources from "../Components/ReportSources";

export default {
    name: 'Reports',
    components: {
        ReportsSummary,
        ReportsDetail,
        ReportSources,
        BreezeAuthenticatedLayout, Head, Link, ReportsMenu
    },
    props: {
        results: Object,
        categories: Object|null,
        page: String,
    },
    data() {
        return {
        }
    },
}
</script>
