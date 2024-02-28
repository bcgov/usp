<template>
    <Head title="Attestations" />

    <AuthenticatedLayout v-bind="$attrs">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Attestation Search
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
                                            <td><button type="button" @click="openEditForm(row)" class="btn btn-link p-0">{{ row.last_name }}</button></td>
                                            <td>{{ row.first_name }}</td>
                                            <td>{{ row.student_number }}</td>
                                            <td><Link :href="'/ministry/institutions/' + row.institution.id">{{ row.institution.name }}</Link></td>
                                            <td>
                                                <div>
                                                    <span v-if="row.status === 'Issued'" class="badge rounded-pill text-bg-success">Issued</span>
                                                    <span v-if="row.status === 'Draft'" class="badge rounded-pill text-bg-warning">Draft</span>
                                                    <span v-if="row.status === 'Received'" class="badge rounded-pill text-bg-primary">Received</span>
                                                    <span v-if="row.status === 'Denied'" class="badge rounded-pill text-bg-danger">Denied</span>
                                                </div>
                                            </td>
                                            <td>{{ row.expiry_date }}</td>
                                            <td>{{ formatDate(row.created_at) }}</td>
                                            <td class="text-center">
                                                <a v-if="row.status !== 'Draft'" :href="'/ministry/attestations/download/' + row.id" target="_blank" class="btn btn-sm btn-outline-secondary">
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

        <div v-if="showNewModal" class="modal modal-lg fade" id="newAtteModal" tabindex="-1" aria-labelledby="newAtteModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newAtteModalLabel">New Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <AttestationCreate :countries="countries" :institutions="institutions" :newAtte="newAtte" />
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal modal-lg fade" id="editAtteModal" tabindex="0" aria-labelledby="editAtteModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAtteModalLabel">Edit Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <AttestationEdit v-bind="$attrs" :countries="countries" :institutions="institutions" :institution="editRow.institution" :attestation="editRow" />
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
        AuthenticatedLayout, AttestationSearchBox, AttestationsHeader, Head, Link, AttestationCreate, Pagination, AttestationEdit
    },
    props: {
        results: Object,
        institutions: Object,
        newAtte: Object|null,
        countries: Object
    },
    data() {
        return {
            newAtteForm: null,
            attestationList: '',
            editRow: '',
            showNewModal: false,
            showEditModal: false
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

                // return date[0] + " " + time[0];
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
    },
    computed: {

    },
    mounted() {
        this.attestationList = this.results.data;
    },
}
</script>
