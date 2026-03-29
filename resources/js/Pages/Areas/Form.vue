<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ area: { type: Object, default: null } });
const isEdit = !!props.area;

const form = useForm({
    codigo: props.area?.codigo || '',
    nombre: props.area?.nombre || '',
    descripcion: props.area?.descripcion || '',
    es_operativa: props.area?.es_operativa ?? false,
    estado: props.area?.estado || 'ACTIVO',
});

function submit() {
    if (isEdit) {
        form.put(route('areas.update', props.area.id));
    } else {
        form.post(route('areas.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Área' : 'Nueva Área'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Área' : 'Nueva Área' }}</h1>
                <Link :href="route('areas.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input v-model="form.codigo" type="text" class="input-field" maxlength="20" required placeholder="Ej: ADM-001" />
                        <p v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">{{ form.errors.codigo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input v-model="form.nombre" type="text" class="input-field" maxlength="120" required placeholder="Nombre del área" />
                        <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea v-model="form.descripcion" class="input-field" rows="3" placeholder="Descripción del área"></textarea>
                    <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select v-model="form.estado" class="input-field">
                            <option value="ACTIVO">Activo</option>
                            <option value="INACTIVO">Inactivo</option>
                        </select>
                        <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input v-model="form.es_operativa" type="checkbox" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue" />
                            <span class="text-sm text-gray-700">Área operativa</span>
                        </label>
                        <p v-if="form.errors.es_operativa" class="text-red-500 text-sm mt-1">{{ form.errors.es_operativa }}</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('areas.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Área') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
