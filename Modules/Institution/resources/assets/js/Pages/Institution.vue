<template>
    <Head title="Account Information" />

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="card mb-3">
                        <div class="card-header">
                            Account Information
                        </div>
                        <div class="card-body">

                            <form class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <Label for="inputName" class="form-label" value="Name" />
                                        <strong>{{institution.name}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputAddress1" class="form-label" value="Address 1" />
                                        <strong>{{institution.address1}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputBceidBizGuid" class="form-label" value="BCeID Business Guid" />
                                        <strong>{{institution.bceid_business_guid}}</strong>
                                    </div>

                                    <div class="col-md-4">
                                        <Label for="inputDli" class="form-label" value="DLI #" />
                                        <strong>{{institution.dli}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputPrimaryContact" class="form-label" value="Primary Contact" />
                                        <strong>{{institution.primary_contact}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputPrimaryEmail" class="form-label" value="Primary Email" />
                                        <strong>{{institution.primary_email}}</strong>
                                    </div>

                                    <div class="col-md-4">
                                        <Label for="inputCity" class="form-label" value="City" />
                                        <strong>{{institution.city}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputPostalCode" class="form-label" value="Postal Code" />
                                        <strong>{{institution.postal_code}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputProvince" class="form-label" value="Province" />
                                        <strong>{{institution.province}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputPublic" class="form-label" value="Public?" />
                                        <strong>{{institution.public}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputActiveStatus" class="form-label" value="Active?" />
                                        <strong>{{institution.active_status}}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <Label for="inputStandingStatus" class="form-label" value="Status - in good standing?" />
                                        <strong>{{institution.standing_status}}</strong>
                                    </div>


                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import { Link, Head } from '@inertiajs/vue3';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';

export default {
    name: 'Institution',
    components: {
        AuthenticatedLayout, Head, Link, Input, Label
    },
    props: {
        institution: Object,
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
