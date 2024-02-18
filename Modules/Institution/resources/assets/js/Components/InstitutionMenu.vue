<template>

    <div class="list-group">
        <Link :href="'/ministry/institutions/' + id" class="list-group-item list-group-item-action" :class="page === 'details' ? 'active' : ''">Information &amp; Contact Info.</Link>
        <Link :href="'/ministry/institutions/' + id + '/caps'" class="list-group-item list-group-item-action" :class="page === 'caps' ? 'active' : ''">Institution Caps</Link>
        <Link :href="'/ministry/institutions/' + id + '/staff'" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">BCeID Staff List</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    page: String,
    id: Number
});

</script>
