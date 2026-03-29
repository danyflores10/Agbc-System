<script setup>
defineProps({
    type: { type: String, default: 'info' },
});

const colors = {
    info: 'bg-blue-50 text-blue-700 border-blue-200',
    success: 'bg-green-50 text-green-700 border-green-200',
    warning: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    danger: 'bg-red-50 text-red-700 border-red-200',
};
</script>

<template>
    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', colors[type] || colors.info]">
        <slot />
    </span>
</template>
