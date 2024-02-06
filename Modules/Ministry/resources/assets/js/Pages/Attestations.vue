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
                                <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newAtteModal">New Attestation</button>
                            </div>
                            <div class="card-body">
                                <div v-if="results != null && results.data.length > 0" class="table-responsive pb-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <AttestationsHeader></AttestationsHeader>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, i) in attestationList">
                                            <td><Link :href="'/ministry/attestations/' + row.id">{{ row.student_name }}</Link></td>
                                            <td>{{ row.student_id_number }}</td>
                                            <td>{{ row.student_dob }}</td>
                                            <td><Link :href="'/ministry/institutions/' + row.institution.id">{{ row.institution.name }}</Link></td>
                                            <td>
                                                <div v-if="editStatusIndex !== i">{{ row.status }} <button @click="editStatus(i)" type="button" class="btn btn-link btn-sm"><i class="bi bi-pencil"></i></button></div>
                                                <Select v-if="editStatusIndex === i" @change="updateTrigger($event, i)" :key="row.id" class="form-select form-select-sm" aria-label="Status of the attestation">
                                                    <option v-for="stat in $attrs.utils['Attestation Status']"
                                                            :selected="row.status === stat.field_name"
                                                            :value="stat.field_name">{{ stat.field_name }}</option>
                                                </Select>
                                            </td>
                                            <td>{{ row.expiry_date }}</td>
                                            <td>{{ formatDate(row.created_at) }}</td>
                                            <td class="text-center">
                                                <a :href="'/ministry/attestations/download/' + row.id" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-down"></i>
                                                </a>
                                                <button v-if="editStatusIndex === i" @click="updateStatus($event, i)"
                                                        type="button" class="ms-1 btn btn-sm btn-warning">
                                                    confirm?
                                                </button>

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

        <div class="modal modal-lg fade" id="newAtteModal" tabindex="-1" aria-labelledby="newAtteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newAtteModalLabel">New Attestation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <AttestationCreate :institutions="institutions" :newAtte="newAtte" />
                </div>
            </div>
        </div>
<!--        <FormSubmitAlert :form-state="newAtteForm.formState" :success-msg="newAtteForm.formSuccessMsg"-->
<!--                         :fail-msg="newAtteForm.formFailMsg"></FormSubmitAlert>-->

    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import AttestationSearchBox from '../Components/AttestationSearch.vue';
import AttestationsHeader from '../Components/AttestationsHeader.vue';
import AttestationCreate from '../Components/AttestationCreate.vue';
import Pagination from "@/Components/Pagination";
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import Select from '@/Components/Select';
import { Link, Head, useForm } from '@inertiajs/vue3';

export default {
    name: 'Attestations',
    components: {
        AuthenticatedLayout, AttestationSearchBox, AttestationsHeader, Head, Link, Pagination, AttestationCreate,
        FormSubmitAlert, Select
    },
    props: {
        results: Object,
        institutions: Object,
        newAtte: Object|null
    },
    data() {
        return {
            newAtteForm: null,
            attestationList: '',
            editStatusIndex: '',
        }
    },

    methods: {
        editStatus: function (index) {
            this.editStatusIndex = index;
        },
        formatDate: function (value) {
            if(value !== undefined && value !== ''){
                let date = value.split("T");
                let time = date[1].split(".");

                return date[0] + " " + time[0];
            }
            return value;
        },
        updateTrigger: function (e, index) {
            this.attestationList[index].updated = true;
        },
        updateStatus: function (e, index) {
            console.log(e);
            e.preventDefault();

            let check = confirm("Are you sure you want to update this status from " + this.attestationList[index].status + " to " + e.target.value);
            if(check){
                this.newAtteForm = useForm(this.results[index]);
                this.newAtteForm.status = e.target.value;
                this.newAtteForm.put('/ministry/attestations', {
                    onSuccess: (response) => {

                        // console.log(response.props.institution)
                    },
                    onError: () => {
                    },
                    preserveState: true
                });
            }else {
                this.attestationList[index].status = this.results.data[index].status;
                this.editStatusIndex = '';
            }
            console.log(check);

        },
        showConfirm: function (index) {
            return this.attestationList[index].status !== this.results.data[index].status;
        },
    },
    computed: {

    },
    mounted() {
        this.attestationList = this.results.data;
    },
}
</script>
