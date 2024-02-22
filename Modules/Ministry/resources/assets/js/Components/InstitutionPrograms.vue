<template>
    <div class="card mb-3">
        <div class="card-header">
            Programs
            <button type="button" class="btn btn-success btn-sm float-end" @click="openNewForm">New Program</button>
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
                            <td>
                                <span v-if="row.active_status" class="badge rounded-pill text-bg-success">Active</span>
                                <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

        <div v-if="showNewModal" class="modal modal-lg fade" id="newInstProgramModal" tabindex="-1" aria-labelledby="newInstProgramModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newInstProgramModalLabel">New Program</h5>
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
            showNewModal: false,
        }
    },
    methods: {
        openNewForm: function (){
            let vm = this;
            this.showNewModal = true;
            setTimeout(function(){
                $("#newInstProgramModal").modal('show')
                    .on('hidden.bs.modal', function () {
                        vm.showNewModal = false;
                    });
            }, 10);
        },
        openEditForm: function (program){
            let vm = this;
            this.editProgram = program;
            setTimeout(function(){$("#editInstProgramModal").modal('show').on('hidden.bs.modal', function () {
                vm.editProgram = '';
            });}, 10)
        },
        closeEditForm: function (){
            setTimeout(function(){$("#editInstProgramModal").modal('hide');}, 10)
            this.editProgram = '';
        }
    },
    mounted() {
        this.programList = this.results.programs;
    }
}
</script>
