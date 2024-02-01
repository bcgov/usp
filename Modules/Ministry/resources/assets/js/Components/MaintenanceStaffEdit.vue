<template>
    <div class="card mb-3">
        <div class="card-header">
            <div>Staff Maintenance</div>
        </div>

        <form v-if="results != null" @submit.prevent="submitStaffForm">
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
                    <BreezeLabel for="start_date" value="Start Date" />
                    <BreezeInput id="start_date" type="date" class="mt-1 block w-full" min="1990-01-01" max="2040-12-31" placeholder="yyyy-mm-dd" aria-placeholder="yyyy-mm-dd" v-model="form.start_date" required />
                </div>

                <div class="mt-4">
                    <BreezeLabel for="access_type" value="Access Type" />
                    <BreezeSelect id="access_type" class="mt-1 block w-full" v-model="form.access_type" required>
                        <option value="A">Admin</option>
                        <option value="U">User</option>
                    </BreezeSelect>
                </div>

                <div class="mt-4">
                    <BreezeLabel for="disabled" value="Status" />
                    <BreezeSelect id="disabled" class="mt-1 block w-full" v-model="form.disabled" required>
                        <option value="false">Active</option>
                        <option value="true">Disabled</option>
                    </BreezeSelect>
                </div>

                <div class="mt-4">
                    <BreezeLabel for="disabled_date" value="Disabled Date" />
                    <BreezeInput id="disabled_date" type="date" class="mt-1 block w-full" placeholder="yyyy-mm-dd" aria-placeholder="yyyy-mm-dd" v-model="form.end_date" />
                </div>

            </div>
            <div class="card-footer">
                <BreezeValidationErrors class="mt-4" />

                <div class="mt-4">
                    <button type="submit" class="btn btn-outline-success" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </button>
                    <Link @click="back" href="#" class="btn btn-outline-primary float-end" :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                        Cancel
                    </Link>
                </div>

            </div>
        </form>
        <h1 v-else class="lead">No results</h1>


        <div v-if="showSuccessMsg" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateSuccessAlert" class="alert alert-success alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">
                        Staff record was updated successfully.
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div v-if="showFailMsg" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateFailAlert" class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="100">
                <div class="">
                    <div class="toast-body">
                        There was an error updating this form.
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeSelect from '@/Components/Select.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    name: 'MaintenanceStaff',
    components: {
        BreezeInput, BreezeLabel, BreezeButton, BreezeSelect, BreezeValidationErrors, Link, useForm
    },
    props: {
        results: Object,
    },
    data() {
        return {
            showSuccessMsg: false,
            showFailMsg: false,
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
            this.form.post('/vss/maintenance/staff/' + this.results.id, {
                onSuccess: () => {
                    this.showSuccessAlert();
                },
                onError: () => {
                    this.showFailAlert();
                }
            });
            // form.wasSuccessful();
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
    mounted() {
        let tmpObj = this.results;
        tmpObj.password = '';
        tmpObj.password_confirmation = '';

        this.form = useForm(tmpObj);
    }
}
</script>
