<template>
    <Head title="Attestations" />

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            Attestations Search
                        </div>
                        <div class="card-body">
                            <AttestationSearchBox />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card mb-3">
                        <div class="card-header">
                            Attestations
                            <template v-if="capStat != ''">
                                <span class="badge rounded-pill text-bg-primary me-1">Active Cap Total: {{ capStat.instCap.total_attestations}}</span>
                                <span class="badge rounded-pill text-bg-primary me-1">Issued PALs: {{ capStat.issued }}</span>
                                <span class="badge rounded-pill text-bg-primary me-1">Remaining PALs: {{ capStat.instCap.total_attestations - capStat.issued }}</span>
                            </template>
                            <button type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">New Attestation</button>
                        </div>
                        <div class="card-body">
                            <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                <table class="table table-striped">
                                    <thead>
                                    <AttestationsHeader></AttestationsHeader>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(row, i) in attestationList">
                                        <td><button type="button" @click="openEditForm(row)" class="btn btn-link pb-0 pt-0">{{ row.last_name }}</button></td>
                                        <td>{{ row.first_name }}</td>
                                        <td>{{ row.id_number }}</td>
                                        <td><span v-if="row.program !== null">{{ row.program.program_name }}</span></td>
                                        <td>
                                            <div>
                                                <span v-if="row.status === 'Issued'" class="badge rounded-pill text-bg-success">Issued</span>
                                                <span v-if="row.status === 'Draft'" class="badge rounded-pill text-bg-warning">Draft</span>
                                                <span v-if="row.status === 'Received'" class="badge rounded-pill text-bg-primary">Received</span>
                                                <span v-if="row.status === 'Denied'" class="badge rounded-pill text-bg-danger">Denied</span>
                                            </div>
                                        </td>
                                        <td>{{ formatDate(row.created_at) }}</td>
                                        <td>{{ row.expiry_date }}</td>
                                        <td class="text-center">
                                            <a v-if="row.status !== 'Draft'" :href="'/institution/attestations/download/' + row.id" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-box-arrow-down"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <Pagination :links="results.links" :active-page="results.current_page" />
                            </div>
                            <h1 v-else class="lead">No results</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showNewModal" class="modal modal-lg" id="newAtteModal" tabindex="-1" aria-labelledby="newAtteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newAtteModalLabel">New Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <AttestationCreate :instCap="instCap" :error="error" v-bind="$attrs" :countries="countries" :institution="institution" :programs="programs" :newAtte="newAtte" />
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal modal-lg" id="editAtteModal" tabindex="0" aria-labelledby="editAtteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAtteModalLabel">Edit Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <AttestationEdit :instCap="instCap" :error="error" v-bind="$attrs" :countries="countries" :institution="institution" :programs="programs" :attestation="editRow" />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import AttestationSearchBox from '../Components/AttestationSearch.vue';
import AttestationsHeader from '../Components/AttestationsHeader.vue';
import AttestationCreate from '../Components/AttestationCreate.vue';
import AttestationEdit from '../Components/AttestationEdit.vue';
import Pagination from "@/Components/Pagination";
import { Link, Head } from '@inertiajs/vue3';

export default {
    name: 'Attestations',
    components: {
        AuthenticatedLayout, AttestationSearchBox, AttestationsHeader, Head, Link, AttestationCreate, AttestationEdit, Pagination
    },
    props: {
        results: Object,
        institution: Object,
        programs: Object,
        newAtte: Object|null,
        countries: Object,
        error: String|null,
        instCaps: Object|null,
        programCaps: Object|null,
        instCap: Object
    },
    data() {
        return {
            newAtteForm: null,
            attestationList: '',
            editRow: '',
            showNewModal: false,
            showEditModal: false,
            capStat: ''
        }
    },

    methods: {
        openNewForm: function (){
            let vm = this;
            this.showNewModal = true;
            setTimeout(function(){
                $("#newAtteModal").modal('show')
                    .on('hidden.bs.modal', function () {
                        vm.showNewModal = false;
                    });
            }, 10);
        },
        formatDate: function (value) {
            if(value !== undefined && value !== ''){
                let date = value.split("T");
                let time = date[1].split(".");

                return date[0];
            }
            return value;
        },
        openEditForm: function (row){
            let vm = this;
            this.editRow = row;
            this.showEditModal = true;
            setTimeout(function(){
                $("#editAtteModal").modal('show')
                    .on('hidden.bs.modal', function () {
                        vm.showEditModal = false;
                        vm.editRow = '';
                    });
            }, 10);
        },
        fetchCapStats: function () {
            let vm = this;
            let data = {
                institution_guid: this.institution.guid,
            }
            axios.post('/institution/api/fetch/capStats', data)
                .then(function (response) {
                    vm.capStat = response.data.body;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
        }
    },
    computed: {

    },
    mounted() {
        this.attestationList = this.results.data;
        this.fetchCapStats();
    },
}
</script>
