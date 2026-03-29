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
    router.get(route('reportes.comparativo'), { gestion_id: selectedGestion.value }, { preserveState: true });
}

const totales = computed(() => ({
    planificado: props.data.reduce((s, r) => s + Number(r.planificado || 0), 0),
    ejecutado: props.data.reduce((s, r) => s + Number(r.ejecutado || 0), 0),
    comprometido: props.data.reduce((s, r) => s + Number(r.comprometido || 0), 0),
    disponible: props.data.reduce((s, r) => s + Number(r.disponible || 0), 0),
}));

const porcentajeEjecucion = computed(() => {
    if (!totales.value.planificado) return 0;
    return ((totales.value.ejecutado / totales.value.planificado) * 100).toFixed(1);
});

function pctEjecucion(row) {
    const plan = Number(row.planificado || 0);
    if (!plan) return 0;
    return ((Number(row.ejecutado || 0) / plan) * 100).toFixed(1);
}

function getBarColor(pct) {
    if (pct >= 90) return 'bg-red-500';
    if (pct >= 70) return 'bg-amber-500';
    return 'bg-blue-500';
}
</script>

<template>
    <Head title="Comparativo Presupuesto" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Comparativo Presupuestario</h1>
                        <p class="text-sm text-gray-500">Planificado vs. Ejecutado vs. Comprometido por partida</p>
                    </div>
                </div>
                <Link :href="route('reportes.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver
                </Link>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Planificado</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ formatMoney(totales.planificado) }}</p>
                    <div class="mt-2 h-1 bg-blue-500 rounded-full"></div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ejecutado</p>
                    <p class="text-lg font-bold text-emerald-600 mt-1">{{ formatMoney(totales.ejecutado) }}</p>
                    <div class="mt-2 h-1 bg-emerald-500 rounded-full"></div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Comprometido</p>
                    <p class="text-lg font-bold text-amber-600 mt-1">{{ formatMoney(totales.comprometido) }}</p>
                    <div class="mt-2 h-1 bg-amber-500 rounded-full"></div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Disponible</p>
                    <p class="text-lg font-bold mt-1" :class="totales.disponible < 0 ? 'text-red-600' : 'text-gray-800'">{{ formatMoney(totales.disponible) }}</p>
                    <div class="mt-2 h-1 rounded-full" :class="totales.disponible < 0 ? 'bg-red-500' : 'bg-gray-400'"></div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">% Ejecución</p>
                    <p class="text-lg font-bold text-violet-600 mt-1">{{ porcentajeEjecucion }}%</p>
                    <div class="mt-2 h-1 bg-violet-500 rounded-full"></div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gestión Fiscal</label>
                        <select v-model="selectedGestion" @change="filtrar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm">
                            <option value="">-- Todas las gestiones --</option>
                            <option v-for="g in gestiones" :key="g.id" :value="g.id">Gestión {{ g.anio }}</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium text-gray-700">{{ data.length }}</span> partidas encontradas
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
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Partida</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Planificado</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Ejecutado</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Comprometido</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 160px">% Ejec.</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="row in data" :key="row.partida_codigo" class="hover:bg-violet-50/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-gray-100 text-xs font-mono font-semibold text-gray-700">{{ row.partida_codigo }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-sm font-medium text-gray-800">{{ row.partida_nombre }}</td>
                                <td class="px-5 py-3.5 text-sm text-right text-gray-700">{{ formatMoney(row.planificado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-medium text-emerald-700">{{ formatMoney(row.ejecutado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right text-amber-700">{{ formatMoney(row.comprometido) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div :class="['h-full rounded-full transition-all duration-500', getBarColor(pctEjecucion(row))]" :style="{ width: Math.min(pctEjecucion(row), 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-600 w-12 text-right">{{ pctEjecucion(row) }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold" :class="Number(row.disponible) < 0 ? 'text-red-600' : 'text-emerald-600'">
                                    {{ formatMoney(row.disponible) }}
                                </td>
                            </tr>
                            <tr v-if="data.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <p class="text-gray-500 font-medium">No hay datos disponibles</p>
                                    <p class="text-gray-400 text-sm mt-1">Seleccione una gestión fiscal diferente</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="data.length > 0">
                            <tr class="bg-gray-800 text-white">
                                <td colspan="2" class="px-5 py-3.5 text-sm font-bold">TOTAL GENERAL</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.planificado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.ejecutado) }}</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totales.comprometido) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-600 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full bg-brand-gold transition-all duration-500" :style="{ width: Math.min(porcentajeEjecucion, 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-brand-gold w-12 text-right">{{ porcentajeEjecucion }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold" :class="totales.disponible < 0 ? 'text-red-300' : ''">{{ formatMoney(totales.disponible) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
