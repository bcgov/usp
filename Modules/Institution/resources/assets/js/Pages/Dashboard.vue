<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="display-5">{{ results.name }}</div>
                                <p>Welcome {{$attrs.auth.user.first_name}} {{$attrs.auth.user.last_name}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Total Attestations Allowed
                            </div>
                            <div class="card-body display-5 m-4">{{ capTotal }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Available Attestations
                            </div>
                            <div class="card-body display-5 m-4">{{ capTotal - issued }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Issued Attestations
                            </div>
                            <div class="card-body display-5 m-4">{{ issued }}</div>
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
import { Link, Head } from '@inertiajs/vue3';

export default {
    name: 'Dashboard',
    components: {
        AuthenticatedLayout, Head, Link
    },
    props: {
        results: Object,
        capTotal: Object|Number|null,
        issued: Object|Number|null
    },
    data() {
        return {
        }
    },

    methods: {

    },
    computed: {

    },
    mounted() {
    },
}
</script>
