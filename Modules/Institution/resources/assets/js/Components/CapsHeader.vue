<template>
    <tr>
        <th scope="col">Start Date</th>
        <th scope="col">End Date</th>
        <th scope="col">Total Atte.</th>
        <th scope="col">Issued Atte.</th>
        <th scope="col">Draft Atte.</th>
        <th scope="col">Status</th>
    </tr>
</template>
<script>

import {Inertia} from "@inertiajs/inertia";

export default {
    name: 'CapsHeader',
    components: {},
    props: {},
    data() {
        return {
            sortClmn: 'created_at',
            sortType: 'desc',
            url: '',
            path: 'attestations',
        }
    },
    mounted() {
        this.url = new URL(document.location);
        this.sortClmn = this.url.searchParams.get("sort");
        this.sortType = this.url.searchParams.get("direction");

        if (this.url.pathname === '/attestations') {
            this.path = 'attestations';
        }

        let search = this.url.pathname.split('attestation-search/');
        if (search.length > 1) {
            this.path = search[1];
        }
    },
    methods: {
        switchSort: function (clmn) {
            if (clmn === this.sortClmn) {
                if (this.sortType === 'asc') {
                    this.sortType = 'desc';
                } else {
                    this.sortType = 'asc';
                }
            } else {
                this.sortClmn = clmn;
                this.sortType = 'asc';
            }

            let data = {
                'direction': this.sortType,
                'sort': this.sortClmn
            };

            //if the url has filter_x params then append them all
            this.url.searchParams.forEach((value, key) => {
                let filter = key.split('filter_');
                if(filter.length > 1) {
                    data[key] = value;
                }
            });

            Inertia.get('/ministry/' + this.path, data, {
                preserveState: true
            });

        },
    }
};
</script>
