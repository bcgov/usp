<template>
    <div class="card mb-3">
        <div class="card-header">
            <div>Staff Maintenance</div>
        </div>

        <form v-if="results != null" @submit.prevent="submitStaffForm">
            <div class="card-body">


                <div class="card-body">



                    <div>
                        <BreezeLabel for="user_id" value="User ID" />
                        <BreezeInput id="user_id" type="text" class="mt-1 block w-full bg-indigo-50" v-model="form.user_id" disabled="disabled" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="first_name" value="First Name" />
                        <BreezeInput id="first_name" type="text" class="mt-1 block w-full bg-indigo-50" v-model="form.first_name" disabled="disabled" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="last_name" value="Last Name" />
                        <BreezeInput id="last_name" type="text" class="mt-1 block w-full bg-indigo-50" v-model="form.last_name" disabled="disabled" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="email" value="Email" />
                        <BreezeInput id="email" type="email" class="mt-1 block w-full bg-indigo-50" v-model="form.email" disabled="disabled" />
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="access_type" value="Access Type" />
                        <BreezeSelect id="access_type" class="mt-1 block w-full" v-model="form.access_type" required>
                            <option></option>
                            <option value="A">Admin</option>
                            <option value="U">User</option>
                            <option value="G">Guest</option>
                        </BreezeSelect>
                    </div>

                    <div class="mt-4">
                        <BreezeLabel for="disabled" value="Status" />
                        <BreezeSelect id="disabled" class="mt-1 block w-full" v-model="form.disabled" required>
                            <option></option>
                            <option value="false">Active</option>
                            <option value="true">Disabled</option>
                        </BreezeSelect>
                    </div>

                </div>

            </div>
            <div class="card-footer">
                <BreezeValidationErrors class="p-3" />

                <div class="mt-4">
                    <button type="submit" class="btn btn-outline-success" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </button>
                    <Link @click="back" href="#" class="btn btn-outline-primary float-end" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                        Cancel
                    </Link>
                </div>

            </div>
            <FormSubmitAlert :form-state="form.formState"
                             :success-msg="'Staff record was updated successfully.'"
                             :fail-msg="'There was an error updating this form.'"></FormSubmitAlert>

        </form>
        <h1 v-else class="lead">No results</h1>

    </div>

</template>
<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeSelect from '@/Components/Select.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormSubmitAlert from "@/Components/FormSubmitAlert";

export default {
    name: 'MaintenanceStaff',
    components: {
        BreezeInput, BreezeLabel, BreezeButton, BreezeSelect, BreezeValidationErrors, Link, useForm, FormSubmitAlert
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
        back: function()
        {
            window.history.back();
        },
        submitStaffForm: function ()
        {
            this.form.formState = '';
            this.form.post('/ministry/maintenance/staff/' + this.results.id, {
                onSuccess: () => {
                    this.form.formState = true;
                },
                onError: () => {
                    this.form.formState = false;
                }
            });
        },

    },
    mounted() {
        let tmpObj = this.results;
        this.form = useForm(tmpObj);
    }
}

</script>
