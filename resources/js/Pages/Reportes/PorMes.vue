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
    router.get(route('reportes.por-mes'), { gestion_id: selectedGestion.value }, { preserveState: true });
}

const totalPresupuestado = computed(() => props.data.reduce((s, r) => s + Number(r.presupuestado || 0), 0));
const maxPresupuestado = computed(() => Math.max(...props.data.map(r => Number(r.presupuestado || 0)), 1));
const promedioMensual = computed(() => props.data.length ? totalPresupuestado.value / props.data.length : 0);

function barWidth(val) {
    return ((Number(val || 0) / maxPresupuestado.value) * 100).toFixed(1);
}

function participacion(val) {
    if (!totalPresupuestado.value) return '0.0';
    return ((Number(val || 0) / totalPresupuestado.value) * 100).toFixed(1);
}

const barColors = [
    'from-blue-400 to-blue-600', 'from-emerald-400 to-emerald-600', 'from-amber-400 to-amber-600',
    'from-violet-400 to-violet-600', 'from-pink-400 to-pink-600', 'from-cyan-400 to-cyan-600',
    'from-red-400 to-red-600', 'from-indigo-400 to-indigo-600', 'from-teal-400 to-teal-600',
    'from-orange-400 to-orange-600', 'from-fuchsia-400 to-fuchsia-600', 'from-sky-400 to-sky-600',
];

function getBarColor(index) {
    return barColors[index % barColors.length];
}
</script>

<template>
    <Head title="Reporte por Mes" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reporte por Mes</h1>
                        <p class="text-sm text-gray-500">Distribución mensual del presupuesto institucional</p>
                    </div>
                </div>
                <Link :href="route('reportes.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Anual</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ formatMoney(totalPresupuestado) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Promedio Mensual</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ formatMoney(promedioMensual) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Meses con Datos</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ data.length }} <span class="text-sm font-normal text-gray-500">de 12</span></p>
                        </div>
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gestión Fiscal</label>
                        <select v-model="selectedGestion" @change="filtrar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                            <option value="">-- Todas las gestiones --</option>
                            <option v-for="g in gestiones" :key="g.id" :value="g.id">Gestión {{ g.anio }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Visual Chart -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Distribución Mensual</h3>
                </div>
                <div class="p-5">
                    <div v-if="data.length > 0" class="space-y-3">
                        <div v-for="(row, index) in data" :key="row.mes" class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-600 w-24 text-right flex-shrink-0">{{ row.nombre }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden relative">
                                <div
                                    :class="['h-full rounded-full bg-gradient-to-r transition-all duration-700 flex items-center', getBarColor(index)]"
                                    :style="{ width: barWidth(row.presupuestado) + '%' }"
                                >
                                    <span v-if="Number(barWidth(row.presupuestado)) > 20" class="text-xs text-white font-bold ml-3">
                                        {{ formatMoney(row.presupuestado) }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-gray-500 w-12 text-right flex-shrink-0">{{ participacion(row.presupuestado) }}%</span>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center">
                        <p class="text-gray-400">No hay datos para graficar</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-20">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mes</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Presupuestado</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">% del Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(row, index) in data" :key="row.mes" class="hover:bg-amber-50/40 transition-colors">
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-xs font-bold text-gray-600">{{ row.mes }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div :class="['w-2.5 h-2.5 rounded-full bg-gradient-to-r flex-shrink-0', getBarColor(index)]"></div>
                                        <span class="text-sm font-medium text-gray-800">{{ row.nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold text-gray-800">{{ formatMoney(row.presupuestado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-medium text-gray-500">{{ participacion(row.presupuestado) }}%</td>
                            </tr>
                            <tr v-if="data.length === 0">
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-gray-500 font-medium">No hay datos disponibles</p>
                                    <p class="text-gray-400 text-sm mt-1">Seleccione una gestión fiscal diferente</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="data.length > 0">
                            <tr class="bg-gray-800 text-white">
                                <td class="px-5 py-3.5"></td>
                                <td class="px-5 py-3.5 text-sm font-bold">TOTAL ANUAL</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totalPresupuestado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold text-brand-gold">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
