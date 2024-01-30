<template>
    <div class="list-group">
        <Link :href="'/admin/users'" class="list-group-item list-group-item-action" :class="page.split('users').length > 1 ? 'active' : ''">Users</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    page: String,
});

</script>
