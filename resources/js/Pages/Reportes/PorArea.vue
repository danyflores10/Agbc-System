<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    data: Array,
    gestiones: Array,
    gestionId: [Number, String],
});

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const selectedGestion = ref(props.gestionId || '');

function filtrar() {
    router.get(route('reportes.por-area'), { gestion_id: selectedGestion.value }, { preserveState: true });
}

const totales = computed(() => ({
    vigente: props.data.reduce((s, r) => s + Number(r.monto_vigente || 0), 0),
    ejecutado: props.data.reduce((s, r) => s + Number(r.monto_ejecutado || 0), 0),
}));

const porcentajeGlobal = computed(() => {
    if (!totales.value.vigente) return 0;
    return ((totales.value.ejecutado / totales.value.vigente) * 100).toFixed(1);
});

function porcentaje(ejecutado, vigente) {
    if (!vigente || vigente == 0) return 0;
    return ((ejecutado / vigente) * 100).toFixed(1);
}

function getColorClass(pct) {
    if (pct >= 90) return 'text-red-600';
    if (pct >= 70) return 'text-amber-600';
    return 'text-emerald-600';
}

function getBarColor(pct) {
    if (pct >= 90) return 'bg-red-500';
    if (pct >= 70) return 'bg-amber-500';
    return 'bg-emerald-500';
}
</script>

<template>
    <Head title="Reporte por Área" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reporte por Área</h1>
                        <p class="text-sm text-gray-500">Ejecución presupuestaria por áreas organizacionales</p>
                    </div>
                </div>
                <Link :href="route('reportes.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Vigente</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ formatMoney(totales.vigente) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Ejecutado</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ formatMoney(totales.ejecutado) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Disponible</p>
                            <p class="text-xl font-bold mt-1" :class="(totales.vigente - totales.ejecutado) < 0 ? 'text-red-600' : 'text-gray-800'">{{ formatMoney(totales.vigente - totales.ejecutado) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ejecución Global</p>
                            <p class="text-xl font-bold mt-1" :class="getColorClass(porcentajeGlobal)">{{ porcentajeGlobal }}%</p>
                        </div>
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gestión Fiscal</label>
                        <select v-model="selectedGestion" @change="filtrar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Todas las gestiones --</option>
                            <option v-for="g in gestiones" :key="g.id" :value="g.id">Gestión {{ g.anio }}</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium text-gray-700">{{ data.length }}</span> áreas encontradas
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Código</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Área</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Monto Vigente</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Ejecutado</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 180px">% Ejecución</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="row in data" :key="row.id" class="hover:bg-blue-50/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-gray-100 text-xs font-mono font-semibold text-gray-700">{{ row.codigo }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-sm font-medium text-gray-800">{{ row.nombre }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-medium text-gray-700">{{ formatMoney(row.monto_vigente) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-medium text-gray-700">{{ formatMoney(row.monto_ejecutado) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                            <div :class="['h-full rounded-full transition-all duration-500', getBarColor(porcentaje(row.monto_ejecutado, row.monto_vigente))]" :style="{ width: Math.min(porcentaje(row.monto_ejecutado, row.monto_vigente), 100) + '%' }"></div>
                                        </div>
                                        <span :class="['text-xs font-bold w-14 text-right', getColorClass(porcentaje(row.monto_ejecutado, row.monto_vigente))]">{{ porcentaje(row.monto_ejecutado, row.monto_vigente) }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold" :class="(row.monto_vigente - row.monto_ejecutado) < 0 ? 'text-red-600' : 'text-emerald-600'">
                                    {{ formatMoney(row.monto_vigente - row.monto_ejecutado) }}
                                </td>
                            </tr>
                            <tr v-if="data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-gray-500 font-medium">No hay datos disponibles</p>
                                    <p class="text-gray-400 text-sm mt-1">Seleccione una gestión fiscal diferente</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="data.length > 0">
                            <tr class="bg-gray-800 text-white">
                                <td colspan="2" class="px-5 py-3.5 text-sm font-bold">TOTAL GENERAL</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.vigente) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.ejecutado) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-600 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-full rounded-full bg-brand-gold transition-all duration-500" :style="{ width: Math.min(porcentajeGlobal, 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold w-14 text-right text-brand-gold">{{ porcentajeGlobal }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.vigente - totales.ejecutado) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
