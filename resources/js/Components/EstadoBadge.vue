<script setup>
const props = defineProps({
    estado: { type: String, required: true },
});

const estadoConfig = {
    pendiente: { label: 'Pendiente', class: 'bg-yellow-100 text-yellow-800 border-yellow-300' },
    en_revision: { label: 'En Revisión', class: 'bg-blue-100 text-blue-800 border-blue-300' },
    aprobado: { label: 'Aprobado', class: 'bg-green-100 text-green-800 border-green-300' },
    rechazado: { label: 'Rechazado', class: 'bg-red-100 text-red-800 border-red-300' },
    ejecutado: { label: 'Ejecutado', class: 'bg-purple-100 text-purple-800 border-purple-300' },
    anulado: { label: 'Anulado', class: 'bg-gray-100 text-gray-800 border-gray-300' },
    borrador: { label: 'Borrador', class: 'bg-gray-100 text-gray-600 border-gray-300' },
    activo: { label: 'Activo', class: 'bg-green-100 text-green-800 border-green-300' },
    cerrado: { label: 'Cerrado', class: 'bg-gray-100 text-gray-800 border-gray-300' },
    ejecutando: { label: 'Ejecutando', class: 'bg-blue-100 text-blue-800 border-blue-300' },
};

const config = estadoConfig[props.estado] || { label: props.estado, class: 'bg-gray-100 text-gray-800 border-gray-300' };
</script>

<template>
    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border', config.class]">
        {{ config.label }}
    </span>
</template>
