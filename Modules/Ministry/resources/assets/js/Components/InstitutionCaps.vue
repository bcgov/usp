<template>
    <div class="card mb-3">
        <div class="card-header">
            Institution Caps
            <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newInstCapModal">New Institution Cap</button>
        </div>
        <div class="card-body">
            <div v-if="results.caps != null && results.caps.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionCapsHeader></InstitutionCapsHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in results.caps">
                        <td><a href="#" @click="openEditForm(row)">{{ row.start_date }}</a></td>
                        <td>{{ row.end_date }}</td>
                        <td>{{ row.total_attestations}}</td>
                        <td>
                            <span v-if="row.status" class="badge rounded-pill text-bg-success">Active</span>
                            <span v-else class="badge rounded-pill text-bg-danger">Inactive</span>
                        </td>
                        <td>{{ row.comment}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>
        <div v-if="editCap == ''" class="modal modal-lg fade" id="newInstCapModal" tabindex="-1" aria-labelledby="newInstCapModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newInstCapModalLabel">New Institution Cap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionCapCreate :fedCaps="fedCaps" :results="results" />
                </div>
            </div>
        </div>
        <div v-if="editCap != ''" class="modal modal-lg fade" id="editInstCapModal" tabindex="0" aria-labelledby="editInstCapModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInstCapModalLabel">Edit Institution Cap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionCapEdit @close="closeEditForm" :cap="editCap" :fedCaps="fedCaps" :results="results" />
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import { Link } from '@inertiajs/vue3';
import InstitutionCapsHeader from "./InstitutionCapsHeader";
import InstitutionCapCreate from "./InstitutionCapCreate";
import InstitutionCapEdit from "./InstitutionCapEdit";
export default {
    name: 'InstitutionCaps',
    components: {
        Link, InstitutionCapsHeader, InstitutionCapCreate, InstitutionCapEdit
    },
    props: {
        results: Object,
        newInstCap: Object|null,
        fedCaps: Object
    },
    data() {
        return {
            editCap: ''
        }
    },
    methods: {
        openEditForm: function (cap){
            this.editCap = cap;
            $("#editInstCapModal").modal('show');
        },
        closeEditForm: function (){
            $("#editInstCapModal").modal('hide');
            this.editCap = '';
        }
    },
    mounted() {
    }
}
</script>
