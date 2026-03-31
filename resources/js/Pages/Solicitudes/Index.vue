<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps({
    solicitudes: Object,
    gestiones: Array,
    filters: Object,
});

const { confirmDelete } = useConfirm();

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const search = ref(props.filters?.search || '');
const estado = ref(props.filters?.estado || '');
const prioridad = ref(props.filters?.prioridad || '');
const gestion_id = ref(props.filters?.gestion_id || '');

let debounce = null;
watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => applyFilters(), 300);
});

function applyFilters() {
    router.get(route('solicitudes.index'), {
        search: search.value || undefined,
        estado: estado.value || undefined,
        prioridad: prioridad.value || undefined,
        gestion_id: gestion_id.value || undefined,
    }, { preserveState: true, replace: true });
}

async function destroy(id) {
    if (await confirmDelete('La solicitud de gasto será eliminada permanentemente.')) {
        router.delete(route('solicitudes.destroy', id));
    }
}

const prioridadClasses = {
    BAJA: 'bg-gray-100 text-gray-700',
    MEDIA: 'bg-blue-100 text-blue-700',
    ALTA: 'bg-yellow-100 text-yellow-700',
    URGENTE: 'bg-red-100 text-red-700',
};

const estadoClasses = {
    BORRADOR: 'bg-gray-100 text-gray-700',
    PENDIENTE: 'bg-yellow-100 text-yellow-700',
    EN_REVISION: 'bg-blue-100 text-blue-700',
    APROBADA: 'bg-green-100 text-green-700',
    RECHAZADA: 'bg-red-100 text-red-700',
    EJECUTADA: 'bg-purple-100 text-purple-700',
    ANULADA: 'bg-gray-100 text-gray-600',
};

const estadoLabels = {
    BORRADOR: 'Borrador',
    PENDIENTE: 'Pendiente',
    EN_REVISION: 'En Revisión',
    APROBADA: 'Aprobada',
    RECHAZADA: 'Rechazada',
    EJECUTADA: 'Ejecutada',
    ANULADA: 'Anulada',
};
</script>

<template>
    <Head title="Solicitudes de Gasto" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Solicitudes de Gasto</h1>
                <p class="text-sm text-gray-500">Gestión de solicitudes y requerimientos de gastos</p>
            </div>
            <Link :href="route('solicitudes.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nueva Solicitud
            </Link>
        </div>

        <div class="card overflow-hidden">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200 grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por código o concepto..."
                    class="input-field text-sm"
                />
                <select v-model="estado" @change="applyFilters" class="input-field text-sm">
                    <option value="">Todos los estados</option>
                    <option value="BORRADOR">Borrador</option>
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="EN_REVISION">En Revisión</option>
                    <option value="APROBADA">Aprobada</option>
                    <option value="RECHAZADA">Rechazada</option>
                    <option value="EJECUTADA">Ejecutada</option>
                    <option value="ANULADA">Anulada</option>
                </select>
                <select v-model="prioridad" @change="applyFilters" class="input-field text-sm">
                    <option value="">Todas las prioridades</option>
                    <option value="BAJA">Baja</option>
                    <option value="MEDIA">Media</option>
                    <option value="ALTA">Alta</option>
                    <option value="URGENTE">Urgente</option>
                </select>
                <select v-model="gestion_id" @change="applyFilters" class="input-field text-sm">
                    <option value="">Todas las gestiones</option>
                    <option v-for="g in gestiones" :key="g.id" :value="g.id">{{ g.nombre }} ({{ g.anio }})</option>
                </select>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="s in solicitudes.data" :key="s.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono font-bold text-blue-700">{{ s.codigo }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ s.concepto }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ s.centro_costo?.area?.nombre ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ s.solicitante?.nombres }} {{ s.solicitante?.apellidos }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-medium" :class="prioridadClasses[s.prioridad] || 'bg-gray-100 text-gray-600'">
                                    {{ s.prioridad }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-medium" :class="estadoClasses[s.estado] || 'bg-gray-100 text-gray-600'">
                                    {{ estadoLabels[s.estado] || s.estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-400">{{ s.created_at?.split('T')[0] }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Link :href="route('solicitudes.show', s.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </Link>
                                    <Link v-if="s.estado === 'BORRADOR'" :href="route('solicitudes.edit', s.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editar
                                    </Link>
                                    <button
                                        v-if="s.estado === 'BORRADOR' || s.estado === 'ANULADA'"
                                        @click="destroy(s.id)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!solicitudes.data?.length">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">No hay solicitudes registradas</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :data="solicitudes" />
        </div>
    </AppLayout>
</template>
