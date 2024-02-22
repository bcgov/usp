<script setup>
// defineProps(['value']);
const props = defineProps({
    required: {
        type: [String],
        default: "false",
    },
    value: {
        default: null,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="required==='true'" class="text-danger me-1">*</span>
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
