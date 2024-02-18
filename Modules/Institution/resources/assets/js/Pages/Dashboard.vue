<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Attestations Allowed
                            </div>
                            <div class="card-body display-5 m-4">5,000</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Attestations Available
                            </div>
                            <div class="card-body display-5 m-4">2,000</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Attestations Used This Month
                            </div>
                            <div class="card-body display-5 m-4">100</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-center">
                            <div class="card-header">
                                Attestations Issued/Received
                            </div>
                            <div class="card-body display-5 m-4">3,000 / 2,800</div>

                        </div>
                    </div>

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
        instCaps: Object|null,
        programCaps: Object|null
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
