<script setup>
import { ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Buscar...' },
    routeName: { type: String, required: true },
    filters: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:modelValue']);

const search = ref(props.modelValue);
let timeout = null;

watch(search, (value) => {
    emit('update:modelValue', value);
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route(props.routeName), { ...props.filters, search: value || undefined }, {
            preserveState: true,
            replace: true,
        });
    }, 400);
});
</script>

<template>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input
            v-model="search"
            type="text"
            :placeholder="placeholder"
            class="input-field pl-10"
        />
    </div>
</template>
