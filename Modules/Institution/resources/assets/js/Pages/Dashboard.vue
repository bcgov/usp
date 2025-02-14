<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div v-if="$page.props.errors.error" class="alert alert-danger mt-3">
                        {{ $page.props.errors.error }}
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="display-5">{{ results.name }}</div>
                            <p>Welcome {{ $attrs.auth.user.first_name }} {{ $attrs.auth.user.last_name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Total Attestations Available
                        </div>
                        <div class="card-body display-5 py-5">{{ capTotal }}</div>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Reserved Grad. Attestations
                        </div>
                        <div class="card-body display-5 py-5">{{ resGraduateCapTotal }}</div>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Available Attestations
                        </div>
                        <div class="card-body display-5 py-5">{{ totalRemaining }}</div>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Grad. Attestations
                        </div>
                        <div class="card-body display-5 py-3">
                            <div class="row">
                                <div class="col-6">
                                    <span class="fs-3">Issued</span><br>{{ issuedGrad }}
                                </div>
                                <div class="col-6">
                                    <span class="fs-3">Declined</span><br>{{ declinedGrad }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Undergrad. Attestations
                        </div>
                        <div class="card-body display-5 py-3">
                            <div class="row">
                                <div class="col-6">
                                    <span class="fs-3">Issued</span><br>{{ issued }}
                                </div>
                                <div class="col-6">
                                    <span class="fs-3">Declined</span><br>{{ declined }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex">
                    <div class="card text-center h-100 w-100">
                        <div class="card-header">
                            Remaining Undergrad. Attestations
                        </div>
                        <div class="card-body display-5 py-5">{{ undergradRemaining }}</div>
                    </div>
                </div>
                <!--                    <div class="col-md-6 mb-3">-->
                <!--                        <div class="card text-center">-->
                <!--                            <div class="card-header">-->
                <!--                                Attestations Used This Month-->
                <!--                            </div>-->
                <!--                            <div class="card-body display-5 m-4">100</div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="col-md-6 mb-3">-->
                <!--                        <div class="card text-center">-->
                <!--                            <div class="card-header">-->
                <!--                                Attestations Issued/Received-->
                <!--                            </div>-->
                <!--                            <div class="card-body display-5 m-4">3,000 / 2,800</div>-->

                <!--                        </div>-->
                <!--                    </div>-->

            </div>
        </div>
    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import {Link, Head} from '@inertiajs/vue3';

export default {
    name: 'Dashboard',
    components: {
        AuthenticatedLayout, Head, Link
    },
    props: {
        results: Object,
        capTotal: Object | Number | null,
        resGraduateCapTotal: Number | null,
        issued: Object | Number | null,
        declined: Object | Number | null,
        undergradRemaining: Number | null,
        totalRemaining: Number | null,
        issuedGrad: Number | null,
        declinedGrad: Number | null
    },
    data() {
        return {}
    },

    methods: {},
    computed: {},
    mounted() {
    },
}
</script>
