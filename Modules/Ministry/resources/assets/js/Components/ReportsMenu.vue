<template>

    <div class="list-group">
        <Link href="/ministry/reports/summary" class="list-group-item list-group-item-action" :class="page.split('summary').length > 1 ? 'active' : ''">Executive Summary</Link>
        <Link href="/ministry/reports/detail" class="list-group-item list-group-item-action" :class="page.split('detail').length > 1 ? 'active' : ''">Report with % Used</Link>
        <Link href="/ministry/reports/sources" class="list-group-item list-group-item-action" :class="page.split('sources').length > 1 ? 'active' : ''">Report Sources</Link>
<!--        <Link href="/ministry/reports/extended" class="list-group-item list-group-item-action" :class="page.split('extended').length > 1 ? 'active' : ''">Report Extended</Link>-->
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    page: String,
});

</script>
