<template>
    <div v-if="editForm != null" class="card mb-3">
        <div class="card-header">
            <div>Institution Details</div>
        </div>

        <form class="card-body" @submit.prevent="submitForm">
            <div class="row g-3">

                <div class="col-md-6">
                    <Label for="inputName" class="form-label" value="Name" />
                    <Input type="text" class="form-control" id="inputName" v-model="editForm.name" />
                </div>
                <div class="col-md-6">
                    <Label for="inputAddress1" class="form-label" value="Address 1" />
                    <Input type="text" class="form-control" id="inputAddress1" v-model="editForm.address1" />
                </div>

                <div class="col-md-4">
                    <Label for="inputDli" class="form-label" value="DLI #" />
                    <Input type="text" class="form-control" id="inputDli" v-model="editForm.dli" />
                </div>
                <div class="col-md-4">
                    <Label for="inputPrimaryContact" class="form-label" value="Primary Contact" />
                    <Input type="text" class="form-control" id="inputPrimaryContact" v-model="editForm.primary_contact" />
                </div>
                <div class="col-md-4">
                    <Label for="inputPrimaryEmail" class="form-label" value="Primary Email" />
                    <Input type="email" class="form-control" id="inputPrimaryEmail" v-model="editForm.primary_email" />
                </div>

                <div class="col-md-4">
                    <Label for="inputCity" class="form-label" value="City" />
                    <Input type="text" class="form-control" id="inputCity" v-model="editForm.city" />
                </div>
                <div class="col-md-4">
                    <Label for="inputPostalCode" class="form-label" value="Postal Code" />
                    <Input type="text" class="form-control" id="inputPostalCode" v-model="editForm.postal_code" />
                </div>
                <div class="col-md-4">
                    <Label for="inputProvince" class="form-label" value="Province" />
                    <Select class="form-select" id="inputProvince" v-model="editForm.province">
                        <option value="AB">AB</option>
                        <option value="BC">BC</option>
                        <option value="MB">MB</option>
                        <option value="NB">NB</option>
                        <option value="NL">NL</option>
                        <option value="NS">NS</option>
                        <option value="NT">NT</option>
                        <option value="NU">NU</option>
                        <option value="ON">ON</option>
                        <option value="PE">PE</option>
                        <option value="QC">QC</option>
                        <option value="SK">SK</option>
                        <option value="YT">YT</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputPublic" class="form-label" value="Public?" />
                    <Select class="form-select" id="inputPublic" v-model="editForm.public">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputActiveStatus" class="form-label" value="Active?" />
                    <Select class="form-select" id="inputActiveStatus" v-model="editForm.active_status">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>
                <div class="col-md-4">
                    <Label for="inputStandingStatus" class="form-label" value="Status - in good standing?" />
                    <Select class="form-select" id="inputStandingStatus" v-model="editForm.standing_status">
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </Select>
                </div>

                <div v-if="editForm.errors != undefined" class="row">
                    <div class="col-12">
                        <div v-if="editForm.hasErrors == true" class="alert alert-danger mt-3">
                            <ul>
                                <li v-for="err in editForm.errors">{{ err }}</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
            <div class="card-footer mt-3">
                <button type="button" class="btn me-2 btn-secondary" @click="back">Back</button>
                <button type="submit" class="btn me-2 btn-outline-success float-end" :disabled="editForm.processing">Update Institution</button>
            </div>
            <FormSubmitAlert :form-state="editForm.formState"
                             :success-msg="'Institution record was updated successfully.'"></FormSubmitAlert>
        </form>
    </div>

</template>
<script>
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    name: 'InstitutionDetails',
    components: {
        Input, Label, Select, Link, useForm, FormSubmitAlert
    },
    props: {
        results: Object,
    },
    data() {
        return {
            editForm: '',
        }
    },
    methods: {
        back: function()
        {
            window.history.back();
        },
        submitForm: function ()
        {
            this.editForm.formState = null;
            this.editForm.put('/ministry/institutions/update', {
                onSuccess: () => {
                    this.editForm.formState = true;
                },
                onError: () => {
                    this.editForm.formState = false;
                },
                preserveState: true
            });
        },
    },

    mounted() {
        this.editForm = useForm(this.results);
    }
}
</script>
