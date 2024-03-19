<template>
    <div class="card">
        <div class="card-header">
            <div>Staff Maintenance</div>
        </div>

        <div class="card-body">
            <div v-if="results != null" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in results">
                            <td>{{ row.first_name }}</td>
                            <td>{{ row.last_name}}</td>
                            <td>{{ row.name}}</td>
                            <td>{{ row.email }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Toggle staff role">
                                    <input type="radio" class="btn-check" :name="'btnRadioRole0'+i"
                                           :id="'btnRadioRole0'+i" autocomplete="off" :checked="isAdmin(row.roles)">
                                    <label @click.prevent="switchRole(row,'Admin')" class="btn btn-outline-success" :for="'btnRadioRole0'+i">Admin</label>

                                    <input type="radio" class="btn-check" :name="'btnRadioRole1'+i"
                                           :id="'btnRadioRole1'+i" autocomplete="off" :checked="isUser(row.roles)">
                                    <label @click.prevent="switchRole(row,'User')" class="btn btn-outline-success" :for="'btnRadioRole1'+i">User</label>

                                    <input type="radio" class="btn-check" :name="'btnRadioRole2'+i"
                                           :id="'btnRadioRole2'+i" autocomplete="off" :checked="isGuest(row.roles)">
                                    <label @click.prevent="switchRole(row,'Guest')" class="btn btn-outline-success" :for="'btnRadioRole2'+i">Guest</label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Toggle staff status">
                                    <input type="radio" class="btn-check" :name="'btnRadioStatus1'+i"
                                           :id="'btnRadioStatus1'+i" autocomplete="off" :checked="!row.disabled">
                                    <label @click.prevent="switchStatus(row,false)" class="btn btn-outline-success" :for="'btnRadioStatus1'+i">Active</label>

                                    <input type="radio" class="btn-check" :name="'btnRadioStatus2'+i"
                                           :id="'btnRadioStatus2'+i" autocomplete="off" :checked="row.disabled">
                                    <label @click.prevent="switchStatus(row,true)" class="btn btn-outline-success" :for="'btnRadioStatus2'+i">Inactive</label>
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
    name: 'MaintenanceStaff',
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
            const role = roles.find(role => role.name === "Ministry Admin");
            return !!role;
        },
        isUser: function (roles){
            const role = roles.find(role => role.name === "Ministry User");
            return !!role;
        },
        isGuest: function (roles){
            const role = roles.find(role => role.name === "Ministry Guest");
            return !!role;
        },
        switchStatus: function (staff, status){
            if(confirm('Are you sure you want to switch this staff member\'s Status to: ' + (status===true ? 'Inactive' : 'Active') )){
                this.editStaffForm = '';
                this.editStaffForm = useForm(staff);
                this.editStaffForm.disabled = status;
                this.submitForm();
            }
        },
        switchRole: function (staff, role){
            if(confirm('Are you sure you want to switch this staff member\'s Role to: ' + role)){
                let newObj = staff;
                newObj.role = role;
                this.roleForm = useForm(newObj);
                this.roleForm.put('/ministry/maintenance/staff/roles/' + staff.id, {
                    onSuccess: () => {
                        this.roleForm.reset();
                        this.$inertia.visit('/ministry/maintenance/staff');
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
            this.editStaffForm.put('/ministry/maintenance/staff/' + this.editStaffForm.id, {
                onSuccess: () => {
                    this.editStaffForm.reset();
                    this.$inertia.visit('/ministry/maintenance/staff');
                },
                onError: () => {
                    this.editStaffForm.formState = false;
                },
                preserveState: true
            });
        }
    }
}

</script>
