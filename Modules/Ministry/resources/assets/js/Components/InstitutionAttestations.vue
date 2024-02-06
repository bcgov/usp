<template>
    <div class="card mb-3">
        <div class="card-header">
            Attestations
            <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newInstCapModal">New Attestation</button>
        </div>
        <div class="card-body">
            <div v-if="results.attestations != null && results.attestations.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionAttestationsHeader></InstitutionAttestationsHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in results.attestations">
                        <td><Link :href="'/ministry/attestations/' + row.id">{{ row.student_name }}</Link></td>
                        <td>{{ row.student_id_number }}</td>
                        <td>{{ row.student_dob }}</td>
                        <td><Link :href="'/ministry/institutions/' + results.id">{{ results.name }}</Link></td>
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
<!--                <Pagination :links="results.links" :active-page="results.current_page" />-->
            </div>
        </div>

    </div>

</template>
<script>
import {Head, Link, useForm} from '@inertiajs/vue3';
import InstitutionAttestationsHeader from "./InstitutionAttestationsHeader";
import Pagination from "@/Components/Pagination";
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import Select from '@/Components/Select';

export default {
    name: 'InstitutionAttestations',
    components: {
        Link, Pagination, InstitutionAttestationsHeader,
        FormSubmitAlert, Select

    },
    props: {
        results: Object,
        newAtte: Object|null
    },
    data() {
        return {
            attestationList: '',
            newAtteForm: null,
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
                this.newAtteForm = useForm(this.results.attestations[index]);
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
                this.attestationList[index].status = this.results.attestations[index].status;
                this.editStatusIndex = '';
            }
            console.log(check);

        },
        showConfirm: function (index) {
            return this.attestationList[index].status !== this.results.data[index].status;
        },
    },
    mounted() {
        this.attestationList = this.results.attestations;
    }
}
</script>
