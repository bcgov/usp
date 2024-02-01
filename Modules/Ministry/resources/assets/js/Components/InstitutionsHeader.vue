<template>
    <tr>
        <th scope="col">
            <a href="#" @click="switchSort('dli')">
                <span>DLI</span>
                <em v-if="sortClmn === 'dli' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('name')">
                <span>Name</span>
                <em v-if="sortClmn === 'name' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('active_status')">
                <span>Status</span>
                <em v-if="sortClmn === 'active_status' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('standing_status')">
                <span>Standing Status</span>
                <em v-if="sortClmn === 'standing_status' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
    </tr>
</template>
<script>

import {Inertia} from "@inertiajs/inertia";

export default {
    name: 'InstitutionsHeader',
    components: {},
    props: {},
    data() {
        return {
            sortClmn: 'name',
            sortType: 'asc',
            url: '',
            path: 'institutions',
        }
    },
    mounted() {
        this.url = new URL(document.location);
        this.sortClmn = this.url.searchParams.get("sort");
        this.sortType = this.url.searchParams.get("direction");

        if (this.url.pathname === '/dashboard') {
            this.path = 'dashboard';
        }

        let search = this.url.pathname.split('institution-search/');
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
