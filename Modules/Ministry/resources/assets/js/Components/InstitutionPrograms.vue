<template>
    <div class="card mb-3">
        <div class="card-header">
            Programs
            <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newInstCapModal">New Program</button>
        </div>
        <div class="card-body">
            <div v-if="results.programs != null && results.programs.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionProgramsHeader></InstitutionProgramsHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in results.programs">
                        <td><a href="#" @click="openEditForm(row)">{{ row.program_name }}</a></td>
                        <td>{{ row.program_type }}</td>
                        <td>{{ row.credential }}</td>
                        <td>{{ row.expiry_date }}</td>
                        <td>
                            <span v-if="row.status" class="badge rounded-pill text-bg-success">Active</span>
                            <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="editProgram == ''" class="modal modal-lg fade" id="newInstProgramModal" tabindex="-1" aria-labelledby="newInstProgramModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newInstCapModalLabel">New Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionProgramCreate v-bind="$attrs" :results="results" />
                </div>
            </div>
        </div>
        <div v-if="editProgram != ''" class="modal modal-lg fade" id="editInstProgramModal" tabindex="0" aria-labelledby="editInstProgramModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInstProgramModalLabel">Edit Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionProgramEdit v-bind="$attrs" @close="closeEditForm" :program="editProgram" :results="results" />
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import {Head, Link, useForm} from '@inertiajs/vue3';
import InstitutionProgramsHeader from "./InstitutionProgramsHeader";
import Pagination from "@/Components/Pagination";
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import Select from '@/Components/Select';
import InstitutionProgramEdit from "./InstitutionProgramEdit";
import InstitutionProgramCreate from "./InstitutionProgramCreate";

export default {
    name: 'InstitutionPrograms',
    components: {
        Link, Pagination, InstitutionProgramsHeader,
        FormSubmitAlert, Select, InstitutionProgramEdit, InstitutionProgramCreate

    },
    props: {
        results: Object,
        newProgram: Object|null
    },
    data() {
        return {
            programList: '',
            newProgramForm: null,
            editProgram: '',
        }
    },
    methods: {
        openEditForm: function (program){
            this.editProgram = program;
            $("#editInstProgramModal").modal('show');
        },
        closeEditForm: function (){
            $("#editInstProgramModal").modal('hide');
            this.editProgram = '';
        }
    },
    mounted() {
        this.programList = this.results.programs;
    }
}
</script>
