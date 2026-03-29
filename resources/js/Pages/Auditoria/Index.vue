<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';

const props = defineProps({
    registros: Object,
    tablas: Array,
    filters: Object,
});

const form = reactive({
    search: props.filters?.search || '',
    accion: props.filters?.accion || '',
    tabla: props.filters?.tabla || '',
});

function filtrar() {
    router.get(route('auditoria.index'), {
        search: form.search || undefined,
        accion: form.accion || undefined,
        tabla: form.tabla || undefined,
    }, { preserveState: true });
}

function limpiar() {
    form.search = '';
    form.accion = '';
    form.tabla = '';
    filtrar();
}

const expandedRow = ref(null);

function toggleRow(id) {
    expandedRow.value = expandedRow.value === id ? null : id;
}

function formatJson(data) {
    if (!data) return null;
    try {
        return typeof data === 'string' ? JSON.parse(data) : data;
    } catch {
        return null;
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

const accionConfig = {
    INSERT: { bg: 'bg-emerald-100 text-emerald-700 ring-emerald-600/20', icon: 'M12 4v16m8-8H4', label: 'Creación' },
    UPDATE: { bg: 'bg-blue-100 text-blue-700 ring-blue-600/20', icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10', label: 'Actualización' },
    DELETE: { bg: 'bg-red-100 text-red-700 ring-red-600/20', icon: 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0', label: 'Eliminación' },
    APROBACION: { bg: 'bg-amber-100 text-amber-700 ring-amber-600/20', icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z', label: 'Aprobación' },
};

function getAccionConfig(accion) {
    return accionConfig[accion] || { bg: 'bg-gray-100 text-gray-700 ring-gray-600/20', icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', label: accion };
}

const totalRegistros = computed(() => props.registros?.total || props.registros?.data?.length || 0);
</script>

<template>
    <Head title="Bitácora de Auditoría" />
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-1">
                    <div class="p-2 bg-brand-blue rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Bitácora de Auditoría</h1>
                        <p class="text-sm text-gray-500">Registro completo de todas las operaciones del sistema</p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ totalRegistros }}</p>
                            <p class="text-xs text-gray-500">Total Registros</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-emerald-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-emerald-700">{{ registros.data.filter(r => r.accion === 'INSERT').length }}</p>
                            <p class="text-xs text-gray-500">Creaciones</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-blue-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-700">{{ registros.data.filter(r => r.accion === 'UPDATE').length }}</p>
                            <p class="text-xs text-gray-500">Actualizaciones</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-red-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-red-700">{{ registros.data.filter(r => r.accion === 'DELETE').length }}</p>
                            <p class="text-xs text-gray-500">Eliminaciones</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Filtros de Búsqueda</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Buscar</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input v-model="form.search" type="text" class="input-field pl-9" placeholder="Usuario, tabla, IP..." @keyup.enter="filtrar" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Acción</label>
                        <select v-model="form.accion" @change="filtrar" class="input-field">
                            <option value="">Todas las acciones</option>
                            <option value="INSERT">Creación (INSERT)</option>
                            <option value="UPDATE">Actualización (UPDATE)</option>
                            <option value="DELETE">Eliminación (DELETE)</option>
                            <option value="APROBACION">Aprobación</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Tabla</label>
                        <select v-model="form.tabla" @change="filtrar" class="input-field">
                            <option value="">Todas las tablas</option>
                            <option v-for="t in tablas" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button @click="filtrar" class="btn-primary flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filtrar
                        </button>
                        <button @click="limpiar" class="btn-secondary flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha y Hora</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Usuario</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Módulo</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dirección IP</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template v-for="reg in registros.data" :key="reg.id">
                                <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="text-sm text-gray-700">{{ formatDate(reg.fecha_evento) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ reg.usuario ? reg.usuario.nombres?.charAt(0)?.toUpperCase() : 'S' }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ reg.usuario ? reg.usuario.nombres + ' ' + reg.usuario.apellidos : 'Sistema' }}</p>
                                                <p class="text-xs text-gray-500">{{ reg.usuario?.email || 'Automático' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold ring-1 ring-inset" :class="getAccionConfig(reg.accion).bg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" :d="getAccionConfig(reg.accion).icon"/></svg>
                                            {{ getAccionConfig(reg.accion).label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                                            <span class="w-2 h-2 rounded-full bg-brand-gold flex-shrink-0"></span>
                                            {{ reg.tabla_nombre }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 bg-gray-100 rounded-md text-xs font-mono font-bold text-gray-600">
                                            #{{ reg.registro_id }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-sm text-gray-500 font-mono">{{ reg.ip_address || '—' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <button
                                            @click="toggleRow(reg.id)"
                                            :class="[
                                                'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200',
                                                expandedRow === reg.id
                                                    ? 'bg-brand-blue text-white shadow-sm'
                                                    : 'bg-gray-100 text-gray-600 hover:bg-brand-blue hover:text-white'
                                            ]"
                                        >
                                            <svg :class="['w-3.5 h-3.5 transition-transform duration-200', expandedRow === reg.id ? 'rotate-180' : '']" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                            {{ expandedRow === reg.id ? 'Ocultar' : 'Ver' }}
                                        </button>
                                    </td>
                                </tr>
                                <!-- Fila expandida con diff -->
                                <tr v-if="expandedRow === reg.id">
                                    <td colspan="7" class="px-5 py-5 bg-gradient-to-b from-slate-50 to-white border-b-2 border-brand-gold/30">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                            <!-- Datos Anteriores -->
                                            <div class="rounded-xl border border-red-200 overflow-hidden">
                                                <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50 border-b border-red-200">
                                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <h4 class="text-sm font-bold text-red-700">Datos Anteriores</h4>
                                                </div>
                                                <div class="p-4 max-h-72 overflow-auto">
                                                    <template v-if="formatJson(reg.datos_anteriores)">
                                                        <div v-for="(val, key) in formatJson(reg.datos_anteriores)" :key="key" class="flex gap-2 py-1.5 border-b border-gray-100 last:border-0">
                                                            <span class="text-xs font-semibold text-gray-500 min-w-[120px] uppercase">{{ key }}</span>
                                                            <span class="text-xs text-gray-800 break-all">{{ val ?? '—' }}</span>
                                                        </div>
                                                    </template>
                                                    <p v-else class="text-xs text-gray-400 italic">Sin datos anteriores (registro nuevo)</p>
                                                </div>
                                            </div>
                                            <!-- Datos Nuevos -->
                                            <div class="rounded-xl border border-emerald-200 overflow-hidden">
                                                <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border-b border-emerald-200">
                                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <h4 class="text-sm font-bold text-emerald-700">Datos Nuevos</h4>
                                                </div>
                                                <div class="p-4 max-h-72 overflow-auto">
                                                    <template v-if="formatJson(reg.datos_nuevos)">
                                                        <div v-for="(val, key) in formatJson(reg.datos_nuevos)" :key="key" class="flex gap-2 py-1.5 border-b border-gray-100 last:border-0">
                                                            <span class="text-xs font-semibold text-gray-500 min-w-[120px] uppercase">{{ key }}</span>
                                                            <span class="text-xs text-gray-800 break-all">{{ val ?? '—' }}</span>
                                                        </div>
                                                    </template>
                                                    <p v-else class="text-xs text-gray-400 italic">Sin datos nuevos</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="registros.data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                    <p class="text-sm font-medium text-gray-500">No se encontraron registros de auditoría</p>
                                    <p class="text-xs text-gray-400 mt-1">Ajuste los filtros o realice una operación en el sistema</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="registros.data.length > 0" class="px-5 py-4 border-t border-gray-200 bg-gray-50/50">
                    <Pagination :links="registros.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
