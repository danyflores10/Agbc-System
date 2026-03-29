<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';

const props = defineProps({
    usuarios: Object,
    filters: Object,
});

const estadoFilter = ref(props.filters?.estado || '');

watch(estadoFilter, (value) => {
    router.get(route('usuarios.index'), {
        search: props.filters?.search || undefined,
        estado: value || undefined,
    }, { preserveState: true, replace: true });
});

function destroy(id) {
    if (confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        router.delete(route('usuarios.destroy', id));
    }
}

const estadoBadge = (estado) => {
    const map = {
        ACTIVO: 'bg-green-100 text-green-800',
        INACTIVO: 'bg-red-100 text-red-800',
        BLOQUEADO: 'bg-gray-100 text-gray-800',
    };
    return map[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Usuarios" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
                <p class="text-sm text-gray-500">Gestión de usuarios del sistema</p>
            </div>
            <Link :href="route('usuarios.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Nuevo Usuario
            </Link>
        </div>

        <div class="card overflow-hidden">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <SearchInput
                        :modelValue="filters?.search"
                        placeholder="Buscar por nombre o email..."
                        route-name="usuarios.index"
                        :filters="{ estado: estadoFilter || undefined }"
                    />
                </div>
                <select v-model="estadoFilter" class="input-field sm:w-48">
                    <option value="">Todos los estados</option>
                    <option value="ACTIVO">Activo</option>
                    <option value="INACTIVO">Inactivo</option>
                    <option value="BLOQUEADO">Bloqueado</option>
                </select>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="u in usuarios.data" :key="u.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center text-sm font-bold shrink-0">
                                        {{ u.nombres?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ u.nombres }} {{ u.apellidos }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ u.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ u.roles?.map(r => r.nombre).join(', ') || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ u.cargo || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span :class="['inline-flex px-2 py-0.5 rounded-full text-xs font-semibold', estadoBadge(u.estado)]">
                                    {{ u.estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('usuarios.edit', u.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editar
                                    </Link>
                                    <button @click="destroy(u.id)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!usuarios.data?.length">
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <Pagination :data="usuarios" />
        </div>
    </AppLayout>
</template>
