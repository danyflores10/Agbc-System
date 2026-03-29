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
    router.get(route('reportes.por-sucursal'), { gestion_id: selectedGestion.value }, { preserveState: true });
}

const totalVigente = computed(() => props.data.reduce((s, r) => s + Number(r.monto_vigente || 0), 0));
const maxVigente = computed(() => Math.max(...props.data.map(r => Number(r.monto_vigente || 0)), 1));

function barWidth(val) {
    return ((Number(val || 0) / maxVigente.value) * 100).toFixed(1);
}

function participacion(val) {
    if (!totalVigente.value) return '0.0';
    return ((Number(val || 0) / totalVigente.value) * 100).toFixed(1);
}

const departamentos = computed(() => {
    const deps = {};
    props.data.forEach(r => {
        const dep = r.departamento || 'Sin Departamento';
        if (!deps[dep]) deps[dep] = 0;
        deps[dep] += Number(r.monto_vigente || 0);
    });
    return deps;
});

const deptColors = {
    'La Paz': 'bg-blue-500', 'Cochabamba': 'bg-emerald-500', 'Santa Cruz': 'bg-amber-500',
    'Oruro': 'bg-violet-500', 'Potosí': 'bg-red-500', 'Tarija': 'bg-cyan-500',
    'Sucre': 'bg-pink-500', 'Chuquisaca': 'bg-pink-500', 'Beni': 'bg-orange-500', 'Pando': 'bg-teal-500',
};

function getDeptColor(dep) {
    return deptColors[dep] || 'bg-gray-500';
}
</script>

<template>
    <Head title="Reporte por Sucursal" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reporte por Sucursal</h1>
                        <p class="text-sm text-gray-500">Distribución presupuestaria por sucursales a nivel nacional</p>
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
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Presupuestado</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ formatMoney(totalVigente) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sucursales</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ data.length }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Departamentos</p>
                            <p class="text-xl font-bold text-gray-800 mt-1">{{ Object.keys(departamentos).length }}</p>
                        </div>
                        <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gestión Fiscal</label>
                        <select v-model="selectedGestion" @change="filtrar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <option value="">-- Todas las gestiones --</option>
                            <option v-for="g in gestiones" :key="g.id" :value="g.id">Gestión {{ g.anio }}</option>
                        </select>
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
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sucursal</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Departamento</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Monto Vigente</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider" style="min-width: 200px">Participación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="row in data" :key="row.id" class="hover:bg-emerald-50/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-gray-100 text-xs font-mono font-semibold text-gray-700">{{ row.codigo }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-sm font-medium text-gray-800">{{ row.nombre }}</td>
                                <td class="px-5 py-3.5">
                                    <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white', getDeptColor(row.departamento)]">
                                        {{ row.departamento }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-right font-medium text-gray-700">{{ formatMoney(row.monto_vigente) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" :style="{ width: barWidth(row.monto_vigente) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-600 w-14 text-right">{{ participacion(row.monto_vigente) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="data.length === 0">
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <p class="text-gray-500 font-medium">No hay datos disponibles</p>
                                    <p class="text-gray-400 text-sm mt-1">Seleccione una gestión fiscal diferente</p>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="data.length > 0">
                            <tr class="bg-gray-800 text-white">
                                <td colspan="3" class="px-5 py-3.5 text-sm font-bold">TOTAL GENERAL</td>
                                <td class="px-5 py-3.5 text-sm text-right font-bold">{{ formatMoney(totalVigente) }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="text-xs font-bold text-brand-gold">100%</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
