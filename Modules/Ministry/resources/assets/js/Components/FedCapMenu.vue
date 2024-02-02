<template>

    <div class="list-group">
        <Link :href="'/ministry/fed_caps/' + id" class="list-group-item list-group-item-action" :class="page === 'details' ? 'active' : ''">Federal Cap Info.</Link>
        <Link :href="'/ministry/fed_caps/' + id + '/caps'" class="list-group-item list-group-item-action" :class="page === 'caps' ? 'active' : ''">Inst. Caps (under this Fed Cap)</Link>
<!--        <Link :href="'/ministry/fed_caps/' + id + '/staff'" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">Attestations</Link>-->
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    page: String,
    id: Number
});

</script>
