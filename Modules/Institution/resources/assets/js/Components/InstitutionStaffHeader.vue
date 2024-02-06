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
    </tr>
</template>
<script>

import {Inertia} from "@inertiajs/inertia";

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
        // this.url = new URL(document.location);
        // this.sortClmn = this.url.searchParams.get("sort");
        // this.sortType = this.url.searchParams.get("direction");
        //
        // if (this.url.pathname === '/caps') {
        //     this.path = 'caps';
        // }
        //
        // let search = this.url.pathname.split('caps-search/');
        // if (search.length > 1) {
        //     this.path = search[1];
        // }
    },
    methods: {
        // switchSort: function (clmn) {
        //     if (clmn === this.sortClmn) {
        //         if (this.sortType === 'asc') {
        //             this.sortType = 'desc';
        //         } else {
        //             this.sortType = 'asc';
        //         }
        //     } else {
        //         this.sortClmn = clmn;
        //         this.sortType = 'asc';
        //     }
        //
        //     let data = {
        //         'direction': this.sortType,
        //         'sort': this.sortClmn
        //     };
        //
        //     //if the url has filter_x params then append them all
        //     this.url.searchParams.forEach((value, key) => {
        //         let filter = key.split('filter_');
        //         if(filter.length > 1) {
        //             data[key] = value;
        //         }
        //     });
        //
        //     Inertia.get('/ministry/' + this.path, data, {
        //         preserveState: true
        //     });
        //
        // },
    }
};
</script>
