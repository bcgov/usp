<template>
    <tr>
        <th scope="col">
            <a href="#" @click="switchSort('last_name')">
                <span>Last Name</span>
                <em v-if="sortClmn === 'last_name' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('first_name')">
                <span>First Name</span>
                <em v-if="sortClmn === 'first_name' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('id_number')">
                <span>Student Number</span>
                <em v-if="sortClmn === 'id_number' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-up"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <span>Institution</span>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('status')">
                <span>Status</span>
                <em v-if="sortClmn === 'status' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('issue_date')">
                <span>Issue Date</span>
                <em v-if="sortClmn === 'created_at' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('expiry_date')">
                <span>Expiry Date</span>
                <em v-if="sortClmn === 'expiry_date' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th></th>
    </tr>
</template>
<script>

import {Inertia} from "@inertiajs/inertia";

export default {
    name: 'AttestationsHeader',
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
