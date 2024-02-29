<template>
    <div class="card mb-3">
        <div class="card-header">
            Institution Caps
            <template v-if="capStat != ''">
                <span class="badge rounded-pill text-bg-primary me-1">Active Cap Total: {{ capStat.instCap.total_attestations}}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Issued Attestations: {{ capStat.issued }}</span>
            </template>
            <button type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">New Cap</button>
        </div>
        <div class="card-body">
            <div v-if="results.caps != null && results.caps.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionCapsHeader></InstitutionCapsHeader>
                    </thead>
                    <tbody>
                    <template v-for="(row, i) in results.caps">
                        <tr v-if="row.program_guid === null">
                            <td>{{ row.start_date }}</td>
                            <td>{{ row.end_date }}</td>
                            <td>{{ row.total_attestations }}</td>
                            <td>{{ row.issued_attestations }}</td>
                            <td>
                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                            </td>
                            <td>{{ formatDate(row.created_at) }}</td>
                            <td>{{ row.comment }}</td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>
    </div>

    <div v-if="allowProgramCap" class="card mb-3">
        <div class="card-header">
            Program Caps
        </div>
        <div class="card-body">
            <div v-if="results.caps != null && results.caps.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionCapsProgramHeader></InstitutionCapsProgramHeader>
                    </thead>
                    <tbody>

                    <template v-for="(row, i) in results.caps">
                        <tr v-if="row.program_guid !== null">
                            <td>{{ row.start_date }}</td>
                            <td>{{ row.end_date }}</td>
                            <td>{{ row.total_attestations }}</td>
                            <td>{{ row.issued_attestations }}</td>
                            <td>{{ row.program.program_name }}</td>
                            <td>
                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                            </td>
                            <td>{{ formatDate(row.created_at) }}</td>
                            <td>{{ row.comment }}</td>
                        </tr>
                    </template>

                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

    </div>
    <div v-if="editCap == ''" class="modal modal-lg fade" id="newInstCapModal" tabindex="-1"
         aria-labelledby="newInstCapModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newInstCapModalLabel">New Institution Cap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <InstitutionCapCreate v-bind="$attrs" :fedCaps="fedCaps" :results="results" :activeInstCap="activeInstCap"/>
            </div>
        </div>
    </div>
    <div v-if="allowProgramCap && editCap != ''" class="modal modal-lg fade" id="editInstCapModal" tabindex="0"
         aria-labelledby="editInstCapModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInstCapModalLabel">Edit Institution Cap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <InstitutionCapEdit v-bind="$attrs" @close="closeEditForm" :cap="editCap" :fedCaps="fedCaps"
                                    :results="results" :activeInstCap="activeInstCap"/>
            </div>
        </div>
    </div>
</template>
<script>
import {Link} from '@inertiajs/vue3';
import InstitutionCapsHeader from "./InstitutionCapsHeader";
import InstitutionCapsProgramHeader from "./InstitutionCapsProgramHeader";
import InstitutionCapCreate from "./InstitutionCapCreate";
import InstitutionCapEdit from "./InstitutionCapEdit";

export default {
    name: 'InstitutionCaps',
    components: {
        Link, InstitutionCapsHeader, InstitutionCapsProgramHeader, InstitutionCapCreate, InstitutionCapEdit
    },
    props: {
        results: Object,
        newInstCap: Object | null,
        fedCaps: Object
    },
    data() {
        return {
            editCap: '',
            capStat: '',
            activeInstCap: null,
            allowProgramCap: false
        }
    },
    methods: {
        openNewForm: function () {
            setTimeout(function () {
                $("#newInstCapModal").modal('show');
            }, 10);
        },

        openEditForm: function (cap) {
            this.editCap = cap;
            setTimeout(function () {
                $("#editInstCapModal").modal('show');
            }, 10);
        },
        closeEditForm: function () {
            $("#editInstCapModal").modal('hide');
            this.editCap = '';
        },
        formatDate: function (value) {
            if (value !== undefined && value !== '') {
                let date = value.split("T");

                return date[0];
            }
            return value;
        },
        fetchCapStats: function () {
            let vm = this;
            let data = {
                institution_guid: this.results.guid,
            }
            axios.post('/ministry/api/fetch/capStats', data)
                .then(function (response) {
                    vm.capStat = response.data.body;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        }
    },
    mounted() {
        this.fetchCapStats();

        //look for inst active cap
        for (const cap of this.results.caps) {
            if (cap.program_guid === null && cap.active_status) {
                this.activeInstCap = cap;
                break;
            }
        }
    }
}
</script>
