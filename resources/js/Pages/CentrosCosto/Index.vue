<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useConfirm } from '@/composables/useConfirm';

defineProps({ centros: Object, areas: Array, filters: Object });

const { confirmDelete } = useConfirm();

const tipoBadge = {
    ADMINISTRATIVO: 'bg-blue-100 text-blue-800',
    OPERATIVO: 'bg-green-100 text-green-800',
    LOGISTICO: 'bg-yellow-100 text-yellow-800',
    FINANCIERO: 'bg-purple-100 text-purple-800',
};

function applyFilter(filters, key, value) {
    const params = { ...filters, [key]: value || undefined };
    router.get(route('centros-costo.index'), params, { preserveState: true, replace: true });
}

async function destroy(id) {
    if (await confirmDelete('El centro de costo será eliminado permanentemente.')) {
        router.delete(route('centros-costo.destroy', id));
    }
}
</script>

<template>
    <Head title="Centros de Costo" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Centros de Costo</h1>
                <p class="text-sm text-gray-500">Gestión de centros de costo organizacionales</p>
            </div>
            <Link :href="route('centros-costo.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Centro de Costo
            </Link>
        </div>

        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <SearchInput
                        :modelValue="filters?.search"
                        placeholder="Buscar por nombre o código..."
                        routeName="centros-costo.index"
                        :filters="{ area_id: filters?.area_id, tipo: filters?.tipo }"
                    />
                </div>
                <select
                    class="input-field w-auto"
                    :value="filters?.area_id || ''"
                    @change="applyFilter(filters, 'area_id', $event.target.value)"
                >
                    <option value="">Todas las áreas</option>
                    <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.nombre }}</option>
                </select>
                <select
                    class="input-field w-auto"
                    :value="filters?.tipo || ''"
                    @change="applyFilter(filters, 'tipo', $event.target.value)"
                >
                    <option value="">Todos los tipos</option>
                    <option value="ADMINISTRATIVO">Administrativo</option>
                    <option value="OPERATIVO">Operativo</option>
                    <option value="LOGISTICO">Logístico</option>
                    <option value="FINANCIERO">Financiero</option>
                </select>
            </div>

            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sucursal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="centro in centros.data" :key="centro.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono font-bold text-brand-blue">{{ centro.codigo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div>{{ centro.nombre }}</div>
                            <div v-if="centro.descripcion" class="text-xs text-gray-400 truncate max-w-xs">{{ centro.descripcion }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ centro.area?.nombre ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ centro.sucursal?.nombre ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                :class="tipoBadge[centro.tipo] || 'bg-gray-100 text-gray-800'"
                                class="px-2 py-1 text-xs rounded-full font-medium"
                            >
                                {{ centro.tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                :class="centro.estado === 'ACTIVO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                class="px-2 py-1 text-xs rounded-full font-medium"
                            >
                                {{ centro.estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('centros-costo.edit', centro.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </Link>
                                <button @click="destroy(centro.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!centros.data?.length">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay centros de costo registrados</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <Pagination :data="centros" />
        </div>
    </AppLayout>
</template>
