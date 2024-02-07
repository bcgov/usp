<template>
    <div class="card mb-3">
        <div class="card-header">
            Attestations
            <button v-if="results.active_caps.length > 0" type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">New Attestation</button>
        </div>
        <div class="card-body">
            <div v-if="results.attestations != null && results.attestations.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionAttestationsHeader></InstitutionAttestationsHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in results.attestations">
                        <td><button type="button" @click="openEditForm(row)" class="btn btn-link pb-0 pt-0">{{ row.first_name }}</button></td>
                        <td>{{ row.last_name }}</td>
                        <td>{{ row.dob }}</td>
                        <td><Link :href="'/ministry/institutions/' + results.id">{{ results.name }}</Link></td>
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
            </div>
            <h1 v-else-if="results.active_caps.length === 0" class="lead">You have no active institution caps.</h1>
            <h1 v-else class="lead">No results.</h1>

            </div>
        <div v-if="showNewModal" class="modal modal-lg fade" id="newAtteModal" tabindex="-1" aria-labelledby="newAtteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newAtteModalLabel">New Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionAttestationCreate :countries="countries" :institution="results" :newAtte="newAtte" />
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal modal-lg fade" id="editAtteModal" tabindex="0" aria-labelledby="editAtteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAtteModalLabel">Edit Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionAttestationEdit v-bind="$attrs" :countries="countries" :institution="results" :attestation="editRow" />
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import { Link, useForm } from '@inertiajs/vue3';
import InstitutionAttestationsHeader from "./InstitutionAttestationsHeader";
import Pagination from "@/Components/Pagination";
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import Select from '@/Components/Select';
import InstitutionAttestationCreate from "./InstitutionAttestationCreate";
import InstitutionAttestationEdit from "./InstitutionAttestationEdit";

export default {
    name: 'InstitutionAttestations',
    components: {
        Link, Pagination, InstitutionAttestationsHeader,
        FormSubmitAlert, Select, InstitutionAttestationCreate, InstitutionAttestationEdit
    },
    props: {
        results: Object,
        newAtte: Object|null,
        countries: Object
    },
    data() {
        return {
            attestationList: '',
            newAtteForm: null,
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

        formatDate: function (value) {
            if(value !== undefined && value !== ''){
                let date = value.split("T");
                let time = date[1].split(".");

                // return date[0] + " " + time[0];
                return date[0];
            }
            return value;
        },

    },
    mounted() {
        this.attestationList = this.results.attestations;
    }
}
</script>
