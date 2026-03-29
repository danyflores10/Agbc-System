<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    centro: { type: Object, default: null },
    areas: Array,
    sucursales: Array,
});

const isEdit = !!props.centro;

const form = useForm({
    codigo: props.centro?.codigo || '',
    nombre: props.centro?.nombre || '',
    area_id: props.centro?.area_id || '',
    sucursal_id: props.centro?.sucursal_id || '',
    descripcion: props.centro?.descripcion || '',
    tipo: props.centro?.tipo || 'ADMINISTRATIVO',
    estado: props.centro?.estado || 'ACTIVO',
});

function submit() {
    if (isEdit) {
        form.put(route('centros-costo.update', props.centro.id));
    } else {
        form.post(route('centros-costo.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Centro de Costo' : 'Nuevo Centro de Costo'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Centro de Costo' : 'Nuevo Centro de Costo' }}</h1>
                <Link :href="route('centros-costo.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input v-model="form.codigo" type="text" class="input-field" maxlength="30" required placeholder="Ej: CC-001" />
                        <p v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">{{ form.errors.codigo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input v-model="form.nombre" type="text" class="input-field" maxlength="150" required placeholder="Nombre del centro de costo" />
                        <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                        <select v-model="form.area_id" class="input-field" required>
                            <option value="">Seleccione un área</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.nombre }}</option>
                        </select>
                        <p v-if="form.errors.area_id" class="text-red-500 text-sm mt-1">{{ form.errors.area_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal <span class="text-gray-400">(opcional)</span></label>
                        <select v-model="form.sucursal_id" class="input-field">
                            <option value="">Sin sucursal</option>
                            <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">{{ sucursal.nombre }}</option>
                        </select>
                        <p v-if="form.errors.sucursal_id" class="text-red-500 text-sm mt-1">{{ form.errors.sucursal_id }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea v-model="form.descripcion" class="input-field" rows="3" placeholder="Descripción del centro de costo"></textarea>
                    <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select v-model="form.tipo" class="input-field" required>
                            <option value="ADMINISTRATIVO">Administrativo</option>
                            <option value="OPERATIVO">Operativo</option>
                            <option value="LOGISTICO">Logístico</option>
                            <option value="FINANCIERO">Financiero</option>
                        </select>
                        <p v-if="form.errors.tipo" class="text-red-500 text-sm mt-1">{{ form.errors.tipo }}</p>
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

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('centros-costo.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Centro de Costo') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
