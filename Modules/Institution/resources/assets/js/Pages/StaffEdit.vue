<template>

    <AuthenticatedLayout v-bind="$attrs">
        <div class="card mb-3">
            <div class="card-header">
                <div>Staff Maintenance</div>
            </div>

            <form v-if="results != null" @submit.prevent="submitStaffForm">
                <div class="card-body">


                    <div class="card-body">



                        <div>
                            <Label for="user_id" value="User ID" />
                            <Input id="user_id" type="text" class="mt-1 block w-full bg-indigo-50" v-model="form.user_id"
                                disabled="disabled" />
                        </div>

                        <div class="mt-4">
                            <Label for="first_name" value="First Name" />
                            <Input id="first_name" type="text" class="mt-1 block w-full bg-indigo-50"
                                v-model="form.first_name" disabled="disabled" />
                        </div>

                        <div class="mt-4">
                            <Label for="last_name" value="Last Name" />
                            <Input id="last_name" type="text" class="mt-1 block w-full bg-indigo-50"
                                v-model="form.last_name" disabled="disabled" />
                        </div>

                        <div class="mt-4">
                            <Label for="email" value="Email" />
                            <Input id="email" type="email" class="mt-1 block w-full bg-indigo-50" v-model="form.email"
                                disabled="disabled" />
                        </div>

                        <div class="mt-4">
                            <Label for="tele" value="Telephone #" />
                            <Input id="tele" type="text" class="mt-1 block w-full" v-model="form.tele" required />
                        </div>

                        <div class="mt-4">
                            <Label for="access_type" value="Access Type" />
                            <Select id="access_type" class="mt-1 block w-full" v-model="form.access_type" required>
                                <option value="A">Admin</option>
                                <option value="U">User</option>
                            </Select>
                        </div>

                        <div class="mt-4">
                            <Label for="disabled" value="Status" />
                            <Select id="disabled" class="mt-1 block w-full" v-model="form.disabled" required>
                                <option value="false">Active</option>
                                <option value="true">Disabled</option>
                            </Select>
                        </div>

                    </div>

                </div>
                <div class="card-footer">
                    <ValidationErrors class="p-3" />

                    <div class="mt-4">
                        <button type="submit" class="btn btn-outline-success" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Save
                        </button>
                        <Link @click="back" href="#" class="btn btn-outline-primary float-end"
                            :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                        Cancel
                        </Link>
                    </div>

                </div>
                <FormSubmitAlert :form-state="form.formState" :success-msg="'Staff record was updated successfully.'"
                    :fail-msg="'There was an error updating this form.'"></FormSubmitAlert>

            </form>
            <h1 v-else class="lead">No results</h1>

        </div>
    </AuthenticatedLayout>
</template>
<script>
import Button from '@/Components/Button.vue';
import Select from '@/Components/Select.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import ValidationErrors from '@/Components/ValidationErrors.vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormSubmitAlert from "@/Components/FormSubmitAlert";
import AuthenticatedLayout from '../Layouts/Authenticated.vue';

export default {
    name: 'StaffEdit',
    components: {
        Input, Label, Button, Select, ValidationErrors, Link, useForm, FormSubmitAlert, AuthenticatedLayout
    },
    props: {
        results: Object,
    },
    data() {
        return {
            form: '',
        }
    },
    methods: {
        back: function () {
            window.history.back();
        },
        submitStaffForm: function () {
            this.form.formState = '';
            this.form.post('/neb/staff/' + this.results.id, {
                onSuccess: () => {
                    this.form.formState = true;
                },
                onFailure: () => {
                },
                onError: () => {
                    this.form.formState = false;
                }
            });
            // form.wasSuccessful();
        },

    },
    mounted() {
        let tmpObj = this.results;
        this.form = useForm(tmpObj);
        this.form.access_type = 'U';
    }
}

</script>
