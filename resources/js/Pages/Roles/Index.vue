<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useConfirm } from '@/composables/useConfirm';

defineProps({ roles: Object, filters: Object });

const { confirmDelete } = useConfirm();

async function destroy(id) {
    if (await confirmDelete('El rol será eliminado permanentemente.')) {
        router.delete(route('roles.destroy', id));
    }
}
</script>

<template>
    <Head title="Roles" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
                <p class="text-sm text-gray-500">Gestión de roles del sistema</p>
            </div>
            <Link :href="route('roles.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuevo Rol
            </Link>
        </div>

        <div class="card overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <SearchInput
                        :modelValue="filters?.search"
                        placeholder="Buscar por nombre..."
                        routeName="roles.index"
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Usuarios</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ role.nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">{{ role.descripcion }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                :class="role.estado === 'ACTIVO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                class="px-2 py-1 text-xs rounded-full font-medium"
                            >
                                {{ role.estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">{{ role.usuarios_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('roles.edit', role.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </Link>
                                <button @click="destroy(role.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!roles.data?.length">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">No hay roles registrados</td>
                    </tr>
                </tbody>
            </table>
            </div>

            <Pagination :data="roles" />
        </div>
    </AppLayout>
</template>
