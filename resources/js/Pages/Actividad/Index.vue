<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';

const props = defineProps({
    registros: Object,
    logNames: Array,
    eventos: Array,
    usuarios: Array,
    stats: Object,
    filters: Object,
});

const form = reactive({
    search: props.filters?.search || '',
    log_name: props.filters?.log_name || '',
    evento: props.filters?.evento || '',
    usuario_id: props.filters?.usuario_id || '',
    fecha_desde: props.filters?.fecha_desde || '',
    fecha_hasta: props.filters?.fecha_hasta || '',
});

const showFilters = ref(false);

function filtrar() {
    router.get(route('actividad.index'), {
        search: form.search || undefined,
        log_name: form.log_name || undefined,
        evento: form.evento || undefined,
        usuario_id: form.usuario_id || undefined,
        fecha_desde: form.fecha_desde || undefined,
        fecha_hasta: form.fecha_hasta || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function limpiar() {
    Object.keys(form).forEach(k => form[k] = '');
    filtrar();
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' }) +
        ' ' + d.toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });
}

function formatRelative(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'hace unos segundos';
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)}h`;
    if (diff < 172800) return 'ayer';
    if (diff < 604800) return `hace ${Math.floor(diff / 86400)} días`;
    return '';
}

const eventoConfig = {
    crear:          { icon: 'M12 4.5v15m7.5-7.5h-15', color: 'text-emerald-500', bg: 'bg-emerald-100', ring: 'ring-emerald-500/30', label: 'Creación' },
    actualizar:     { icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10', color: 'text-blue-500', bg: 'bg-blue-100', ring: 'ring-blue-500/30', label: 'Actualización' },
    eliminar:       { icon: 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0', color: 'text-red-500', bg: 'bg-red-100', ring: 'ring-red-500/30', label: 'Eliminación' },
    aprobar:        { icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-green-600', bg: 'bg-green-100', ring: 'ring-green-500/30', label: 'Aprobación' },
    rechazar:       { icon: 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-rose-600', bg: 'bg-rose-100', ring: 'ring-rose-500/30', label: 'Rechazo' },
    login:          { icon: 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9', color: 'text-indigo-500', bg: 'bg-indigo-100', ring: 'ring-indigo-500/30', label: 'Inicio de sesión' },
    logout:         { icon: 'M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75', color: 'text-slate-500', bg: 'bg-slate-100', ring: 'ring-slate-500/30', label: 'Cierre de sesión' },
    cambio_estado:  { icon: 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5', color: 'text-amber-500', bg: 'bg-amber-100', ring: 'ring-amber-500/30', label: 'Cambio de estado' },
    cambio_password:{ icon: 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z', color: 'text-orange-500', bg: 'bg-orange-100', ring: 'ring-orange-500/30', label: 'Cambio de contraseña' },
    adjuntar:       { icon: 'M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13', color: 'text-cyan-500', bg: 'bg-cyan-100', ring: 'ring-cyan-500/30', label: 'Archivo adjunto' },
};

function getEventConfig(event) {
    return eventoConfig[event] || { icon: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-gray-400', bg: 'bg-gray-100', ring: 'ring-gray-400/30', label: event || 'Acción' };
}

const logNameConfig = {
    'catálogos':      { label: 'Catálogos',       color: 'bg-teal-50 text-teal-700 ring-teal-600/20' },
    'presupuestos':   { label: 'Presupuestos',    color: 'bg-blue-50 text-blue-700 ring-blue-600/20' },
    'solicitudes':    { label: 'Solicitudes',      color: 'bg-amber-50 text-amber-700 ring-amber-600/20' },
    'aprobaciones':   { label: 'Aprobaciones',     color: 'bg-green-50 text-green-700 ring-green-600/20' },
    'ejecuciones':    { label: 'Ejecuciones',      color: 'bg-orange-50 text-orange-700 ring-orange-600/20' },
    'gestiones':      { label: 'Gestiones',        color: 'bg-purple-50 text-purple-700 ring-purple-600/20' },
    'usuarios':       { label: 'Usuarios',         color: 'bg-pink-50 text-pink-700 ring-pink-600/20' },
    'perfil':         { label: 'Perfil',           color: 'bg-fuchsia-50 text-fuchsia-700 ring-fuchsia-600/20' },
    'autenticación':  { label: 'Autenticación',    color: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20' },
};

function getLogConfig(name) {
    return logNameConfig[name] || { label: name || 'Sistema', color: 'bg-gray-50 text-gray-700 ring-gray-600/20' };
}

function getUserInitials(causer) {
    if (!causer) return 'S';
    return (causer.nombres?.charAt(0) || '') + (causer.apellidos?.charAt(0) || '');
}

function getUserAvatarUrl(causer) {
    if (!causer?.avatar) return null;
    return `/storage/${causer.avatar}`;
}

const activeFiltersCount = computed(() =>
    Object.values(form).filter(v => v !== '' && v !== null && v !== undefined).length
);

// Agrupar registros por fecha
const groupedByDate = computed(() => {
    const groups = {};
    for (const reg of props.registros.data) {
        const d = new Date(reg.fecha);
        const key = d.toLocaleDateString('es-BO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        if (!groups[key]) groups[key] = [];
        groups[key].push(reg);
    }
    return groups;
});
</script>

<template>
    <Head title="Registro de Actividad" />
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-br from-violet-600 to-purple-700 rounded-xl shadow-lg shadow-violet-600/20">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Registro de Actividad</h1>
                        <p class="text-sm text-gray-500">Línea de tiempo de acciones realizadas en el sistema</p>
                    </div>
                </div>
            </div>

            <!-- Stats compactos -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 shadow-sm">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ stats.total.toLocaleString() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-emerald-200 px-4 py-3 shadow-sm">
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Hoy</p>
                    <p class="text-2xl font-bold text-emerald-700 mt-0.5">{{ stats.hoy.toLocaleString() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-blue-200 px-4 py-3 shadow-sm">
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Semana</p>
                    <p class="text-2xl font-bold text-blue-700 mt-0.5">{{ stats.semana.toLocaleString() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-violet-200 px-4 py-3 shadow-sm">
                    <p class="text-[10px] font-bold text-violet-500 uppercase tracking-widest">Usuarios</p>
                    <p class="text-2xl font-bold text-violet-700 mt-0.5">{{ stats.usuarios.toLocaleString() }}</p>
                </div>
                <div class="bg-white rounded-xl border border-indigo-200 px-4 py-3 shadow-sm">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Logins hoy</p>
                    <p class="text-2xl font-bold text-indigo-700 mt-0.5">{{ (stats.logins_hoy ?? 0).toLocaleString() }}</p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input v-model="form.search" type="text" class="input-field pl-11 w-full" placeholder="Buscar acciones..." @keyup.enter="filtrar" />
                        </div>
                        <div class="flex gap-2">
                            <button @click="showFilters = !showFilters" :class="['btn-secondary flex items-center gap-2 relative', showFilters && 'ring-2 ring-violet-400/30']">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                                Filtros
                                <span v-if="activeFiltersCount > 0" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-violet-600 text-white text-xs font-bold rounded-full flex items-center justify-center">{{ activeFiltersCount }}</span>
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
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Módulo</label>
                                    <select v-model="form.log_name" @change="filtrar" class="input-field">
                                        <option value="">Todos</option>
                                        <option v-for="l in logNames" :key="l" :value="l">{{ getLogConfig(l).label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Tipo de Acción</label>
                                    <select v-model="form.evento" @change="filtrar" class="input-field">
                                        <option value="">Todos</option>
                                        <option v-for="e in eventos" :key="e" :value="e">{{ getEventConfig(e).label }}</option>
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
                                <button @click="limpiar" class="text-sm text-gray-500 hover:text-violet-600 flex items-center gap-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- Timeline -->
            <div v-if="registros.data.length > 0" class="space-y-8">
                <div v-for="(items, dateLabel) in groupedByDate" :key="dateLabel">
                    <!-- Date divider -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <span class="text-sm font-semibold text-gray-600 capitalize">{{ dateLabel }}</span>
                        </div>
                        <div class="flex-1 border-t border-gray-200"></div>
                        <span class="text-xs text-gray-400 tabular-nums">{{ items.length }} {{ items.length === 1 ? 'acción' : 'acciones' }}</span>
                    </div>

                    <!-- Timeline items -->
                    <div class="relative">
                        <!-- Vertical line -->
                        <div class="absolute left-[23px] top-0 bottom-0 w-px bg-gradient-to-b from-violet-200 via-gray-200 to-transparent"></div>

                        <div class="space-y-1">
                            <div v-for="reg in items" :key="reg.id" class="relative flex gap-4 group">
                                <!-- Timeline dot -->
                                <div class="relative z-10 flex-shrink-0">
                                    <div :class="['w-[46px] h-[46px] rounded-full flex items-center justify-center ring-4 ring-white shadow-sm transition-all duration-200 group-hover:shadow-md group-hover:scale-105', getEventConfig(reg.evento).bg]">
                                        <svg :class="['w-5 h-5', getEventConfig(reg.evento).color]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="getEventConfig(reg.evento).icon"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Content card -->
                                <div class="flex-1 pb-5 min-w-0">
                                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-violet-200 transition-all duration-200 overflow-hidden">
                                        <div class="px-4 py-3">
                                            <!-- Top row: user + time -->
                                            <div class="flex items-start justify-between gap-3 mb-1.5">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <!-- Avatar -->
                                                    <div class="flex-shrink-0">
                                                        <img v-if="getUserAvatarUrl(reg.causer)" :src="getUserAvatarUrl(reg.causer)" class="w-7 h-7 rounded-full object-cover ring-1 ring-gray-200" />
                                                        <div v-else class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white flex items-center justify-center text-[10px] font-bold ring-1 ring-violet-200">
                                                            {{ getUserInitials(reg.causer) }}
                                                        </div>
                                                    </div>
                                                    <!-- User name -->
                                                    <span class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ reg.causer ? reg.causer.nombres + ' ' + reg.causer.apellidos : 'Sistema' }}
                                                    </span>
                                                </div>
                                                <!-- Time -->
                                                <div class="flex-shrink-0 text-right">
                                                    <p class="text-xs text-gray-400 tabular-nums">{{ formatDate(reg.fecha) }}</p>
                                                    <p v-if="formatRelative(reg.fecha)" class="text-[10px] text-violet-500 font-medium">{{ formatRelative(reg.fecha) }}</p>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <p class="text-sm text-gray-700 leading-relaxed mb-2">{{ reg.descripcion }}</p>

                                            <!-- Tags -->
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span :class="['inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset', getLogConfig(reg.log_name).color]">
                                                    {{ getLogConfig(reg.log_name).label }}
                                                </span>
                                                <span :class="['inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset', getEventConfig(reg.evento).bg, getEventConfig(reg.evento).color, getEventConfig(reg.evento).ring]">
                                                    {{ getEventConfig(reg.evento).label }}
                                                </span>
                                                <!-- Extra properties pills -->
                                                <template v-if="reg.propiedades">
                                                    <span v-if="reg.propiedades.codigo" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200">
                                                        {{ reg.propiedades.codigo }}
                                                    </span>
                                                    <span v-if="reg.propiedades.estado" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200">
                                                        {{ reg.propiedades.estado }}
                                                    </span>
                                                    <span v-if="reg.propiedades.archivo" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-cyan-50 text-cyan-700 ring-1 ring-inset ring-cyan-200" :title="reg.propiedades.archivo">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                                        {{ reg.propiedades.archivo.length > 25 ? reg.propiedades.archivo.substring(0, 25) + '...' : reg.propiedades.archivo }}
                                                    </span>
                                                    <span v-if="reg.propiedades.motivo" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200 max-w-[200px] truncate" :title="reg.propiedades.motivo">
                                                        Motivo: {{ reg.propiedades.motivo }}
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                    <Pagination :links="registros.links" />
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-20 text-center">
                    <div class="inline-flex items-center justify-center p-5 bg-violet-100 rounded-2xl mb-5">
                        <svg class="w-14 h-14 text-violet-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-500">Sin registros de actividad</p>
                    <p class="text-sm text-gray-400 mt-1">Las acciones del sistema aparecerán aquí como una línea de tiempo</p>
                    <button v-if="activeFiltersCount > 0" @click="limpiar" class="mt-5 btn-secondary text-sm">Limpiar filtros</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
