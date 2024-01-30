<template>

    <AuthenticatedLayout v-bind="$attrs">

        <div class="card">
            <div class="card-header">
                <div>Staff Maintenance</div>
            </div>

            <div class="card-body">
                <div v-if="results != null" class="table-responsive pb-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">User ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Access Type</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, i) in results">
                                <th scope="row">
                                    <Link :href="'/neb/staff/' + row.id">{{ row.user_id }}</Link>
                                </th>
                                <td>{{ row.first_name }}</td>
                                <td>{{ row.last_name }}</td>
                                <td>{{ row.email }}</td>
                                <td>
                                    <div v-for="role in row.roles">{{ role.name }}</div>
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
    </AuthenticatedLayout>
</template>
<script>
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '../Layouts/Authenticated.vue';

export default {
    name: 'Staff',
    components: {
        Link, AuthenticatedLayout, Head
    },
    props: {
        results: Object,
    },
    data() {
        return {
        }
    },
}

</script>
