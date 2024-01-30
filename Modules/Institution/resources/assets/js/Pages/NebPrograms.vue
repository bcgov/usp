<template>

<AuthenticatedLayout v-bind="$attrs">

    <div class="card">
        <div class="card-header">
            <div>NEB Programs<button class="btn btn-success btn-sm float-end" data-bs-toggle="modal"
                    data-bs-target="#newNebProgramModal">Add New</button></div>
        </div>

        <div class="card-body">
            <div v-if="nebPrograms.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">NEB Prog. Code</th>
                            <th scope="col">Description</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in nebPrograms">
                            <td>{{ row.program_code }}</td>
                            <td>{{ row.program_description }}</td>
                            <td>
                                <!-- <button class="btn btn-sm me-1 btn-secondary" @click="editProgram(row)">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

        <div class="modal modal-lg fade" id="newNebProgramModal" tabindex="-1" aria-labelledby="newNebProgramModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newNebProgramModalLabel">Add New NEB Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="newNebProgram">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="newNebProgramProgramCode" class="form-label"
                                            value="NEB Prog. Code" />
                                        <Input type="text" class="form-control" id="newNebProgramProgramCode"
                                            v-model="newNebProgramForm.program_code" />
                                        <div v-if="errors.program_code" class="text-xs text-red-500">
                                            {{ errors.program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <Label for="newNebProgramDescription" class="form-label" value="Description" />
                                        <Input type="text" class="form-control" id="newNebProgramDescription"
                                            v-model="newNebProgramForm.program_description" />
                                        <div v-if="errors.program_description" class="text-xs text-red-500">
                                            {{ errors.program_description.join(', ') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">

                                    <div class="col-auto">
                                        <div v-if="errors.eligible" class="text-xs text-red-500">
                                            {{ errors.eligible.join(', ') }}
                                        </div>
                                    </div>



                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="newNebProgramForm.processing">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div v-if="editNebProgramForm != null" class="modal modal-lg fade" id="editNebProgramModal" tabindex="-1" aria-labelledby="editNebProgramModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNebProgramModalLabel">Edit NEB Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="editNebProgram">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="editNebProgramProgramCode" class="form-label"
                                            value="NEB Prog. Code" />
                                        <Input type="text" class="form-control" id="editNebProgramProgramCode"
                                            v-model="editNebProgramForm.program_code" />
                                        <div v-if="errors.program_code" class="text-xs text-red-500">
                                            {{ errors.program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <Label for="editNebProgramDescription" class="form-label" value="Description" />
                                        <Input type="text" class="form-control" id="editNebProgramDescription"
                                            v-model="editNebProgramForm.program_description" />
                                        <div v-if="errors.program_description" class="text-xs text-red-500">
                                            {{ errors.program_description.join(', ') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">

                                    <div class="col-auto">
                                        <div v-if="errors.eligible" class="text-xs text-red-500">
                                            {{ errors.eligible.join(', ') }}
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="editNebProgramForm.processing">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
    </AuthenticatedLayout>
</template>
<script>
import { Link, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/Authenticated.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Select from "@/Components/Select";
import FormSubmitAlert from "@/Components/FormSubmitAlert";

export default {
    name: 'NebPrograms',
    components: {
        Link, Input, Select, Label, FormSubmitAlert, AuthenticatedLayout, Head
    },
    props: {
        results: Object,
        errors: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            nebPrograms: [],
            formatter: new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }),
            newNebProgramForm: useForm({
                formState: true,
                program_code: '',
                program_description: '',
            }),
            editNebProgramForm: null,
        }
    },
    computed: {
    },
    methods: {
        formatCurrency: function (value) {
            if (!value)
                return 0;

            return this.formatter.format(value.toString());
        },
        fetchNebPrograms: function () {
            let vm = this;
            // Make a request for a user with a given ID
            axios.get('/neb/programs/fetch')
                .then(function (response) {
                    // handle success
                    vm.nebPrograms = response.data.neb_programs;
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
        },
        newNebProgram: function () {
            let vm = this;
            this.newNebProgramForm.formState = '';
            this.newNebProgramForm.post('/neb/programs/store', {
                onSuccess: () => {
                    $("#newNebProgramModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.newNebProgramForm.reset();

                            vm.newNebProgramForm.formState = true;
                            vm.fetchNebPrograms();
                        });
                },
                onFailure: () => {
                },
                onError: () => {
                    this.newNebProgramForm.formState = false;
                },
                preserveState: true
            });
        },
        editProgram: function (neb_program) {
            let vm = this;
            this.editNebProgramForm = useForm(neb_program);
            this.editNebProgramForm.formState = '';
            $("#editNebProgramModal").modal('show')
                .on('hidden.bs.modal', function () {
                    vm.editNebProgramForm.reset();

                    vm.editNebProgramForm.formState = true;
                    vm.fetchNebPrograms();
                });
        },
        editNebProgram: function () {
            let vm = this;
            this.editNebProgramForm.put('/neb/programs/update', {
                onSuccess: () => {
                    $("#editNebProgramModal").modal('hide');
                },
                onFailure: () => {
                },
                onError: () => {
                    this.editNebProgramForm.formState = false;
                },
                // preserveState: true
            });
        }
    },
    mounted() {
        this.fetchNebPrograms();
    }
}

</script>
