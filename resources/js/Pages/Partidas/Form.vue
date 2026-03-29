<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    partida: { type: Object, default: null },
    categorias: { type: Array, default: () => [] },
});
const isEdit = !!props.partida;

const form = useForm({
    codigo: props.partida?.codigo || '',
    nombre: props.partida?.nombre || '',
    descripcion: props.partida?.descripcion || '',
    tipo: props.partida?.tipo || 'FUNCIONAMIENTO',
    nivel: props.partida?.nivel ?? 1,
    imputable: props.partida?.imputable ?? false,
    parent_id: props.partida?.parent_id || '',
    estado: props.partida?.estado || 'ACTIVO',
});

function submit() {
    if (isEdit) {
        form.put(route('partidas.update', props.partida.id));
    } else {
        form.post(route('partidas.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Partida' : 'Nueva Partida'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Partida' : 'Nueva Partida' }}</h1>
                <Link :href="route('partidas.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input v-model="form.codigo" type="text" class="input-field" maxlength="30" required placeholder="Ej: 4.01.01" />
                        <p v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">{{ form.errors.codigo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input v-model="form.nombre" type="text" class="input-field" maxlength="180" required placeholder="Nombre de la partida" />
                        <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea v-model="form.descripcion" class="input-field" rows="3" placeholder="Descripción de la partida"></textarea>
                    <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select v-model="form.tipo" class="input-field" required>
                            <option value="FUNCIONAMIENTO">Funcionamiento</option>
                            <option value="OPERATIVO">Operativo</option>
                            <option value="INVERSION">Inversión</option>
                            <option value="CONTINGENCIA">Contingencia</option>
                        </select>
                        <p v-if="form.errors.tipo" class="text-red-500 text-sm mt-1">{{ form.errors.tipo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                        <input v-model.number="form.nivel" type="number" class="input-field" min="1" required />
                        <p v-if="form.errors.nivel" class="text-red-500 text-sm mt-1">{{ form.errors.nivel }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Partida Padre</label>
                        <select v-model="form.parent_id" class="input-field">
                            <option value="">— Sin padre —</option>
                            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                                {{ cat.codigo }} — {{ cat.nombre }}
                            </option>
                        </select>
                        <p v-if="form.errors.parent_id" class="text-red-500 text-sm mt-1">{{ form.errors.parent_id }}</p>
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

                <div class="flex items-center">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input v-model="form.imputable" type="checkbox" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue" />
                        <span class="text-sm text-gray-700">Partida imputable (permite imputar gastos directamente)</span>
                    </label>
                    <p v-if="form.errors.imputable" class="text-red-500 text-sm mt-1">{{ form.errors.imputable }}</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('partidas.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Partida') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
