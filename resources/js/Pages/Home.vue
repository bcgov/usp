<script setup>

import {Head} from '@inertiajs/vue3';
import {defineComponent, computed, useAttrs} from "vue";
defineComponent( {
    Head
});
defineProps({
    loginAttempt: Boolean,
    hasAccess: Boolean,
    status: String,
});
// Access roles from $attrs
const userRoles = useAttrs().auth?.roles || [];

// Define computed property
const isSuper = computed(() => {
    return userRoles.some(role => role.name === 'Super Admin');
});

</script>
<style scoped>
.bg-bc-gov{
    background: none;
    background-color: #036;
    font-family: 'BCSans', 'Noto Sans', Verdana, Arial, sans-serif;
    color: #036;
}
.box-disabled{
    text-decoration: none;
    background-color: #e0e0e0;
    opacity: 0.7;
    color: #888;
    cursor: not-allowed;
}
</style>
<template>
        <Head title="Select Application" />

    <div class="min-h-screen text-center flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-bc-gov">

    </div>
</template>
