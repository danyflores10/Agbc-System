<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    presupuestos: Object,
    gestiones: Array,
    filters: Object,
});

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const estadoClasses = {
    BORRADOR: 'bg-gray-100 text-gray-700',
    EN_REVISION: 'bg-yellow-100 text-yellow-700',
    APROBADO: 'bg-green-100 text-green-700',
    CERRADO: 'bg-blue-100 text-blue-700',
    ANULADO: 'bg-red-100 text-red-700',
};

function applyFilter(key, value) {
    router.get(route('presupuestos.index'), {
        ...route().params,
        [key]: value || undefined,
    }, { preserveState: true, replace: true });
}

function destroy(id) {
    if (confirm('¿Está seguro de eliminar este presupuesto?')) {
        router.delete(route('presupuestos.destroy', id));
    }
}
</script>

<template>
    <Head title="Presupuestos" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Presupuestos</h1>
                <p class="text-sm text-gray-500">Gestión y control de presupuestos institucionales</p>
            </div>
            <Link :href="route('presupuestos.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nuevo Presupuesto
            </Link>
        </div>

        <div class="card overflow-hidden">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <select @change="applyFilter('gestion_id', $event.target.value)" class="input-field text-sm">
                    <option value="">Todas las gestiones</option>
                    <option v-for="g in gestiones" :key="g.id" :value="g.id" :selected="filters?.gestion_id == g.id">{{ g.anio }}</option>
                </select>
                <select @change="applyFilter('estado', $event.target.value)" class="input-field text-sm">
                    <option value="">Todos los estados</option>
                    <option value="BORRADOR" :selected="filters?.estado === 'BORRADOR'">Borrador</option>
                    <option value="EN_REVISION" :selected="filters?.estado === 'EN_REVISION'">En Revisión</option>
                    <option value="APROBADO" :selected="filters?.estado === 'APROBADO'">Aprobado</option>
                    <option value="CERRADO" :selected="filters?.estado === 'CERRADO'">Cerrado</option>
                    <option value="ANULADO" :selected="filters?.estado === 'ANULADO'">Anulado</option>
                </select>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gestión</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Versión</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Detalles</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado por</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="p in presupuestos.data" :key="p.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ p.nombre }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ p.gestion?.anio }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-500">{{ p.version }}</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-500">{{ p.detalles_count }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="estadoClasses[p.estado] || 'bg-gray-100 text-gray-700'">
                                    {{ p.estado?.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ p.creado_por_user?.nombre_completo || (p.creado_por_user?.nombres + ' ' + p.creado_por_user?.apellidos) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Link :href="route('presupuestos.show', p.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </Link>
                                    <Link :href="route('presupuestos.edit', p.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editar
                                    </Link>
                                    <button @click="destroy(p.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!presupuestos.data?.length">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay presupuestos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :data="presupuestos" />
        </div>
    </AppLayout>
</template>
