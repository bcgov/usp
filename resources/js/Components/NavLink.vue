<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps(['href', 'active']);

const classes = computed(() => props.active
    ? 'navlink-active inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 transition  duration-150 ease-in-out nav-link me-3'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-gray-300 focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out nav-link me-3'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
