<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ gestion: { type: Object, default: null } });
const isEdit = !!props.gestion;

const form = useForm({
    anio: props.gestion?.anio || new Date().getFullYear(),
    fecha_inicio: props.gestion?.fecha_inicio || '',
    fecha_fin: props.gestion?.fecha_fin || '',
    estado: props.gestion?.estado || 'PLANIFICACION',
});

function submit() {
    if (isEdit) {
        form.put(route('gestiones.update', props.gestion.id));
    } else {
        form.post(route('gestiones.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Gestión' : 'Nueva Gestión'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Gestión' : 'Nueva Gestión' }}</h1>
                <Link :href="route('gestiones.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                        <input v-model="form.anio" type="number" class="input-field" required min="2020" max="2099" />
                        <p v-if="form.errors.anio" class="text-red-500 text-sm mt-1">{{ form.errors.anio }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select v-model="form.estado" class="input-field">
                            <option value="PLANIFICACION">PLANIFICACION</option>
                            <option value="ABIERTA">ABIERTA</option>
                            <option value="CERRADA">CERRADA</option>
                        </select>
                        <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                        <input v-model="form.fecha_inicio" type="date" class="input-field" required />
                        <p v-if="form.errors.fecha_inicio" class="text-red-500 text-sm mt-1">{{ form.errors.fecha_inicio }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                        <input v-model="form.fecha_fin" type="date" class="input-field" required />
                        <p v-if="form.errors.fecha_fin" class="text-red-500 text-sm mt-1">{{ form.errors.fecha_fin }}</p>
                    </div>
                </div>

                <div v-if="!isEdit" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-700">
                        <strong>Nota:</strong> Al crear una gestión se generarán automáticamente 12 periodos fiscales mensuales.
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('gestiones.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Gestión') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
