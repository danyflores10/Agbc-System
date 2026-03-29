<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    usuario: { type: Object, default: null },
    roles: Array,
    centrosCosto: Array,
    rolesSeleccionados: { type: Array, default: () => [] },
    centrosCostoSeleccionados: { type: Array, default: () => [] },
    centroCostoPrincipal: { type: [Number, String], default: null },
});

const isEdit = computed(() => !!props.usuario);

const form = useForm({
    nombres: props.usuario?.nombres || '',
    apellidos: props.usuario?.apellidos || '',
    email: props.usuario?.email || '',
    password: '',
    password_confirmation: '',
    telefono: props.usuario?.telefono || '',
    cargo: props.usuario?.cargo || '',
    estado: props.usuario?.estado || 'ACTIVO',
    roles: props.rolesSeleccionados.map(r => r.id ?? r),
    centros_costo: props.centrosCostoSeleccionados.map(c => c.id ?? c),
    centro_costo_principal: props.centroCostoPrincipal,
});

function submit() {
    if (isEdit.value) {
        form.put(route('usuarios.update', props.usuario.id));
    } else {
        form.post(route('usuarios.store'));
    }
}

function toggleCentroCosto(id) {
    const idx = form.centros_costo.indexOf(id);
    if (idx === -1) {
        form.centros_costo.push(id);
    } else {
        form.centros_costo.splice(idx, 1);
        if (form.centro_costo_principal === id) {
            form.centro_costo_principal = null;
        }
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Usuario' : 'Nuevo Usuario'" />
    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
                <Link :href="route('usuarios.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-6">
                <!-- Datos personales -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Datos personales</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                            <input v-model="form.nombres" type="text" class="input-field" maxlength="120" required />
                            <p v-if="form.errors.nombres" class="text-red-500 text-sm mt-1">{{ form.errors.nombres }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                            <input v-model="form.apellidos" type="text" class="input-field" maxlength="120" required />
                            <p v-if="form.errors.apellidos" class="text-red-500 text-sm mt-1">{{ form.errors.apellidos }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="input-field" maxlength="150" required />
                            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input v-model="form.telefono" type="text" class="input-field" maxlength="30" />
                            <p v-if="form.errors.telefono" class="text-red-500 text-sm mt-1">{{ form.errors.telefono }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input v-model="form.cargo" type="text" class="input-field" maxlength="100" />
                            <p v-if="form.errors.cargo" class="text-red-500 text-sm mt-1">{{ form.errors.cargo }}</p>
                        </div>
                    </div>
                </fieldset>

                <!-- Credenciales -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Credenciales</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña
                                <span v-if="isEdit" class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                            </label>
                            <input v-model="form.password" type="password" class="input-field" :required="!isEdit" />
                            <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                            <input v-model="form.password_confirmation" type="password" class="input-field" :required="!isEdit" />
                        </div>
                    </div>
                </fieldset>

                <!-- Estado -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Estado</legend>
                    <select v-model="form.estado" class="input-field sm:w-64">
                        <option value="ACTIVO">Activo</option>
                        <option value="INACTIVO">Inactivo</option>
                        <option value="BLOQUEADO">Bloqueado</option>
                    </select>
                    <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                </fieldset>

                <!-- Roles -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Roles</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        <label v-for="rol in roles" :key="rol.id" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" :value="rol.id" v-model="form.roles" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-sm text-gray-700">{{ rol.nombre }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</p>
                </fieldset>

                <!-- Centros de Costo -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Centros de Costo</legend>
                    <p class="text-sm text-gray-500 mb-3">Seleccione los centros de costo y marque uno como principal.</p>
                    <div class="space-y-2">
                        <div v-for="cc in centrosCosto" :key="cc.id"
                            class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                            <label class="flex items-center space-x-2 cursor-pointer flex-1">
                                <input type="checkbox" :checked="form.centros_costo.includes(cc.id)"
                                    @change="toggleCentroCosto(cc.id)"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-gray-700">
                                    {{ cc.codigo }} - {{ cc.nombre }}
                                    <span v-if="cc.area" class="text-gray-400">({{ cc.area.nombre }})</span>
                                </span>
                            </label>
                            <label v-if="form.centros_costo.includes(cc.id)" class="flex items-center space-x-1 ml-4 cursor-pointer">
                                <input type="radio" :value="cc.id" v-model="form.centro_costo_principal"
                                    class="border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-medium text-gray-500">Principal</span>
                            </label>
                        </div>
                    </div>
                    <p v-if="form.errors.centros_costo" class="text-red-500 text-sm mt-1">{{ form.errors.centros_costo }}</p>
                    <p v-if="form.errors.centro_costo_principal" class="text-red-500 text-sm mt-1">{{ form.errors.centro_costo_principal }}</p>
                </fieldset>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <Link :href="route('usuarios.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Usuario') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
