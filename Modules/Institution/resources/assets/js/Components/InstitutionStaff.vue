<template>
    <div class="card mb-3">
        <div class="card-header">
            Institution Staff - BCeID Accounts
            <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#newStaffModal">New Staff</button>
        </div>
        <div class="card-body">
            <div v-if="results.staff != null && results.staff.length > 0" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                    <InstitutionStaffHeader></InstitutionStaffHeader>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in editFrom.staff">
                        <td>{{ row.bceid_user_name}}</td>
                        <td>{{ row.bceid_user_email}}</td>
                        <td>{{ row.bceid_user_id }}</td>
                        <td>{{ row.bceid_user_guid }}</td>
                        <td>
<!--                            <span v-if="row.is_admin" class="badge rounded-pill text-bg-success">Admin</span>-->
<!--                            <span v-else class="badge rounded-pill text-bg-info">User</span>-->
                            <div class="btn-group btn-group-sm" role="group" aria-label="Toggle staff role">
                                <input type="radio" class="btn-check" :name="'btnRadioRole0'+i"
                                       :id="'btnRadioRole0'+i" autocomplete="off" :checked="row.is_admin">
                                <label @click.prevent="switchRole(row,'Admin')" class="btn btn-outline-success" :for="'btnRadioRole0'+i">Admin</label>

                                <input type="radio" class="btn-check" :name="'btnRadioRole1'+i"
                                       :id="'btnRadioRole1'+i" autocomplete="off" :checked="isUser(row.user.roles)">
                                <label @click.prevent="switchRole(row,'User')" class="btn btn-outline-success" :for="'btnRadioRole1'+i">User</label>

                                <input type="radio" class="btn-check" :name="'btnRadioRole2'+i"
                                       :id="'btnRadioRole2'+i" autocomplete="off" :checked="isGuest(row.user.roles)">
                                <label @click.prevent="switchRole(row,'Guest')" class="btn btn-outline-success" :for="'btnRadioRole2'+i">Guest</label>
                            </div>

                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Toggle staff status">
                                <input type="radio" class="btn-check" :name="'btnRadioStatus1'+i"
                                       :id="'btnRadioStatus1'+i" autocomplete="off" :checked="row.status==='Active'">
                                <label @click.prevent="switchStatus(row,'Active')" class="btn btn-outline-success" :for="'btnRadioStatus1'+i">Active</label>

                                <input type="radio" class="btn-check" :name="'btnRadioStatus2'+i"
                                       :id="'btnRadioStatus2'+i" autocomplete="off" :checked="row.status!=='Active'">
                                <label @click.prevent="switchStatus(row,'Inactive')" class="btn btn-outline-success" :for="'btnRadioStatus2'+i">Inactive</label>
                            </div>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>
        <div v-if="editStaff == ''" class="modal modal-lg fade" id="newStaffModal" tabindex="-1" aria-labelledby="newStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newStaffModalLabel">New Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <InstitutionStaffCreate v-bind="$attrs" :results="results" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {Link, useForm} from '@inertiajs/vue3';
import InstitutionStaffHeader from "./InstitutionStaffHeader";
import InstitutionStaffCreate from "./InstitutionStaffCreate";
import InstitutionStaffEdit from "./InstitutionStaffEdit";
export default {
    name: 'InstitutionStaff',
    components: {
        Link, InstitutionStaffHeader, InstitutionStaffCreate, InstitutionStaffEdit
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
        openEditForm: function (staff){
            this.editStaff = staff;
            $("#editInstStaffModal").modal('show');
        },
        closeEditForm: function (){
            $("#editInstStaffModal").modal('hide');
            this.editStaff = '';
        },
        isUser: function (roles){
            const role = roles.find(role => role.name === "Institution User");
            return !!role;
        },
        isGuest: function (roles){
            const role = roles.find(role => role.name === "Institution Guest");
            return !!role;
        },
        switchStatus: function (staff, status){
            if(confirm('Are you sure you want to switch this staff member\'s Status to: ' + status)){
                this.editStaffForm = '';
                this.editStaffForm = useForm(staff);
                this.editStaffForm.status = status;
                this.submitForm();
            }
        },
        switchRole: function (staff, role){
            if(confirm('Are you sure you want to switch this staff member\'s Role to: ' + role)){
                let newObj = staff;
                newObj.role = role;
                this.roleForm = useForm(newObj);
                this.roleForm.put('/ministry/institution_roles', {
                        onSuccess: () => {
                            this.roleForm.reset();
                            this.$inertia.visit('/ministry/institutions/' + this.results.id + '/staff');
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
            this.editStaffForm.put('/ministry/institution_staff', {
                onSuccess: () => {
                    this.editStaffForm.reset();
                    this.$inertia.visit('/ministry/institutions/' + this.results.id + '/staff');
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
