<template>
    <div class="card">
        <div class="card-header">
            <div>Staff List</div>
        </div>

        <div class="card-body">
            <div v-if="results != null" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">User ID</th>
                        <th scope="col">GUID</th>
                        <th scope="col">Role</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in editFrom">
                        <td>{{ row.bceid_user_name}}</td>
                        <td>{{ row.bceid_user_email}}</td>
                        <td>{{ row.bceid_user_id }}</td>
                        <td>{{ row.bceid_user_guid }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Toggle staff role">
                                <input type="radio" class="btn-check" :name="'btnRadioRole0'+i"
                                       :id="'btnRadioRole0'+i" autocomplete="off" :checked="isAdmin(row.user.roles)">
                                <label class="btn btn-outline-success disabled" :for="'btnRadioRole0'+i">Admin</label>

                                <input type="radio" class="btn-check" :name="'btnRadioRole1'+i"
                                       :id="'btnRadioRole1'+i" autocomplete="off" :checked="isUser(row.user.roles)">
                                <label @click.prevent="switchRole(row,'User')" class="btn btn-outline-success" :for="'btnRadioRole1'+i">User</label>

                                <input type="radio" class="btn-check" :name="'btnRadioRole2'+i"
                                       :id="'btnRadioRole2'+i" autocomplete="off" :checked="isGuest(row.user.roles)">
                                <label @click.prevent="switchRole(row,'Guest')" class="btn btn-outline-success" :for="'btnRadioRole2'+i">Guest</label>
                            </div>

                        </td>


                    </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

    </div>

</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';

export default {
    name: 'StaffList',
    components: {
        BreezeInput, Link
    },
    props: {
        results: Object,
    },
    data() {
        return {
            editFrom: '',
            editStaffForm: '',
            editStaff: '',
            roleForm: ''
        }
    },
    methods: {
        isAdmin: function (roles){
            const role = roles.find(role => role.name === "Institution Admin");
            return !!role;
        },
        isUser: function (roles){
            const role = roles.find(role => role.name === "Institution User");
            return !!role;
        },
        isGuest: function (roles){
            const role = roles.find(role => role.name === "Institution Guest");
            return !!role;
        },
        switchRole: function (staff, role){
            if(confirm('Are you sure you want to switch this staff member\'s Role to: ' + role)){
                let newObj = staff;
                newObj.role = role;
                this.roleForm = useForm(newObj);
                this.roleForm.put('/institution/roles', {
                    onSuccess: () => {
                        this.roleForm.reset();
                        this.$inertia.visit('/institution/staff');
                    },
                    onError: () => {
                        this.roleForm.formState = false;
                    },
                    preserveState: true
                });
            }
        },
        submitForm: function () {
            this.editStaffForm.formState = null;
            this.editStaffForm.put('/institution/staff', {
                onSuccess: () => {
                    this.editStaffForm.reset();
                    this.$inertia.visit('/institution/staff');
                },
                onError: () => {
                    this.editStaffForm.formState = false;
                },
                preserveState: true
            });
        }
    },
    mounted() {
        this.editFrom = this.results;
    }

}

</script>
