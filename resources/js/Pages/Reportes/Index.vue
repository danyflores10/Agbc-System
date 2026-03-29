<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, Head } from '@inertiajs/vue3';

const reports = [
    {
        title: 'Reporte por Área',
        description: 'Análisis de ejecución presupuestaria agrupado por áreas organizacionales',
        route: 'reportes.por-area',
        color: 'blue',
        gradient: 'from-blue-500 to-blue-700',
        bg: 'bg-blue-50',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />`,
    },
    {
        title: 'Reporte por Sucursal',
        description: 'Distribución presupuestaria por sucursales y departamentos a nivel nacional',
        route: 'reportes.por-sucursal',
        color: 'emerald',
        gradient: 'from-emerald-500 to-emerald-700',
        bg: 'bg-emerald-50',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`,
    },
    {
        title: 'Comparativo Presupuestario',
        description: 'Contraste entre montos planificados, ejecutados, comprometidos y disponibles',
        route: 'reportes.comparativo',
        color: 'violet',
        gradient: 'from-violet-500 to-violet-700',
        bg: 'bg-violet-50',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />`,
    },
    {
        title: 'Reporte por Mes',
        description: 'Distribución mensual del presupuesto con visualización gráfica de tendencias',
        route: 'reportes.por-mes',
        color: 'amber',
        gradient: 'from-amber-500 to-amber-700',
        bg: 'bg-amber-50',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />`,
    },
];
</script>

<template>
    <Head title="Reportes" />
    <AppLayout>
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-blue to-blue-800 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Centro de Reportes</h1>
                        <p class="text-sm text-gray-500">Análisis y estadísticas del presupuesto institucional</p>
                    </div>
                </div>
            </div>

            <!-- Report Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <Link
                    v-for="report in reports"
                    :key="report.route"
                    :href="route(report.route)"
                    class="group relative bg-white rounded-2xl border border-gray-200 hover:border-gray-300 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
                >
                    <!-- Top gradient bar -->
                    <div :class="['h-1.5 bg-gradient-to-r', report.gradient]"></div>

                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Icon -->
                            <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110', report.bg]">
                                <svg :class="['w-7 h-7 text-' + report.color + '-600']" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="report.icon"></svg>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-brand-blue transition-colors">{{ report.title }}</h3>
                                <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ report.description }}</p>
                            </div>
                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-brand-blue group-hover:translate-x-1 transition-all flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
