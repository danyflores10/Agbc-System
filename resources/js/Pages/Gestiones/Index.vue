<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useConfirm } from '@/composables/useConfirm';

defineProps({ gestiones: Object, filters: Object });

const { confirmDelete } = useConfirm();

async function destroy(id) {
    if (await confirmDelete('La gestión fiscal será eliminada permanentemente.')) {
        router.delete(route('gestiones.destroy', id));
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

const estadoClasses = {
    PLANIFICACION: 'bg-yellow-100 text-yellow-800',
    ABIERTA: 'bg-green-100 text-green-800',
    CERRADA: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head title="Gestiones" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestiones Fiscales</h1>
                <p class="text-sm text-gray-500">Administración de períodos presupuestarios</p>
            </div>
            <Link :href="route('gestiones.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nueva Gestión
            </Link>
        </div>

        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <SearchInput
                    :model-value="filters?.search"
                    route-name="gestiones.index"
                    placeholder="Buscar por año..."
                />
            </div>

            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Año</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Periodos</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Presupuestos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="g in gestiones.data" :key="g.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ g.anio }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(g.fecha_inicio) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(g.fecha_fin) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="estadoClasses[g.estado] || 'bg-gray-100 text-gray-800'"
                            >
                                {{ g.estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center text-gray-700">{{ g.periodos_count }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-700">{{ g.presupuestos_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('gestiones.show', g.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver
                                </Link>
                                <Link :href="route('gestiones.edit', g.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </Link>
                                <button @click="destroy(g.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!gestiones.data?.length">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay gestiones registradas</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <Pagination :data="gestiones" />
        </div>
    </AppLayout>
</template>
