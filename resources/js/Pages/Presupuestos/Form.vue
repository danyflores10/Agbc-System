<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    presupuesto: { type: Object, default: null },
    gestiones: Array,
});

const isEdit = !!props.presupuesto;

const form = useForm({
    gestion_id: props.presupuesto?.gestion_id || '',
    nombre: props.presupuesto?.nombre || '',
    descripcion: props.presupuesto?.descripcion || '',
    estado: props.presupuesto?.estado || 'BORRADOR',
});

function submit() {
    if (isEdit) {
        form.put(route('presupuestos.update', props.presupuesto.id));
    } else {
        form.post(route('presupuestos.store'));
    }
}

const estadosCreate = ['BORRADOR', 'EN_REVISION', 'APROBADO'];
const estadosEdit = ['BORRADOR', 'EN_REVISION', 'APROBADO', 'CERRADO', 'ANULADO'];
const estadoLabels = {
    BORRADOR: 'Borrador',
    EN_REVISION: 'En Revisión',
    APROBADO: 'Aprobado',
    CERRADO: 'Cerrado',
    ANULADO: 'Anulado',
};
</script>

<template>
    <Head :title="isEdit ? 'Editar Presupuesto' : 'Nuevo Presupuesto'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Presupuesto' : 'Nuevo Presupuesto' }}</h1>
                <Link :href="route('presupuestos.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gestión</label>
                    <select v-model="form.gestion_id" class="input-field" required>
                        <option value="">Seleccione una gestión...</option>
                        <option v-for="g in gestiones" :key="g.id" :value="g.id">{{ g.anio }}</option>
                    </select>
                    <p v-if="form.errors.gestion_id" class="text-red-500 text-sm mt-1">{{ form.errors.gestion_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input v-model="form.nombre" type="text" class="input-field" maxlength="150" required placeholder="Nombre del presupuesto" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea v-model="form.descripcion" class="input-field" rows="3" placeholder="Descripción del presupuesto (opcional)"></textarea>
                    <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select v-model="form.estado" class="input-field">
                        <option v-for="e in (isEdit ? estadosEdit : estadosCreate)" :key="e" :value="e">{{ estadoLabels[e] }}</option>
                    </select>
                    <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('presupuestos.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Presupuesto') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
