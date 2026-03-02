<template>
    <tr>
        <th scope="col">
                <span>Program Name</span>
        </th>
        <th scope="col">
                <span>Program Type</span>
        </th>
        <th scope="col">
                <span>Credential</span>
        </th>
        <th scope="col">
                <span>Status</span>
        </th>
    </tr>
</template>
<script>

import { router } from '@inertiajs/vue3';

export default {
    name: 'InstitutionProgramsHeader',
    components: {},
    props: {},
    data() {
        return {
            sortClmn: 'program_name',
            sortType: 'asc',
            url: '',
            path: 'programs',
        }
    },
    mounted() {
        this.url = new URL(document.location);
        this.sortClmn = this.url.searchParams.get("sort");
        this.sortType = this.url.searchParams.get("direction");

        if (this.url.pathname === '/programs') {
            this.path = 'programs';
        }

        let search = this.url.pathname.split('programs-search/');
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

            router.get('/ministry/' + this.path, data, {
                preserveState: true
            });

        },
    }
};
</script>
