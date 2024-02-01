<template>
    <div class="card">
        <div class="card-header">School Maintenance
            <button type="button" class="btn btn-sm btn-success float-end" data-bs-toggle="modal" data-bs-target="#newSchoolModal">New School</button>
        </div>

        <div class="modal modal-lg fade" id="newSchoolModal" tabindex="-1" aria-labelledby="newSchoolModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newSchoolModalLabel">Add New School</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="newSchool">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <BreezeLabel for="new_school_code" value="School Code" />
                                        <BreezeInput id="new_school_code" class="form-control" type="text" v-model="newForm.institution_code" :disabled="newForm.processing" />
                                    </div>
                                    <div class="col-lg-6">
                                        <BreezeLabel for="new_school_name" value="School Name" />
                                        <BreezeInput id="new_school_name" class="form-control" type="text" v-model="newForm.institution_name" :disabled="newForm.processing" />
                                    </div>
                                    <div class="col-lg-2">
                                        <BreezeLabel for="new_school_location_code" value="Location Code" />
                                        <BreezeInput id="new_school_location_code" class="form-control" type="text" v-model="newForm.institution_location_code" :disabled="newForm.processing" />
                                    </div>
                                    <div class="col-lg-2">
                                        <BreezeLabel for="new_school_type_code" value="Type Code" />
                                        <BreezeInput id="new_school_type_code" class="form-control" type="text" v-model="newForm.institution_type_code" :disabled="newForm.processing" />
                                    </div>
                                </div>

                                <div v-if="newForm.errors != undefined" class="row">
                                    <div class="col-12">
                                        <div v-if="newForm.hasErrors == true" class="alert alert-danger mt-3">
                                            <ul>
                                                <li v-for="err in newForm.errors"><small>{{ err }}</small></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn me-2 btn-outline-success" :disabled="newForm.processing">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-lg fade" id="editSchoolModal" tabindex="-1" aria-labelledby="editSchoolModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSchoolModalLabel">Edit School</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="editSchool">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <BreezeLabel for="edit_school_code" value="School Code" />
                                        <BreezeInput id="edit_school_code" class="form-control" type="text" v-model="editForm.institution_code" :disabled="editForm.processing" />
                                    </div>
                                    <div class="col-lg-6">
                                        <BreezeLabel for="edit_school_name" value="School Name" />
                                        <BreezeInput id="edit_school_name" class="form-control" type="text" v-model="editForm.institution_name" :disabled="editForm.processing" />
                                    </div>
                                    <div class="col-lg-2">
                                        <BreezeLabel for="edit_school_location_code" value="Location Code" />
                                        <BreezeInput id="edit_school_location_code" class="form-control" type="text" v-model="editForm.institution_location_code" :disabled="editForm.processing" />
                                    </div>
                                    <div class="col-lg-2">
                                        <BreezeLabel for="edit_school_type_code" value="Type Code" />
                                        <BreezeInput id="edit_school_type_code" class="form-control" type="text" v-model="editForm.institution_type_code" :disabled="editForm.processing" />
                                    </div>
                                </div>

                                <div v-if="editForm.errors != undefined" class="row">
                                    <div class="col-12">
                                        <div v-if="editForm.hasErrors == true" class="alert alert-danger mt-3">
                                            <ul>
                                                <li v-for="err in editForm.errors"><small>{{ err }}</small></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn me-2 btn-outline-success" :disabled="editForm.processing">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div v-if="filterResults != null" class="table-responsive pb-3">
                <table aria-label="School Maintenance List" class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">School Code</th>
                        <th scope="col">
                            <div v-if="filterActive" class="input-group">
                                <input @keydown="filterList" @keyup.esc="toggleFilter" v-model="filterKey" type="text" class="form-control" id="school_filter" placeholder="type keyword to filter the list" aria-label="Filter schools" aria-describedby="basic-close">
                                <a @click="toggleFilter" href="#" class="input-group-text" id="basic-close">X</a>
                            </div>
                            <a v-else href="#" @click="toggleFilter">School Name</a>
                        </th>
                        <th scope="col">Location Code</th>
                        <th scope="col">Location Type</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in filterResults">
                        <th scope="row"><button @click="editRow(row,i)" type="button" class="btn btn-sm btn-link">{{ row.institution_code }}</button></th>
                        <td>{{ row.institution_name }}</td>
                        <td>{{ row.institution_location_code }}</td>
                        <td>{{ row.institution_type_code }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

        <div v-if="showSuccessMsg" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">
                        Form was submitted successfully.
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div v-if="showFailMsg" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateFailAlert" class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">
                        There was an error submitting this form.
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

    </div>

</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';

export default {
    name: 'MaintenanceSchool',
    components: {
        BreezeInput, BreezeLabel, Link
    },
    props: {
        results: Object,
    },
    data() {
        return {
            showSuccessMsg: false,
            showFailMsg: false,

            newForm: useForm({
                institution_code: '',
                institution_name: '',
                institution_location_code: '',
                institution_type_code: '',
            }),
            editForm: useForm({
                institution_code: '',
                institution_name: '',
                institution_location_code: '',
                institution_type_code: '',
                id: '',
            }),

            filterResults: '',
            filterKey: '',
            filterActive: false,

        }
    },
    methods: {
        toggleFilter: function (){
            this.filterActive = !this.filterActive;
            if(this.filterActive === false){
                this.filterResults = this.results;
            }
        },
        filterList: function (){
            if(this.filterKey == '') {
                this.filterResults = this.results;
            }else{
                let newResults = [];
                let key = this.filterKey.toUpperCase();
                for(let i=0; i<this.results.length; i++){
                    if(this.results[i].institution_name.toUpperCase().indexOf(key) > -1){
                        newResults.push(this.results[i]);
                    }
                }

                this.filterResults = newResults;

            }
        },
        editRow: function (row, index){
            this.editForm.institution_code = row.institution_code;
            this.editForm.institution_name = row.institution_name;
            this.editForm.institution_location_code = row.institution_location_code;
            this.editForm.institution_type_code = row.institution_type_code;
            this.editForm.id = row.id;

            $("#editSchoolModal").modal('show');

        },
        editSchool: function ()
        {
            this.editForm.put('/vss/maintenance/school/' + this.editForm.id, {
                onSuccess: () => {
                    this.showSuccessAlert();
                    this.editForm.reset('institution_code', 'institution_name', 'institution_location_code', 'institution_type_code', 'id');

                    $("#editSchoolModal").modal('hide');

                    Inertia.reload();
                },
                onFailure: () => {
                },
                onError: () => {
                    this.showFailAlert();
                },
                preserveState: true
            });
        },
        newSchool: function ()
        {

            this.newForm.post('/vss/maintenance/school', {
                onSuccess: () => {
                    this.showSuccessAlert();
                    this.newForm.reset('institution_code', 'institution_name', 'institution_location_code', 'institution_type_code');

                    $("#newSchoolModal").modal('hide');

                },
                onFailure: () => {
                },
                onError: () => {
                    this.showFailAlert();
                },
                preserveState: true

            });
        },
        showSuccessAlert: function ()
        {
            this.showSuccessMsg = true;
            let vm = this;
            setTimeout(function (){
                vm.showSuccessMsg = false;
            }, 5000);
        },
        showFailAlert: function ()
        {
            this.showFailMsg = true;
            let vm = this;
            setTimeout(function (){
                vm.showFailMsg = false;
            }, 5000);
        },
    },
    watch: {
        results: {
            handler(newValue, oldValue) {
                this.filterResults = newValue;
            },
            deep: true
        }
    },
    computed: {
    },
    mounted() {
        this.filterResults = this.results;
    }
}

</script>
