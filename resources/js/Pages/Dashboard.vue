<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    gestionActual: Object,
    stats: Object,
    ejecucionPorMes: Array,
    presupuestoPorArea: Array,
    solicitudesRecientes: Array,
});

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

function formatShort(val) {
    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
    if (val >= 1000) return (val / 1000).toFixed(1) + 'K';
    return val?.toString() || '0';
}

const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

const saldoDisponible = computed(() =>
    (props.stats?.presupuesto_vigente || 0) - (props.stats?.total_ejecutado || 0)
);

const porcentajeEjecucion = computed(() => {
    if (!props.stats?.presupuesto_vigente) return 0;
    return Math.min(Math.round((props.stats.total_ejecutado / props.stats.presupuesto_vigente) * 100), 100);
});

const maxEjecucion = computed(() => {
    const vals = (props.ejecucionPorMes || []).map(e => parseFloat(e.total) || 0);
    return Math.max(...vals, 1);
});

function barHeight(mes) {
    const item = (props.ejecucionPorMes || []).find(e => e.mes == mes);
    return Math.max((parseFloat(item?.total) || 0) / maxEjecucion.value * 100, 2);
}

function barValue(mes) {
    const item = (props.ejecucionPorMes || []).find(e => e.mes == mes);
    return parseFloat(item?.total) || 0;
}
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Panel de Control Presupuestario</h1>
            <p class="text-sm text-gray-500 mt-1">Agencia Boliviana de Correos - Gestión {{ gestionActual?.anio || '' }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Presupuesto Vigente</p>
                <p class="text-xl font-bold text-brand-blue mt-1">{{ formatMoney(stats?.presupuesto_vigente) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Ejecutado</p>
                <p class="text-xl font-bold text-green-600 mt-1">{{ formatMoney(stats?.total_ejecutado) }}</p>
                <p class="text-xs text-gray-400">{{ porcentajeEjecucion }}% del presupuesto</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Saldo Disponible</p>
                <p class="text-xl font-bold mt-1" :class="saldoDisponible < 0 ? 'text-red-600' : 'text-yellow-600'">{{ formatMoney(saldoDisponible) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Solicitudes Pendientes</p>
                <p class="text-xl font-bold text-red-600 mt-1">{{ stats?.solicitudes_pendientes || 0 }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Progreso de Ejecución</h3>
                <span class="text-2xl font-bold text-brand-blue">{{ porcentajeEjecucion }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="h-4 rounded-full transition-all duration-700 bg-gradient-to-r from-brand-blue to-sidebar" :style="{ width: porcentajeEjecucion + '%' }"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-2">
                <span>Ejecutado: {{ formatMoney(stats?.total_ejecutado) }}</span>
                <span>Comprometido: {{ formatMoney(stats?.total_comprometido) }}</span>
                <span>Disponible: {{ formatMoney(saldoDisponible) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ejecución por Mes</h3>
                <div class="flex items-end justify-between h-48 gap-1">
                    <div v-for="m in 12" :key="m" class="flex-1 flex flex-col items-center">
                        <span class="text-xs text-gray-500 mb-1">{{ formatShort(barValue(m)) }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-brand-blue to-sidebar transition-all duration-500" :style="{ height: barHeight(m) + '%' }"></div>
                        <span class="text-xs text-gray-400 mt-1">{{ meses[m - 1] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Presupuesto por Área</h3>
                <div class="space-y-3 max-h-48 overflow-y-auto">
                    <div v-for="area in presupuestoPorArea" :key="area.id" class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-700">{{ area.nombre }}</span>
                            <span class="text-xs text-gray-500">{{ formatMoney(area.monto_vigente) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full bg-brand-blue" :style="{ width: Math.min((area.monto_vigente / (stats?.presupuesto_vigente || 1)) * 100, 100) + '%' }"></div>
                        </div>
                    </div>
                    <p v-if="!presupuestoPorArea?.length" class="text-gray-400 text-sm text-center py-4">Sin datos</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Solicitudes Recientes</h3>
                    <Link :href="route('solicitudes.index')" class="text-sm text-brand-blue hover:underline">Ver todas</Link>
                </div>
                <div class="space-y-3">
                    <div v-for="sol in solicitudesRecientes" :key="sol.id" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ sol.concepto }}</p>
                            <p class="text-xs text-gray-500">{{ sol.codigo }} - {{ sol.centro_costo?.area?.nombre }}</p>
                        </div>
                        <span class="ml-3 px-2 py-1 text-xs rounded-full font-medium"
                            :class="{
                                'bg-yellow-100 text-yellow-800': sol.estado === 'PENDIENTE',
                                'bg-blue-100 text-blue-800': sol.estado === 'EN_REVISION',
                                'bg-green-100 text-green-800': sol.estado === 'APROBADA',
                                'bg-red-100 text-red-800': sol.estado === 'RECHAZADA',
                                'bg-gray-100 text-gray-800': sol.estado === 'BORRADOR',
                            }">{{ sol.estado }}</span>
                    </div>
                    <p v-if="!solicitudesRecientes?.length" class="text-gray-400 text-sm text-center py-4">Sin solicitudes recientes</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-2 gap-3">
                    <Link :href="route('solicitudes.create')" class="flex flex-col items-center p-4 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors">
                        <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span class="text-sm font-medium text-blue-700">Nueva Solicitud</span>
                    </Link>
                    <Link :href="route('presupuestos.create')" class="flex flex-col items-center p-4 rounded-lg bg-green-50 hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
                        <span class="text-sm font-medium text-green-700">Nuevo Presupuesto</span>
                    </Link>
                    <Link :href="route('aprobaciones.index')" class="flex flex-col items-center p-4 rounded-lg bg-yellow-50 hover:bg-yellow-100 transition-colors">
                        <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium text-yellow-700">Aprobaciones</span>
                    </Link>
                    <Link :href="route('reportes.index')" class="flex flex-col items-center p-4 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors">
                        <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="text-sm font-medium text-purple-700">Reportes</span>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
