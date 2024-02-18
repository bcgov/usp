<template>
    <Head title="Admin Home" />

    <AuthenticatedLayout>

        <div class="mt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 mt-3">
                        <div class="card">
                            <div class="card-header">
                                Admin Menu
                            </div>
                            <div class="card-body">
                                <AdminMenu :page="page" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 mt-3">
                        <AdminUsers v-if="page === 'users'" :users="users" :roles="roles"></AdminUsers>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
<script>
import AuthenticatedLayout from '@/Layouts/Authenticated';
import { Head, Link } from '@inertiajs/vue3';
import AdminMenu from "@/Components/Admin/Menu";
import AdminUsers from "@/Components/Admin/Users";

export default {
    name: 'Admin',
    components: {
        AdminUsers,
        AdminMenu,
        AuthenticatedLayout, Head, Link
    },
    props: {
        users: Object,
        roles: Object,
        page: String,
    },
    data() {
        return {
        }
    },
}
</script>
