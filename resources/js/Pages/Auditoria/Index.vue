<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';

const props = defineProps({
    registros: Object,
    modelos: Array,
    usuarios: Array,
    stats: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters?.search || '',
    evento: props.filters?.evento || '',
    modelo: props.filters?.modelo || '',
    usuario_id: props.filters?.usuario_id || '',
    fecha_desde: props.filters?.fecha_desde || '',
    fecha_hasta: props.filters?.fecha_hasta || '',
});

const showFilters = ref(false);
const expandedRow = ref(null);
const diffMode = ref('side');

function filtrar() {
    router.get(route('auditoria.index'), {
        search: form.search || undefined,
        evento: form.evento || undefined,
        modelo: form.modelo || undefined,
        usuario_id: form.usuario_id || undefined,
        fecha_desde: form.fecha_desde || undefined,
        fecha_hasta: form.fecha_hasta || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function limpiar() {
    Object.keys(form).forEach(k => form[k] = '');
    filtrar();
}

function toggleRow(id) {
    expandedRow.value = expandedRow.value === id ? null : id;
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' }) +
        ' ' + d.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function formatRelative(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'hace unos segundos';
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)}h`;
    if (diff < 604800) return `hace ${Math.floor(diff / 86400)}d`;
    return '';
}

const eventoConfig = {
    created: { bg: 'bg-emerald-100 text-emerald-800 ring-emerald-600/20', icon: 'M12 4.5v15m7.5-7.5h-15', label: 'Creación', dot: 'bg-emerald-500' },
    updated: { bg: 'bg-blue-100 text-blue-800 ring-blue-600/20', icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10', label: 'Actualización', dot: 'bg-blue-500' },
    deleted: { bg: 'bg-red-100 text-red-800 ring-red-600/20', icon: 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0', label: 'Eliminación', dot: 'bg-red-500' },
    restored: { bg: 'bg-amber-100 text-amber-800 ring-amber-600/20', icon: 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', label: 'Restauración', dot: 'bg-amber-500' },
};

function getEventConfig(event) {
    return eventoConfig[event] || { bg: 'bg-gray-100 text-gray-700 ring-gray-600/20', icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', label: event, dot: 'bg-gray-400' };
}

function getChangedFields(oldVals, newVals) {
    if (!oldVals && !newVals) return [];
    const all = new Set([...Object.keys(oldVals || {}), ...Object.keys(newVals || {})]);
    return Array.from(all).map(key => ({
        field: key,
        old: oldVals?.[key] ?? null,
        new: newVals?.[key] ?? null,
        changed: JSON.stringify(oldVals?.[key]) !== JSON.stringify(newVals?.[key]),
    }));
}

const activeFiltersCount = computed(() =>
    Object.values(form).filter(v => v !== '' && v !== null && v !== undefined).length
);
</script>

<template>
    <Head title="Bitácora de Auditoría" />
    <AppLayout>
        <div class="max-w-[90rem] mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-br from-brand-blue to-indigo-700 rounded-xl shadow-lg shadow-brand-blue/20">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Bitácora de Auditoría</h1>
                        <p class="text-sm text-gray-500">Registro automático de todos los cambios en el sistema</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.total.toLocaleString() }}</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-xl">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-emerald-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Creaciones</p>
                            <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.creaciones.toLocaleString() }}</p>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-xl">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-blue-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Actualizaciones</p>
                            <p class="text-3xl font-bold text-blue-700 mt-1">{{ stats.actualizaciones.toLocaleString() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-red-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-red-600 uppercase tracking-wider">Eliminaciones</p>
                            <p class="text-3xl font-bold text-red-700 mt-1">{{ stats.eliminaciones.toLocaleString() }}</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-xl">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input v-model="form.search" type="text" class="input-field pl-11 w-full" placeholder="Buscar por usuario, modelo, datos, IP..." @keyup.enter="filtrar" />
                        </div>
                        <div class="flex gap-2">
                            <button @click="showFilters = !showFilters" :class="['btn-secondary flex items-center gap-2 relative', showFilters && 'ring-2 ring-brand-blue/30']">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                                Filtros
                                <span v-if="activeFiltersCount > 0" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-brand-blue text-white text-xs font-bold rounded-full flex items-center justify-center">{{ activeFiltersCount }}</span>
                            </button>
                            <button @click="filtrar" class="btn-primary flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Buscar
                            </button>
                        </div>
                    </div>

                    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                        <div v-show="showFilters" class="mt-4 pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Evento</label>
                                    <select v-model="form.evento" @change="filtrar" class="input-field">
                                        <option value="">Todos</option>
                                        <option value="created">Creación</option>
                                        <option value="updated">Actualización</option>
                                        <option value="deleted">Eliminación</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Modelo</label>
                                    <select v-model="form.modelo" @change="filtrar" class="input-field">
                                        <option value="">Todos</option>
                                        <option v-for="m in modelos" :key="m.value" :value="m.value">{{ m.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Usuario</label>
                                    <select v-model="form.usuario_id" @change="filtrar" class="input-field">
                                        <option value="">Todos</option>
                                        <option v-for="u in usuarios" :key="u.id" :value="u.id">{{ u.nombres }} {{ u.apellidos }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Desde</label>
                                    <input v-model="form.fecha_desde" type="date" class="input-field" @change="filtrar" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Hasta</label>
                                    <input v-model="form.fecha_hasta" type="date" class="input-field" @change="filtrar" />
                                </div>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button @click="limpiar" class="text-sm text-gray-500 hover:text-brand-blue flex items-center gap-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100/80 border-b border-gray-200">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-[180px]">Fecha</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Usuario</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-[130px]">Evento</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Modelo</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-[70px]">ID</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-[130px]">IP</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-[100px]">Cambios</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template v-for="reg in registros.data" :key="reg.id">
                                <tr class="hover:bg-blue-50/40 transition-all duration-150 cursor-pointer" @click="toggleRow(reg.id)">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div>
                                            <p class="text-sm text-gray-800 font-medium">{{ formatDate(reg.fecha) }}</p>
                                            <p class="text-xs text-gray-400">{{ formatRelative(reg.fecha) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-blue to-indigo-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
                                                {{ reg.usuario ? reg.usuario.nombres?.charAt(0)?.toUpperCase() : 'S' }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ reg.usuario ? reg.usuario.nombres + ' ' + reg.usuario.apellidos : 'Sistema' }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ reg.usuario?.email || 'Automático' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ring-1 ring-inset" :class="getEventConfig(reg.evento).bg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="getEventConfig(reg.evento).icon"/></svg>
                                            {{ getEventConfig(reg.evento).label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="getEventConfig(reg.evento).dot"></span>
                                            {{ reg.modelo_tipo }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 bg-gray-100 rounded-md text-xs font-mono font-bold text-gray-600">#{{ reg.modelo_id }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-xs text-gray-500 font-mono">{{ reg.ip_address || '—' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <button @click.stop="toggleRow(reg.id)" :class="['inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200', expandedRow === reg.id ? 'bg-brand-blue text-white shadow-md shadow-brand-blue/20' : 'bg-gray-100 text-gray-600 hover:bg-brand-blue hover:text-white hover:shadow-md']">
                                            <svg :class="['w-3.5 h-3.5 transition-transform duration-200', expandedRow === reg.id ? 'rotate-180' : '']" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            {{ expandedRow === reg.id ? 'Cerrar' : 'Ver' }}
                                        </button>
                                    </td>
                                </tr>

                                <!-- Detalle expandido - Diff -->
                                <tr v-if="expandedRow === reg.id">
                                    <td colspan="7" class="px-0 py-0">
                                        <div class="bg-gradient-to-b from-slate-50 to-white border-b-2 border-brand-gold/30 p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-4">
                                                    <h4 class="text-sm font-bold text-gray-700">Detalle de cambios</h4>
                                                    <span v-if="reg.url" class="text-xs text-gray-400 font-mono truncate max-w-xs">{{ reg.url }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-0.5">
                                                    <button @click="diffMode = 'side'" :class="['px-3 py-1 text-xs font-medium rounded-md transition-colors', diffMode === 'side' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700']">Lado a lado</button>
                                                    <button @click="diffMode = 'unified'" :class="['px-3 py-1 text-xs font-medium rounded-md transition-colors', diffMode === 'unified' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700']">Unificado</button>
                                                </div>
                                            </div>

                                            <!-- Side by side -->
                                            <div v-if="diffMode === 'side'" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                <div class="rounded-xl border border-red-200 overflow-hidden">
                                                    <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50 border-b border-red-200">
                                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <h4 class="text-sm font-bold text-red-700">Valores Anteriores</h4>
                                                    </div>
                                                    <div class="p-3 max-h-80 overflow-auto">
                                                        <template v-if="reg.datos_anteriores && Object.keys(reg.datos_anteriores).length">
                                                            <div v-for="(val, key) in reg.datos_anteriores" :key="key" class="flex gap-2 py-2 px-2 rounded-lg border-b border-gray-50 last:border-0" :class="reg.datos_nuevos && JSON.stringify(reg.datos_nuevos[key]) !== JSON.stringify(val) ? 'bg-red-50/60' : ''">
                                                                <span class="text-xs font-bold text-gray-500 min-w-[130px] uppercase tracking-wide">{{ key }}</span>
                                                                <span class="text-xs text-gray-800 break-all font-mono">{{ val ?? '—' }}</span>
                                                            </div>
                                                        </template>
                                                        <p v-else class="text-xs text-gray-400 italic py-6 text-center">Sin datos anteriores (registro nuevo)</p>
                                                    </div>
                                                </div>
                                                <div class="rounded-xl border border-emerald-200 overflow-hidden">
                                                    <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border-b border-emerald-200">
                                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <h4 class="text-sm font-bold text-emerald-700">Valores Nuevos</h4>
                                                    </div>
                                                    <div class="p-3 max-h-80 overflow-auto">
                                                        <template v-if="reg.datos_nuevos && Object.keys(reg.datos_nuevos).length">
                                                            <div v-for="(val, key) in reg.datos_nuevos" :key="key" class="flex gap-2 py-2 px-2 rounded-lg border-b border-gray-50 last:border-0" :class="reg.datos_anteriores && JSON.stringify(reg.datos_anteriores[key]) !== JSON.stringify(val) ? 'bg-emerald-50/60' : ''">
                                                                <span class="text-xs font-bold text-gray-500 min-w-[130px] uppercase tracking-wide">{{ key }}</span>
                                                                <span class="text-xs text-gray-800 break-all font-mono">{{ val ?? '—' }}</span>
                                                            </div>
                                                        </template>
                                                        <p v-else class="text-xs text-gray-400 italic py-6 text-center">Sin datos nuevos</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Unified -->
                                            <div v-else class="rounded-xl border border-gray-200 overflow-hidden">
                                                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5 grid grid-cols-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    <span>Campo</span>
                                                    <span>Anterior</span>
                                                    <span>Nuevo</span>
                                                </div>
                                                <div class="divide-y divide-gray-50 max-h-80 overflow-auto">
                                                    <div v-for="field in getChangedFields(reg.datos_anteriores, reg.datos_nuevos)" :key="field.field" class="grid grid-cols-3 px-4 py-2.5 text-xs" :class="field.changed ? 'bg-amber-50/50' : ''">
                                                        <span class="font-bold text-gray-600 uppercase tracking-wide flex items-center gap-1">
                                                            <span v-if="field.changed" class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                                                            {{ field.field }}
                                                        </span>
                                                        <span class="font-mono break-all" :class="field.changed ? 'text-red-600 line-through' : 'text-gray-500'">{{ field.old ?? '—' }}</span>
                                                        <span class="font-mono break-all" :class="field.changed ? 'text-emerald-600 font-semibold' : 'text-gray-500'">{{ field.new ?? '—' }}</span>
                                                    </div>
                                                    <div v-if="!getChangedFields(reg.datos_anteriores, reg.datos_nuevos).length" class="px-4 py-8 text-center text-gray-400 italic">Sin datos</div>
                                                </div>
                                            </div>

                                            <!-- Metadata -->
                                            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-400">
                                                <span v-if="reg.user_agent" class="flex items-center gap-1" :title="reg.user_agent">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                                    {{ reg.user_agent?.substring(0, 80) }}...
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/></svg>
                                                    Audit #{{ reg.id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-if="registros.data.length === 0">
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-gray-100 rounded-2xl mb-4">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-500">No se encontraron registros</p>
                                        <p class="text-sm text-gray-400 mt-1">Realice operaciones en el sistema para generar auditorías automáticas</p>
                                        <button v-if="activeFiltersCount > 0" @click="limpiar" class="mt-4 btn-secondary text-sm">Limpiar filtros</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="registros.data.length > 0" class="px-5 py-4 border-t border-gray-200 bg-gray-50/50">
                    <Pagination :links="registros.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
