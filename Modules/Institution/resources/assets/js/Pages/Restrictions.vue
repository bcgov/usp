<template>

<AuthenticatedLayout v-bind="$attrs">

    <div class="card">
        <div class="card-header">
            <div>Restrictions<button class="btn btn-success btn-sm float-end" data-bs-toggle="modal"
                    data-bs-target="#newRestrictionModal">Add New</button></div>
        </div>

        <div class="card-body">
            <div v-if="restrictions.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Restriction Code</th>
                            <th scope="col">Description</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in restrictions">
                            <td>{{ row.restriction_code }}</td>
                            <td>{{ row.restriction_description }}</td>
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

        <div class="modal modal-lg fade" id="newRestrictionModal" tabindex="-1" aria-labelledby="newRestrictionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newRestrictionModalLabel">Add New Restriction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="newRestriction">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="newRestrictionProgramCode" class="form-label"
                                            value="Restriction Code" />
                                        <Input type="text" class="form-control" id="newRestrictionProgramCode"
                                            v-model="newRestrictionForm.restriction_code" />
                                        <div v-if="errors.restriction_code" class="text-xs text-red-500">
                                            {{ errors.restriction_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <Label for="newRestrictionDescription" class="form-label" value="Description" />
                                        <Input type="text" class="form-control" id="newRestrictionDescription"
                                            v-model="newRestrictionForm.restriction_description" />
                                        <div v-if="errors.restriction_description" class="text-xs text-red-500">
                                            {{ errors.restriction_description.join(', ') }}
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
                                :disabled="newRestrictionForm.processing">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div v-if="editRestrictionForm != null" class="modal modal-lg fade" id="editRestrictionModal" tabindex="-1" aria-labelledby="editRestrictionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRestrictionModalLabel">Edit Restriction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="editRestriction">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="editRestrictionProgramCode" class="form-label"
                                            value="Restriction Code" />
                                        <Input type="text" class="form-control" id="editRestrictionProgramCode"
                                            v-model="editRestrictionForm.restriction_code" />
                                        <div v-if="errors.restriction_code" class="text-xs text-red-500">
                                            {{ errors.restriction_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <Label for="editRestrictionDescription" class="form-label" value="Description" />
                                        <Input type="text" class="form-control" id="editRestrictionDescription"
                                            v-model="editRestrictionForm.restriction_description" />
                                        <div v-if="errors.restriction_description" class="text-xs text-red-500">
                                            {{ errors.restriction_description.join(', ') }}
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
                                :disabled="editRestrictionForm.processing">Update</button>
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
    name: 'Restrictions',
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
            restrictions: [],
            formatter: new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }),
            newRestrictionForm: useForm({
                formState: true,
                restriction_code: '',
                restriction_description: '',
            }),
            editRestrictionForm: null,
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
        fetchRestrictions: function () {
            let vm = this;
            // Make a request for a user with a given ID
            axios.get('/neb/restrictions/fetch')
                .then(function (response) {
                    // handle success
                    vm.restrictions = response.data.restrictions;
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
        },
        newRestriction: function () {
            let vm = this;
            this.newRestrictionForm.formState = '';
            this.newRestrictionForm.post('/neb/restrictions/store', {
                onSuccess: () => {
                    $("#newRestrictionModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.newRestrictionForm.reset();

                            vm.newRestrictionForm.formState = true;
                            vm.fetchRestrictions();
                        });
                },
                onFailure: () => {
                },
                onError: () => {
                    this.newRestrictionForm.formState = false;
                },
                preserveState: true
            });
        },
        editProgram: function (neb_program) {
            let vm = this;
            this.editRestrictionForm = useForm(neb_program);
            this.editRestrictionForm.formState = '';
            $("#editRestrictionModal").modal('show')
                .on('hidden.bs.modal', function () {
                    vm.editRestrictionForm.reset();

                    vm.editRestrictionForm.formState = true;
                    vm.fetchRestrictions();
                });
        },
        editRestriction: function () {
            let vm = this;
            this.editRestrictionForm.put('/neb/restrictions/update', {
                onSuccess: () => {
                    $("#editRestrictionModal").modal('hide');
                },
                onFailure: () => {
                },
                onError: () => {
                    this.editRestrictionForm.formState = false;
                },
                // preserveState: true
            });
        }
    },
    mounted() {
        this.fetchRestrictions();
    }
}

</script>
