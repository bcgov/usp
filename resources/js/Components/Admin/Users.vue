<template>
    <div class="card">
        <div class="card-header">
            <div>Users</div>
        </div>

        <div class="card-body">
            <div v-if="users != null" class="table-responsive pb-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Type of Access</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in users">
                            <th scope="row">
                                <button type="button" class="btn btn-link" @click="edit(i)">{{ row.user_id }}</button>
                            </th>
                            <td>{{ row.first_name }}</td>
                            <td>{{ row.last_name}}</td>
                            <td>{{ row.email }}</td>
                            <td>
                                <template v-for="role in row.roles">
                                    <div>{{ role.name }}</div>
                                </template>
                            </td>
                            <td>
                                <span v-if="row.disabled" class="badge rounded-pill text-bg-danger">Disabled</span>
                                <span v-else class="badge rounded-pill text-bg-success">Active</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h1 v-else class="lead">No results</h1>
        </div>

    </div>

    <div v-if="editUser != ''" class="modal modal-lg fade" id="editUserModal" tabindex="-1"
         aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="submitUser">
                    <div class="modal-body">
                        <div class="card-body row">

                            <div class="col-md-6">
                                <BreezeLabel for="user_id" value="User ID" />
                                <BreezeInput id="user_id" type="text" class="mt-1 block w-full bg-indigo-50" v-model="editUser.user_id" disabled="disabled" />
                            </div>

                            <div class="col-md-6">
                                <BreezeLabel for="first_name" value="First Name" />
                                <BreezeInput id="first_name" type="text" class="mt-1 block w-full" v-model="editUser.first_name" />
                            </div>

                            <div class="mt-4 col-md-6">
                                <BreezeLabel for="last_name" value="Last Name" />
                                <BreezeInput id="last_name" type="text" class="mt-1 block w-full" v-model="editUser.last_name" />
                            </div>

                            <div class="mt-4 col-md-6">
                                <BreezeLabel for="email" value="Email" />
                                <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="editUser.email" />
                            </div>

                            <div class="mt-4 col-md-6">
                                <BreezeLabel for="tele" value="Telephone #" />
                                <BreezeInput id="tele" type="number" class="mt-1 block w-full" v-model="editUser.tele" required />
                            </div>

                            <div class="mt-4 col-md-6">
                                <BreezeLabel for="disabled" value="Status" />
                                <BreezeSelect id="disabled" class="mt-1 block w-full" v-model="editUser.disabled" required>
                                    <option value="false">Active</option>
                                    <option value="true">Disabled</option>
                                </BreezeSelect>
                            </div>

                            <div class="mt-4 col-md-4">
                                <BreezeLabel for="access_type" value="Access Type" />
                                    <ul class="list-group">
                                        <template v-for="(role, i) in roles">
                                            <li v-if="role.name.includes('Admin')" class="list-group-item">
                                                <BreezeInput type="checkbox" :value="i" @click="roleUpdate($event, role.id)" :checked="hasRole(i)"/> {{role.name}}
                                            </li>
                                        </template>
                                    </ul>
                            </div>
                            <div class="mt-4 col-md-4">
                                <BreezeLabel for="access_type" value="Access Type" />
                                    <ul class="list-group">
                                        <template v-for="(role, i) in roles">
                                            <li v-if="role.name.includes('User')" class="list-group-item">
                                                <BreezeInput type="checkbox" :value="i" @click="roleUpdate($event, role.id)" :checked="hasRole(i)"/> {{role.name}}
                                            </li>
                                        </template>
                                    </ul>
                            </div>
                            <div class="mt-4 col-md-4">
                                <BreezeLabel for="access_type" value="Access Type" />
                                    <ul class="list-group">
                                        <template v-for="(role, i) in roles">
                                            <li v-if="role.name.includes('Guest')" class="list-group-item">
                                                <BreezeInput type="checkbox" :value="i" @click="roleUpdate($event, role.id)" :checked="hasRole(i)"/> {{role.name}}
                                            </li>
                                        </template>
                                    </ul>
                            </div>

                            <div v-if="editUser.errors != undefined" class="col-12">
                                <div v-if="editUser.hasErrors == true" class="alert alert-danger mt-3">
                                    <ul>
                                        <li v-for="err in editUser.errors"><small>{{ err }}</small></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn me-2 btn-outline-success" :disabled="editUser.processing">Submit</button>
                    </div>
                    <FormSubmitAlert :form-state="editUser.formState" :success-msg="editUser.formSuccessMsg" :fail-msg="editUser.formFailMsg"></FormSubmitAlert>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { Link, useForm } from '@inertiajs/vue3';
import BreezeInput from '@/Components/Input.vue';
import BreezeSelect from '@/Components/Select.vue';
import BreezeLabel from '@/Components/Label.vue';
import FormSubmitAlert from '@/Components/FormSubmitAlert.vue';

export default {
    name: 'AdminUsers',
    components: {
        BreezeInput, Link, BreezeSelect, BreezeLabel, FormSubmitAlert
    },
    props: {
        users: Object,
        roles: Object
    },
    data() {
        return {
            editUser: ''
        }
    },
    methods: {
        hasRole: function (index){
            let hasIt = false;
            let name = this.roles[index].name;
            for(let i = 0; i<this.editUser.roles.length; i++){
                if(this.editUser.roles[i].name === name){
                    hasIt = true;
                    break;
                }
            }
            return hasIt;
        },
        roleUpdate: function (e, id) {
            let role = this.roles.filter(role => role.id === id);
            if(e.target.checked){
                this.editUser.updatedRoles.push(role[0]);
            }else{
                // If the value is falsy, remove the role from the updatedRoles array
                this.editUser.updatedRoles = this.editUser.updatedRoles.filter(role => role.id !== id);
            }
        },
        edit: function (i){
            let frm = this.users[i];
            frm.updatedRoles = this.editUser.roles;
            this.editUser = useForm(frm);
            $("#editUserModal").modal('show');
        },
        submitUser: function ()
        {
            this.editUser.formState = '';
            this.editUser.put('/admin/users/' + this.editUser.id, {
                onSuccess: () => {
                    this.editUser.formState = true;
                    $("#editUserModal").modal('hide');
                },
                onError: () => {
                    this.editUser.formState = false;
                }
            });
        },
    }
}

</script>
