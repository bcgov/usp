<template>
    <tr>
        <th scope="col">
            <a href="#" @click="switchSort('student_name')">
                <span>Student Name</span>
                <em v-if="sortClmn === 'student_name' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('student_id_number')">
                <span>ID</span>
                <em v-if="sortClmn === 'name' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('student_dob')">
                <span>Date of Birth</span>
                <em v-if="sortClmn === 'active_status' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
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
            <a href="#" @click="switchSort('expiry_date')">
                <span>Expiry Date</span>
                <em v-if="sortClmn === 'expiry_date' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('created_at')">
                <span>Create Date</span>
                <em v-if="sortClmn === 'created_at' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
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

        if (this.url.pathname === '/dashboard') {
            this.path = 'dashboard';
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
