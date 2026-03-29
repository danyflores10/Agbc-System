<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';

defineProps({ proveedores: Object, filters: Object });

function destroy(id) {
    if (confirm('¿Está seguro de eliminar este proveedor?')) {
        router.delete(route('proveedores.destroy', id));
    }
}
</script>

<template>
    <Head title="Proveedores" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Proveedores</h1>
                <p class="text-sm text-gray-500">Gestión de proveedores</p>
            </div>
            <Link :href="route('proveedores.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Proveedor
            </Link>
        </div>

        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <SearchInput
                        :modelValue="filters?.search"
                        placeholder="Buscar por razón social o NIT..."
                        routeName="proveedores.index"
                        :filters="{ tipo: filters?.tipo }"
                    />
                </div>
                <select
                    class="input-field w-auto"
                    :value="filters?.tipo || ''"
                    @change="router.get(route('proveedores.index'), { search: filters?.search, tipo: $event.target.value || undefined }, { preserveState: true, replace: true })"
                >
                    <option value="">Todos los tipos</option>
                    <option value="NATURAL">Natural</option>
                    <option value="JURIDICA">Jurídica</option>
                </select>
            </div>

            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Razón Social</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIT</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="proveedor in proveedores.data" :key="proveedor.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ proveedor.razon_social }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ proveedor.nit }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                :class="proveedor.tipo_persona === 'NATURAL' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                class="px-2 py-1 text-xs rounded-full font-medium"
                            >
                                {{ proveedor.tipo_persona }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ proveedor.telefono }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ proveedor.email }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                :class="proveedor.estado === 'ACTIVO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                class="px-2 py-1 text-xs rounded-full font-medium"
                            >
                                {{ proveedor.estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('proveedores.edit', proveedor.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </Link>
                                <button @click="destroy(proveedor.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!proveedores.data?.length">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay proveedores registrados</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <Pagination :data="proveedores" />
        </div>
    </AppLayout>
</template>
