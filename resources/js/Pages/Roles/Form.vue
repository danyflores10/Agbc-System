<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    role: { type: Object, default: null },
    permisos: Array,
    permisosAgrupados: Object,
    permisosSeleccionados: { type: Array, default: () => [] },
});

const isEdit = !!props.role;

const form = useForm({
    nombre: props.role?.nombre || '',
    descripcion: props.role?.descripcion || '',
    estado: props.role?.estado || 'ACTIVO',
    permisos: isEdit ? [...props.permisosSeleccionados] : [],
});

function togglePermiso(id) {
    const idx = form.permisos.indexOf(id);
    if (idx === -1) {
        form.permisos.push(id);
    } else {
        form.permisos.splice(idx, 1);
    }
}

function toggleModulo(permisosList) {
    const ids = permisosList.map(p => p.id);
    const allSelected = ids.every(id => form.permisos.includes(id));
    if (allSelected) {
        form.permisos = form.permisos.filter(id => !ids.includes(id));
    } else {
        const toAdd = ids.filter(id => !form.permisos.includes(id));
        form.permisos.push(...toAdd);
    }
}

function submit() {
    if (isEdit) {
        form.put(route('roles.update', props.role.id));
    } else {
        form.post(route('roles.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Rol' : 'Nuevo Rol'" />
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Rol' : 'Nuevo Rol' }}</h1>
                <Link :href="route('roles.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="card p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input v-model="form.nombre" type="text" class="input-field" maxlength="80" required placeholder="Nombre del rol" />
                            <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select v-model="form.estado" class="input-field">
                                <option value="ACTIVO">Activo</option>
                                <option value="INACTIVO">Inactivo</option>
                            </select>
                            <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea v-model="form.descripcion" class="input-field" rows="3" placeholder="Descripción del rol"></textarea>
                        <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Permisos</h2>
                    <p v-if="form.errors.permisos" class="text-red-500 text-sm mb-3">{{ form.errors.permisos }}</p>

                    <div class="space-y-4">
                        <div v-for="(permisosList, modulo) in permisosAgrupados" :key="modulo" class="border border-gray-200 rounded-lg p-4">
                            <label class="flex items-center space-x-2 cursor-pointer mb-3">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                                    :checked="permisosList.every(p => form.permisos.includes(p.id))"
                                    @change="toggleModulo(permisosList)"
                                />
                                <span class="text-sm font-semibold text-gray-800 uppercase">{{ modulo }}</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 ml-6">
                                <label v-for="permiso in permisosList" :key="permiso.id" class="flex items-center space-x-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue"
                                        :checked="form.permisos.includes(permiso.id)"
                                        @change="togglePermiso(permiso.id)"
                                    />
                                    <span class="text-sm text-gray-700">{{ permiso.nombre }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <Link :href="route('roles.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Rol') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
