<template>

    <div class="list-group">
        <Link href="/dashboard" class="list-group-item list-group-item-action" :class="page === 'home' ? 'active' : ''">Home</Link>
        <Link href="/bursary-periods" class="list-group-item list-group-item-action" :class="page === 'bursary-periods' ? 'active' : ''">Bursary Periods</Link>
        <Link href="/staff" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">Staff</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    page: String,
});

</script>
