<template>

    <div class="list-group">
        <Link :href="'/vss/maintenance/area-of-audit'" class="list-group-item list-group-item-action" :class="page === 'area-of-audit' ? 'active' : ''">Areas of Audit</Link>
        <Link :href="'/vss/maintenance/school'" class="list-group-item list-group-item-action" :class="page === 'school' ? 'active' : ''">Schools</Link>
        <Link :href="'/vss/maintenance/nature-offence'" class="list-group-item list-group-item-action" :class="page === 'nature-offence' ? 'active' : ''">Nature of Offence</Link>
        <Link :href="'/vss/maintenance/referral-source'" class="list-group-item list-group-item-action" :class="page === 'referral-source' ? 'active' : ''">Referral Sources</Link>
        <Link :href="'/vss/maintenance/sanction-type'" class="list-group-item list-group-item-action" :class="page === 'sanction-type' ? 'active' : ''">Sanction Types</Link>
        <Link :href="'/vss/maintenance/staff'" class="list-group-item list-group-item-action" :class="page === 'staff' ? 'active' : ''">Staff</Link>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    page: String,
});

</script>
