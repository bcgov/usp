<template>

<AuthenticatedLayout v-bind="$attrs">
    <div class="card">
        <div class="card-header">
            <div>SFAS Programs<button class="btn btn-success btn-sm float-end" data-bs-toggle="modal"
                    data-bs-target="#newSfasProgramModal">Add New</button></div>
        </div>

        <div class="card-body">
            <div v-if="sfasPrograms.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">SFAS Prog. Code</th>
                            <th scope="col">Area of Study</th>
                            <th scope="col">Degree Level</th>
                            <th scope="col">Nurse Type</th>
                            <th scope="col">NEB Prog. Code</th>
                            <th scope="col">NEB Eligible?</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in sfasPrograms">
                            <td>{{ row.sfas_program_code }}</td>
                            <td>{{ row.area_of_study }}</td>
                            <td>{{ row.degree_level }}</td>
                            <td>{{ row.nurse_type }}</td>
                            <td>{{ row.neb_program_code }}</td>
                            <td>
                                <span v-if="row.eligible" class="badge rounded-pill text-bg-success">YES</span>
                                <span v-else class="badge rounded-pill text-bg-info">NO</span>
                            </td>
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

        <div class="modal modal-lg fade" id="newSfasProgramModal" tabindex="-1" aria-labelledby="newSfasProgramModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newSfasProgramModalLabel">Add New SFAS Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="newSfasProgram">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="newSfasProgramSfasProgramCode" class="form-label"
                                            value="SFAS Prog. Code" />
                                        <Input type="text" class="form-control" id="newSfasProgramSfasProgramCode"
                                            v-model="newSfasProgramForm.sfas_program_code" />
                                        <div v-if="errors.sfas_program_code" class="text-xs text-red-500">
                                            {{ errors.sfas_program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <Label for="newSfasProgramAreaOfStudy" class="form-label" value="Area of Study" />
                                        <Input type="text" class="form-control" id="newSfasProgramAreaOfStudy"
                                            v-model="newSfasProgramForm.area_of_study" />
                                        <div v-if="errors.area_of_study" class="text-xs text-red-500">
                                            {{ errors.area_of_study.join(', ') }}
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <Label for="newSfasProgramNurseType" class="form-label" value="Nurse Type" />
                                        <Select id="newSfasProgramNurseType" class="form-select"
                                            v-model="newSfasProgramForm.nurse_type">
                                            <option value="LPN">LPN</option>
                                            <option value="RN">RN</option>
                                        </Select>
                                        <div v-if="errors.nurse_type" class="text-xs text-red-500">
                                            {{ errors.nurse_type.join(', ') }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <Label for="newSfasProgramDegreeLevel" class="form-label" value="Degree Level" />
                                        <Select id="newSfasProgramDegreeLevel" class="form-select"
                                            v-model="newSfasProgramForm.degree_level">
                                            <option value="Certificate">Certificate</option>
                                            <option value="Co-op Non-Degree">Co-op Non-Degree</option>
                                            <option value="Co-op Undergraduate">Co-op Undergraduate</option>
                                            <option value="Diploma">Diploma</option>
                                            <option value="Doctorate">Doctorate</option>
                                            <option value="Masters">Masters</option>
                                            <option value="Non-Degree">Non-Degree</option>
                                            <option value="Undergraduate">Undergraduate</option>
                                        </Select>
                                        <div v-if="errors.degree_level" class="text-xs text-red-500">
                                            {{ errors.degree_level.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <Label for="newSfasProgramNebProgCode" class="form-label" value="NEB Prog. Code" />
                                        <Select id="newSfasProgramNebProgCode" class="form-select"
                                            v-model="newSfasProgramForm.neb_program_code">
                                            <option v-for="pr in nebPrograms" :value="pr.program_code">({{ pr.program_code
                                            }}) {{ pr.program_description }}</option>
                                        </Select>
                                        <div v-if="errors.neb_program_code" class="text-xs text-red-500">
                                            {{ errors.neb_program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="newSfasProgramEligible"
                                                v-model="newSfasProgramForm.eligible">
                                            <label class="form-check-label" for="newSfasProgramEligible">
                                                NEB Eligible?
                                            </label>
                                        </div>

                                        <div v-if="errors.eligible" class="text-xs text-red-500">
                                            {{ errors.eligible.join(', ') }}
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="newSfasProgramForm.processing">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div v-if="editSfasProgramForm != null" class="modal modal-lg fade" id="editSfasProgramModal" tabindex="-1" aria-labelledby="editSfasProgramModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSfasProgramModalLabel">Edit SFAS Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="editSfasProgram">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-2">
                                        <Label for="editSfasProgramSfasProgramCode" class="form-label"
                                            value="SFAS Prog. Code" />
                                        <Input type="text" class="form-control" id="editSfasProgramSfasProgramCode"
                                            v-model="editSfasProgramForm.sfas_program_code" />
                                        <div v-if="errors.sfas_program_code" class="text-xs text-red-500">
                                            {{ errors.sfas_program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <Label for="editSfasProgramAreaOfStudy" class="form-label" value="Area of Study" />
                                        <Input type="text" class="form-control" id="editSfasProgramAreaOfStudy"
                                            v-model="editSfasProgramForm.area_of_study" />
                                        <div v-if="errors.area_of_study" class="text-xs text-red-500">
                                            {{ errors.area_of_study.join(', ') }}
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <Label for="editSfasProgramNurseType" class="form-label" value="Nurse Type" />
                                        <Select id="editSfasProgramNurseType" class="form-select"
                                            v-model="editSfasProgramForm.nurse_type">
                                            <option value="LPN">LPN</option>
                                            <option value="RN">RN</option>
                                        </Select>
                                        <div v-if="errors.nurse_type" class="text-xs text-red-500">
                                            {{ errors.nurse_type.join(', ') }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <Label for="editSfasProgramDegreeLevel" class="form-label" value="Degree Level" />
                                        <Select id="editSfasProgramDegreeLevel" class="form-select"
                                            v-model="editSfasProgramForm.degree_level">
                                            <option value="Certificate">Certificate</option>
                                            <option value="Co-op Non-Degree">Co-op Non-Degree</option>
                                            <option value="Co-op Undergraduate">Co-op Undergraduate</option>
                                            <option value="Diploma">Diploma</option>
                                            <option value="Doctorate">Doctorate</option>
                                            <option value="Masters">Masters</option>
                                            <option value="Non-Degree">Non-Degree</option>
                                            <option value="Undergraduate">Undergraduate</option>
                                        </Select>
                                        <div v-if="errors.degree_level" class="text-xs text-red-500">
                                            {{ errors.degree_level.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <Label for="editSfasProgramNebProgCode" class="form-label" value="NEB Prog. Code" />
                                        <Select id="editSfasProgramNebProgCode" class="form-select"
                                            v-model="editSfasProgramForm.neb_program_code">
                                            <option v-for="pr in nebPrograms" :value="pr.program_code">({{ pr.program_code
                                            }}) {{ pr.program_description }}</option>
                                        </Select>
                                        <div v-if="errors.neb_program_code" class="text-xs text-red-500">
                                            {{ errors.neb_program_code.join(', ') }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editSfasProgramEligible"
                                                v-model="editSfasProgramForm.eligible">
                                            <label class="form-check-label" for="editSfasProgramEligible">
                                                NEB Eligible?
                                            </label>
                                        </div>

                                        <div v-if="errors.eligible" class="text-xs text-red-500">
                                            {{ errors.eligible.join(', ') }}
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn mr-2 btn-outline-success"
                                :disabled="editSfasProgramForm.processing">Update</button>
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
    name: 'SfasPrograms',
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
            sfasPrograms: [],
            formatter: new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }),
            newSfasProgramForm: useForm({
                formState: true,
                sfas_program_code: '',
                neb_program_code: '',
                area_of_study: '',
                nurse_type: '',
                degree_level: '',
                eligible: '',
            }),
            editSfasProgramForm: null,
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
        fetchSfasPrograms: function () {
            let vm = this;
            // Make a request for a user with a given ID
            axios.get('/neb/sfas-programs/fetch')
                .then(function (response) {
                    // handle success
                    vm.sfasPrograms = response.data.sfas_programs;
                    vm.nebPrograms = response.data.neb_programs;
                    // console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
        },
        newSfasProgram: function () {
            let vm = this;
            this.newSfasProgramForm.formState = '';
            this.newSfasProgramForm.post('/neb/sfas-programs/store', {
                onSuccess: () => {
                    $("#newSfasProgramModal").modal('hide')
                        .on('hidden.bs.modal', function () {
                            vm.newSfasProgramForm.reset();

                            vm.newSfasProgramForm.formState = true;
                            vm.fetchSfasPrograms();
                        });
                },
                onFailure: () => {
                },
                onError: () => {
                    this.newSfasProgramForm.formState = false;
                },
                preserveState: true
            });
        },
        editProgram: function (sfas_program) {
            let vm = this;
            this.editSfasProgramForm = useForm(sfas_program);
            this.editSfasProgramForm.formState = '';
            $("#editSfasProgramModal").modal('show')
                .on('hidden.bs.modal', function () {
                    vm.editSfasProgramForm.reset();

                    vm.editSfasProgramForm.formState = true;
                    vm.fetchSfasPrograms();
                });
        },
        editSfasProgram: function () {
            let vm = this;
            this.editSfasProgramForm.put('/neb/sfas-programs/update', {
                onSuccess: () => {
                    $("#editSfasProgramModal").modal('hide');
                },
                onFailure: () => {
                },
                onError: () => {
                    this.editSfasProgramForm.formState = false;
                },
                // preserveState: true
            });
        }
    },
    mounted() {
        this.fetchSfasPrograms();
    }
}

</script>
