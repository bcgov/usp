<template xmlns="http://www.w3.org/1999/html">
    <div>
    <div v-for="cat in categories" class="card mb-3">
        <div class="card-header">
            <div>{{ cat }}
                <button v-if="cat.indexOf('Ministry') === -1" @click="newUtil(cat)" type="button" class="btn btn-success btn-sm float-end">New {{ cat }}</button>
            </div>
        </div>
        <div class="card-body">
            <div v-if="results[cat] != null" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in results[cat]">
                            <td>
                                <button @click="editUtil(cat, i)" type="button" class="btn btn-link text-start" data-bs-toggle="modal" data-bs-target="#editUtilModal">{{ row.field_name }}</button>
                            </td>
                            <td>
                                <span v-if="row.active_flag" class="badge rounded-pill text-bg-success">True</span>
                                <span v-else class="badge rounded-pill text-bg-danger">False</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No {{ cat }}</h1>
        </div>
    </div>


    <div class="modal modal-lg fade" id="newUtilModal" tabindex="-1" aria-labelledby="newUtilModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newApplicationModalLabel">New {{ newUtilForm.field_type }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form v-if="newUtilForm != null" @submit.prevent="submitNewUtil">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-9">
                                    <BreezeLabel for="newUtilTitle" class="form-label" value="Title" />
                                    <BreezeInput type="text" class="form-control" id="newUtilTitle" v-model="newUtilForm.field_name" />
                                </div>
                                <div class="col-md-3">
                                    <BreezeLabel for="newUtilActiveFlag" class="form-label" value="Active" />
                                    <BreezeSelect class="form-select" id="newUtilActiveFlag" v-model="newUtilForm.active_flag">
                                        <option value="false">False</option>
                                        <option value="true">True</option>
                                    </BreezeSelect>
                                </div>
                                <div class="col-12">
                                    <BreezeLabel for="newUtilDescription" class="form-label" value="Description" />
                                    <textarea class="form-control" id="newUtilDescription"
                                              placeholder="describe where this field is being used in the application"
                                              aria-placeholder="describe where this field is being used in the application"
                                              v-model="newUtilForm.field_description"></textarea>
                                </div>

                            </div>

                            <div v-if="newUtilForm.errors != undefined" class="row">
                                <div class="col-12">
                                    <div v-if="newUtilForm.hasErrors == true" class="alert alert-danger mt-3">
                                        <ul>
                                            <li v-for="err in newUtilForm.errors"><small>{{ err }}</small></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn me-2 btn-outline-success" :disabled="newUtilForm.processing">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end new util -->
    <FormSubmitAlert :form-state="newUtilForm.formState"></FormSubmitAlert>

    <div class="modal modal-lg fade" id="editUtilModal" tabindex="-1" aria-labelledby="editUtilModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editApplicationModalLabel">Edit {{ editUtilForm.field_type }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form v-if="editUtilForm != null" @submit.prevent="submitEditUtil">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-9">
                                    <BreezeLabel for="editUtilTitle" class="form-label" value="Title" />
                                    <BreezeInput type="text" class="form-control" id="editUtilTitle" v-model="editUtilForm.field_name" />
                                </div>
                                <div class="col-md-3">
                                    <BreezeLabel for="editUtilActiveFlag" class="form-label" value="Active" />
                                    <BreezeSelect class="form-select" id="editUtilActiveFlag" v-model="editUtilForm.active_flag">
                                        <option value="false">False</option>
                                        <option value="true">True</option>
                                    </BreezeSelect>
                                </div>
                                <div class="col-12">
                                    <BreezeLabel for="editUtilDescription" class="form-label" value="Description" />
                                    <textarea class="form-control" id="editUtilDescription"
                                              placeholder="describe where this field is being used in the application"
                                              aria-placeholder="describe where this field is being used in the application"
                                              v-model="editUtilForm.field_description"></textarea>
                                </div>

                            </div>

                            <div v-if="editUtilForm.errors != undefined" class="row">
                                <div class="col-12">
                                    <div v-if="editUtilForm.hasErrors == true" class="alert alert-danger mt-3">
                                        <ul>
                                            <li v-for="err in editUtilForm.errors"><small>{{ err }}</small></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn me-2 btn-outline-success" :disabled="editUtilForm.processing">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end edit util -->
    <FormSubmitAlert :form-state="editUtilForm.formState"></FormSubmitAlert>

</div>
</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';
import FormSubmitAlert from "@/Components/FormSubmitAlert";
import BreezeSelect from "@/Components/Select";
import BreezeLabel from "@/Components/Label";

export default {
    name: 'MaintenanceUtils',
    components: {
        BreezeInput, Link, FormSubmitAlert, BreezeSelect, BreezeLabel
    },
    props: {
        results: Object,
        categories: Object
    },
    data() {
        return {
            newUtilForm: '',
            newUtilFormData: {
                formState: '',
                field_name: '', field_type: '', field_description: '', active_flag: false,
            },
            editUtilForm: '',
        }
    },
    methods: {
        editUtil: function (type, index) {
            this.editUtilForm = useForm(this.results[type][index]);
            this.editUtilForm.formState = '';
        },

        submitEditUtil: function () {
            this.editUtilForm.formState = '';
            this.editUtilForm.put('/ministry/maintenance/utils/' + this.editUtilForm.id, {
                onSuccess: (response) => {
                    $("#editUtilModal").modal('hide');
                    this.editUtilForm.formState = true;
                },
                onError: () => {
                    this.editUtilForm.formState = false;
                },
                preserveState: true,
                preserveScroll: true,
            });
        },

        newUtil: function (type) {
            $("#newUtilModal").modal('show');
            this.newUtilForm = useForm(this.newUtilFormData);
            this.newUtilForm.field_type = type;
        },
        submitNewUtil: function ()
        {
            this.newUtilForm.formState = '';
            this.newUtilForm.post('/ministry/maintenance/utils', {
                onSuccess: (response) => {
                    $("#newUtilModal").modal('hide');
                    this.newUtilForm.reset(this.newUtilFormData);
                    this.newUtilForm.formState = true;

                },
                onError: () => {
                    this.newUtilForm.formState = false;
                },
                preserveState: true,
                preserveScroll: true,
            });
        },
    },
    mounted() {

    }
}

</script>
