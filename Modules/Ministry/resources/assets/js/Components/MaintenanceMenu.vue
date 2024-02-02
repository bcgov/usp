<template>

    <div class="list-group">
        <Link :href="'/ministry/maintenance/staff'" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">Staff</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    page: String,
});

</script>
