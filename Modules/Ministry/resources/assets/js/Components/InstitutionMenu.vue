<template>

    <div class="list-group">
        <Link href="#" class="list-group-item list-group-item-action disabled list-group-item-secondary">{{ name }}</Link>
        <Link :href="'/ministry/institutions/' + id" class="list-group-item list-group-item-action" :class="page === 'details' ? 'active' : ''">Information &amp; Contact Info.</Link>
        <Link :href="'/ministry/institutions/' + id + '/programs'" class="list-group-item list-group-item-action" :class="page === 'programs' ? 'active' : ''">Programs</Link>
        <Link :href="'/ministry/institutions/' + id + '/attestations'" class="list-group-item list-group-item-action" :class="page === 'attestations' ? 'active' : ''">Attestations</Link>
        <Link :href="'/ministry/institutions/' + id + '/caps'" class="list-group-item list-group-item-action" :class="page === 'caps' ? 'active' : ''">Institution Caps</Link>
        <Link :href="'/ministry/institutions/' + id + '/staff'" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">BCeID Staff List</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    page: String,
    id: Number,
    name: String
});

</script>
