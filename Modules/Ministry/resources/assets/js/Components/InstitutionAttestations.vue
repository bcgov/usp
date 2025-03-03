<template>
    <div class="card mb-3">
        <div class="card-header">
            Attestations
            <template v-if="capStat != '' && capStat.instCap != null">
                <span class="badge rounded-pill text-bg-primary me-1">Active Cap Total: {{ capStat.instCap.total_attestations }}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Active Res. Grad.: {{ capStat.instCap.total_reserved_graduate_attestations }}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Issued PALs: {{ capStat.issued }}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Remaining PALs: {{ capStat.totalRemaining }}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Issued Grad. PALs: {{ capStat.gradIssued }}</span>
                <span class="badge rounded-pill text-bg-primary me-1">Issued Undegrad. PALs: {{ capStat.remainingUndergrad }}</span>
            </template>
<!--            <button v-if="results.active_caps.length > 0" type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">New Attestation</button>-->
        </div>
        <div class="card-body">
            <div v-if="attestationList !== '' && attestationList.data.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionAttestationsHeader></InstitutionAttestationsHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in attestationList.data">
                        <td><button type="button" @click="openEditForm(row)" class="btn btn-link pb-0 pt-0">{{ row.last_name }}</button></td>
                        <td>{{ row.first_name }}</td>
                        <td>{{ row.student_number }}</td>
                        <td><Link :href="'/ministry/institutions/' + results.id">{{ results.name }}</Link></td>
                        <td>
                            <div>
                                <span v-if="row.status === 'Issued'" class="badge rounded-pill text-bg-success">Issued</span>
                                <span v-if="row.status === 'Draft'" class="badge rounded-pill text-bg-warning">Draft</span>
                                <span v-if="row.status === 'Received'" class="badge rounded-pill text-bg-primary">Received</span>
                                <span v-if="row.status === 'Declined'" class="badge rounded-pill text-bg-danger">Declined</span>
                                <span v-if="row.status === 'Cancelled Draft'" class="badge rounded-pill text-bg-secondary">Cancelled Draft</span>
                            </div>
                        </td>
                        <td>{{ row.issue_date }}</td>
                        <td>{{ row.expiry_date }}</td>
                        <td class="text-center">
                            <a v-if="row.status === 'Issued' || row.status === 'Declined'" :href="'/ministry/attestations/download/' + row.id" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-down"></i>
                            </a>
                        </td>

                    </tr>
                    </tbody>
                </table>
                <div v-if="attestationList.links.length > 3">
                    <div class="d-flex flex-row justify-content-center -mb-1">
                        <template v-for="(link, key) in attestationList.links">
                            <button v-if="key === 0" :key="`link-${key}`" class="btn btn-light btn-link border m-1" :class="{ 'disabled': (link.label == attestationList.current_page) }" @click="fetchAttestations(1)">First</button>
                            <button v-if="key > 0 && key < attestationList.links.length-1" :key="`link-${key}`" class="btn btn-light btn-link border m-1" :class="{ 'disabled': (link.label == attestationList.current_page) }" @click="fetchAttestations(link.label)" v-html="link.label" />
                            <button v-if="key === attestationList.links.length-1" :key="`link-${key}`" class="btn btn-light btn-link border m-1" :class="{ 'disabled': (link.label == attestationList.current_page) }" @click="fetchAttestations(attestationList.last_page)">Last</button>
                        </template>
                    </div>
                </div>

            </div>
            <h1 v-else-if="results.active_caps.length === 0" class="lead">You have no active institution caps.</h1>
            <h1 v-else class="lead">No results.</h1>

            </div>
        <div v-if="showEditModal" class="modal modal-lg fade" id="editAtteModal" tabindex="0" aria-labelledby="editAtteModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <InstitutionAttestationEdit v-bind="$attrs" :cap="capStat.instCap" :countries="countries" :institution="results" :attestation="editRow" />
            </div>
        </div>
    </div>

</template>
<script>
import { Link, useForm } from '@inertiajs/vue3';
import InstitutionAttestationsHeader from "./InstitutionAttestationsHeader";
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import Select from '@/Components/Select';
import InstitutionAttestationCreate from "./InstitutionAttestationCreate";
import InstitutionAttestationEdit from "./InstitutionAttestationEdit";

export default {
    name: 'InstitutionAttestations',
    components: {
        Link, InstitutionAttestationsHeader,
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
        fetchAttestations: function (page = 1) {
            let vm = this;
            axios.get('/ministry/api/fetch/attestations?g=' + this.results.guid + "&page=" + page)
                .then(function (response) {
                    vm.attestationList = response.data.body;
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                });
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
        this.fetchAttestations();
        this.fetchCapStats();
    }
}
</script>
