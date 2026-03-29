<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: { type: String, default: 'blue' },
    subtitle: String,
    trend: Number,
});

const colorClasses = {
    blue: 'bg-blue-50 text-blue-600',
    green: 'bg-green-50 text-green-600',
    yellow: 'bg-yellow-50 text-yellow-600',
    red: 'bg-red-50 text-red-600',
    purple: 'bg-purple-50 text-purple-600',
    indigo: 'bg-indigo-50 text-indigo-600',
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-500">{{ title }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ value }}</p>
                <p v-if="subtitle" class="text-xs text-gray-400 mt-1">{{ subtitle }}</p>
            </div>
            <div :class="['w-12 h-12 rounded-lg flex items-center justify-center', colorClasses[color] || colorClasses.blue]">
                <slot name="icon">
                    <span class="text-xl">{{ icon }}</span>
                </slot>
            </div>
        </div>
        <div v-if="trend !== undefined" class="mt-3 flex items-center text-sm">
            <span :class="trend >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
                {{ trend >= 0 ? '+' : '' }}{{ trend }}%
            </span>
            <span class="text-gray-400 ml-2">vs. periodo anterior</span>
        </div>
    </div>
</template>
