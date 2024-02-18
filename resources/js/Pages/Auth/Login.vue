<script>
import GuestLayout from '@/Layouts/Guest.vue';
import {Head} from '@inertiajs/vue3';

export default {
    name: 'Login',
    components: {
        GuestLayout, Head
    },
    props: {
        loginAttempt: Boolean,
        hasAccess: Boolean,
        status: String,
    }
}
</script>

<style scoped>
.btn-bc-gov{
    background-image: none;
    background-color: #003366;
    border: none;
    border-radius: 4px;
    color: white;
    text-align: center;
    text-decoration: none;
    display: block;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    margin-left: auto;
    margin-right: auto;
}
.btn-bc-gov:hover{
    text-decoration: underline !important;
    opacity: 0.80 !important;
}
img.bceid_rev{
    width: 50px;
    display: inline;
}
</style>
<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>


        <div class="row">
            <div class="col-12 mb-3">
                <a href="/applogin" class="btn btn-bc-gov mb-3 w-100 fw-light">Ministry Staff Login with IDIR</a>
                <a href="/bceid-login" class="btn btn-bc-gov mb-3 w-100 fw-light">Institution Login with
                    <img src="https://www.bceid.ca/assets/images/bceid_rev.png" class="bceid_rev"></a>
            </div>
        </div>
    </GuestLayout>
</template>
