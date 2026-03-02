<template>
    <tr>
        <th scope="col">
            <span>Name</span>
        </th>
        <th scope="col">
            <span>Email</span>
        </th>
        <th scope="col">
            <span>User ID</span>
        </th>
        <th scope="col">
            <span>GUID</span>
        </th>
        <th scope="col">
            <span>Role</span>
        </th>
        <th scope="col">
            <span>Status</span>
        </th>
        <th></th>
    </tr>
</template>
<script>

import { router } from '@inertiajs/vue3';

export default {
    name: 'InstitutionStaffHeader',
    components: {},
    props: {},
    data() {
        return {
            sortClmn: 'bceid_user_name',
            sortType: 'asc',
            url: '',
            path: 'staff',
        }
    },
    mounted() {

    },
    methods: {

    }
};
</script>
