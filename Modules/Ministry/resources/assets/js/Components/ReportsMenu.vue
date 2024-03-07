<template>

    <div class="list-group">
        <Link href="/ministry/reports/summary" class="list-group-item list-group-item-action" :class="page.split('summary').length > 1 ? 'active' : ''">Executive Summary</Link>
        <Link href="/ministry/reports/detail" class="list-group-item list-group-item-action" :class="page.split('detail').length > 1 ? 'active' : ''">Detailed Reporting</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    page: String,
});

</script>
