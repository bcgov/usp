<template>
    <tr>
        <th scope="col">
            <a href="#" @click="switchSort('start_date')">
                <span>Start Date</span>
                <em v-if="sortClmn === 'start_date' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('end_date')">
                <span>End Date</span>
                <em v-if="sortClmn === 'end_date' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('total_attestations')">
                <span># Attestations</span>
                <em v-if="sortClmn === 'total_attestations' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col">
            <a href="#" @click="switchSort('total_reserved_graduate_attestations')">
                <span># Res. Grad. Attestations</span>
                <em v-if="sortClmn === 'total_reserved_graduate_attestations' && sortType === 'desc'" class="bi bi-sort-numeric-up"></em>
                <em v-else class="bi bi-sort-numeric-down"></em>
            </a>
        </th>
        <th scope="col" style="min-width: 100px;">
            <a href="#" @click="switchSort('status')">
                <span>Status</span>
                <em v-if="sortClmn === 'status' && sortType === 'desc'" class="bi bi-sort-alpha-up"></em>
                <em v-else class="bi bi-sort-alpha-down"></em>
            </a>
        </th>
    </tr>
</template>
<script>

import {Inertia} from "@inertiajs/inertia";

export default {
    name: 'FedCapsHeader',
    components: {},
    props: {},
    data() {
        return {
            sortClmn: 'start_date',
            sortType: 'asc',
            url: '',
            path: 'fed_caps',
        }
    },
    mounted() {
        this.url = new URL(document.location);
        this.sortClmn = this.url.searchParams.get("sort");
        this.sortType = this.url.searchParams.get("direction");

        if (this.url.pathname === '/fed_caps') {
            this.path = 'fed_caps';
        }

        let search = this.url.pathname.split('fed_caps-search/');
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
