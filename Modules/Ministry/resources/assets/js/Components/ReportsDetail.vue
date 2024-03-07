<template>
    <div class="card">
        <div class="card-header">
            <div>Detailed Reporting </div>
        </div>

        <div class="card-body">

        </div>

    </div>

</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';

export default {
    name: 'ReportsDetail',
    components: {
        BreezeInput, Link
    },
    props: {
        results: Object,
    },
    data() {
        return {
        }
    },
    methods: {

    }
}

</script>
