<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ gestion: Object });

const estadoClasses = {
    PLANIFICACION: 'bg-yellow-100 text-yellow-800',
    ABIERTA: 'bg-green-100 text-green-800',
    CERRADA: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head :title="`Gestión ${gestion.anio}`" />
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestión {{ gestion.anio }}</h1>
                    <p class="text-sm text-gray-500">Detalle de la gestión fiscal</p>
                </div>
                <div class="flex items-center space-x-2">
                    <Link :href="route('gestiones.edit', gestion.id)" class="btn-secondary">Editar</Link>
                    <Link :href="route('gestiones.index')" class="btn-secondary">← Volver</Link>
                </div>
            </div>

            <!-- Datos generales -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Año</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ gestion.anio }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Fecha Inicio</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ gestion.fecha_inicio }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Fecha Fin</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ gestion.fecha_fin }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="estadoClasses[gestion.estado] || 'bg-gray-100 text-gray-800'"
                            >
                                {{ gestion.estado }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Periodos Fiscales -->
            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Periodos Fiscales</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Abierto</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="p in gestion.periodos" :key="p.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ p.mes }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ p.nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ p.fecha_inicio }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ p.fecha_fin }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="p.abierto ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ p.abierto ? 'Sí' : 'No' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!gestion.periodos?.length">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No hay periodos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Presupuestos -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Presupuestos</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Versión</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="p in gestion.presupuestos" :key="p.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ p.nombre }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ p.version }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="estadoClasses[p.estado] || 'bg-gray-100 text-gray-800'"
                                >
                                    {{ p.estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ p.created_at }}</td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('presupuestos.show', p.id)" class="text-blue-600 hover:text-blue-800 text-sm">Ver</Link>
                            </td>
                        </tr>
                        <tr v-if="!gestion.presupuestos?.length">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No hay presupuestos vinculados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
