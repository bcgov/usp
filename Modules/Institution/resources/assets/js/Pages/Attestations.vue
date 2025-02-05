<template>
    <Head title="Attestations"/>

    <AuthenticatedLayout v-bind="$attrs">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            Attestations Search
                        </div>
                        <div class="card-body">
                            <AttestationSearchBox/>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div v-if="updateFedCap" class="above">&nbsp;</div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-10">
                                    Attestations
                                    <template v-if="capStat != '' && capStat.instCap != null">
                                        <span
                                            class="badge rounded-pill text-bg-primary me-1">Active Cap Total: {{ capStat.instCap.total_attestations }}</span>
                                        <span class="badge rounded-pill text-bg-primary me-1">Active Res. Grad.: {{ capStat.instCap.total_reserved_graduate_attestations }}</span>
                                        <span class="badge rounded-pill text-bg-primary me-1">Issued PALs: {{
                                                capStat.issued
                                            }}</span>
                                        <span class="badge rounded-pill text-bg-primary me-1">Remaining PALs: {{
                                                capStat.instCap.total_attestations - capStat.issued
                                            }}</span>
                                        <span class="badge rounded-pill text-bg-primary me-1">Issued Grad. PALs: {{
                                                capStat.resGradIssued
                                            }}</span>
                                        <span class="badge rounded-pill text-bg-primary me-1">Issued Undegrad. PALs: {{
                                                capStat.issued - capStat.resGradIssued
                                            }}</span>
                                    </template>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">
                                        New Attestation
                                    </button>
                                    <a href="/institution/attestations/export" target="_blank"
                                       class="btn btn-outline-success btn-sm float-end me-1"
                                       title="Export Attestations"><i class="bi bi-filetype-csv"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div v-if="warning" class="alert alert-warning mt-3">
                                    {{ warning }}
                                </div>
                                <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                    <table class="table table-striped">
                                        <thead>
                                        <AttestationsHeader></AttestationsHeader>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, i) in attestationList">
                                            <td>
                                                <button type="button" @click="openEditForm(row)"
                                                        class="btn btn-link p-0 text-start">{{ row.last_name }}
                                                </button>
                                            </td>
                                            <td>{{ row.first_name }}</td>
                                            <td>{{ row.student_number }}</td>
                                            <td><span v-if="row.program !== null">{{ row.program.program_name }}</span></td>
                                            <td>{{ row.program && row.program.program_graduate ? 'Graduate' : 'Undergraduate' }}</td>
                                            <td>
                                                <div>
                                                    <span v-if="row.status === 'Issued'"
                                                          class="badge rounded-pill text-bg-success">Issued</span>
                                                    <span v-if="row.status === 'Draft'"
                                                          class="badge rounded-pill text-bg-warning">Draft</span>
                                                    <span v-if="row.status === 'Received'"
                                                          class="badge rounded-pill text-bg-primary">Received</span>
                                                    <span v-if="row.status === 'Declined'"
                                                          class="badge rounded-pill text-bg-danger">Declined</span>
                                                </div>
                                            </td>
                                            <td>{{ row.issue_date }}</td>
                                            <td>{{ row.expiry_date }}</td>
                                            <td class="text-center">
                                                <a v-if="row.status === 'Issued'"
                                                   :href="'/institution/attestations/download/' + row.id"
                                                   target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-down"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination :links="results.links" :active-page="results.current_page"/>
                                </div>
                                <h1 v-else class="lead">No results</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showNewModal" class="modal modal-lg" id="newAtteModal" tabindex="-1"
                 aria-labelledby="newAtteModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newAtteModalLabel">New Attestation <span class=" fs-6" v-if="instCap !== null">for PY: {{ instCap.start_date }} to {{ instCap.end_date }}</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div v-if="instCap === null" class="modal-body">
                            <div class="g-3">You have no active cap</div>
                        </div>
                        <AttestationCreate v-else :instCap="instCap" :error="error" v-bind="$attrs"
                                           :countries="countries" :institution="institution" :programs="programs"
                                           :newAtte="newAtte"/>
                    </div>
                </div>
            </div>
            <div v-if="showEditModal" class="modal modal-lg" id="editAtteModal" tabindex="0"
                 aria-labelledby="editAtteModalLabel" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title me-2" id="editAtteModalLabel">Edit Attestation <span class="fs-6" v-if="instCap !== null">for PY: {{ instCap.start_date }} to {{ instCap.end_date }}</span></h5>
                            <strong>Issued by: {{ editRow.issued_by_name }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <AttestationEdit :instCap="instCap" :error="error" v-bind="$attrs" :countries="countries"
                                         :institution="institution" :programs="programs" :attestation="editRow"/>
                        <div v-if="editRow.status === 'Issued'" class="modal-footer justify-content-between">
                            <a :href="'/institution/attestations/download/' + editRow.id" target="_blank"
                               class="btn btn-success">
                                Download <i class="bi bi-box-arrow-down"></i>
                            </a>
                            <button @click="duplicate" type="button" class="btn btn-secondary">Replicate &amp; Issue
                            </button>
                        </div>
                    </div>
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
import {Link, Head, useForm} from '@inertiajs/vue3';

export default {
    name: 'Attestations',
    components: {
        AuthenticatedLayout,
        AttestationSearchBox,
        AttestationsHeader,
        Head,
        Link,
        AttestationCreate,
        AttestationEdit,
        Pagination
    },
    props: {
        results: Object,
        institution: Object,
        programs: Object,
        newAtte: Object | null,
        countries: Object,
        error: String | null,
        instCaps: Object | null,
        programCaps: Object | null,
        instCap: Object,
        warning: String | null,
    },
    data() {
        return {
            newAtteForm: null,
            attestationList: '',
            editRow: '',
            showNewModal: false,
            showEditModal: false,
            capStat: '',
            updateFedCap: false
        }
    },

    methods: {
        duplicate: function () {
            let check = confirm('Are you sure you want to replicate and issue this attestation? This will result in ' +
                'changing the status of the existing one to DECLINED and to use your available CAP to issue the new one if possible.');
            if (check) {
                let duplicateForm = useForm({
                    formState: null,
                    formSuccessMsg: 'Form was submitted successfully.',
                    formFailMsg: 'There was an error submitting this form.',
                    old_guid: this.editRow.guid,
                })
                duplicateForm.formState = null;
                duplicateForm.post('/institution/duplicate_attestations', {
                    onSuccess: (response) => {
                        $("#editAtteModal").modal('hide');

                        this.$inertia.visit('/institution/attestations');
                    },
                    onError: () => {
                        duplicateForm.formState = false;
                    },
                    preserveState: true
                });
            }
        },
        openNewForm: function () {
            let vm = this;
            this.showNewModal = true;
            setTimeout(function () {
                $("#newAtteModal").modal('show')
                    .on('hidden.bs.modal', function () {
                        vm.showNewModal = false;
                    });
            }, 10);
        },
        formatDate: function (value) {
            if (value !== undefined && value !== '') {
                let date = value.split("T");
                let time = date[1].split(".");

                return date[0];
            }
            return value;
        },
        openEditForm: function (row) {
            let vm = this;
            this.editRow = row;
            this.showEditModal = true;
            setTimeout(function () {
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
        },
        handleFedCapUpdate(event) {
            this.updateFedCap = event.detail.updateFedCap;
        }
    },
    beforeUnmount() {
        window.removeEventListener('update-fed-cap', this.handleFedCapUpdate);
    },
    mounted() {
        this.attestationList = this.results.data;
        window.addEventListener('update-fed-cap', this.handleFedCapUpdate);
        this.fetchCapStats();
    },
}
</script>
<style scoped>
.above{
    display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    background: #efefef7d;
    z-index: 111;
}
</style>
