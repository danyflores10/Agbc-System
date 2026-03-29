<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ sucursal: { type: Object, default: null } });
const isEdit = !!props.sucursal;

const form = useForm({
    codigo: props.sucursal?.codigo || '',
    nombre: props.sucursal?.nombre || '',
    departamento: props.sucursal?.departamento || '',
    ciudad: props.sucursal?.ciudad || '',
    direccion: props.sucursal?.direccion || '',
    telefono: props.sucursal?.telefono || '',
    email: props.sucursal?.email || '',
    tipo: props.sucursal?.tipo || '',
    estado: props.sucursal?.estado || 'ACTIVO',
});

function submit() {
    if (isEdit) {
        form.put(route('sucursales.update', props.sucursal.id));
    } else {
        form.post(route('sucursales.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Sucursal' : 'Nueva Sucursal'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Sucursal' : 'Nueva Sucursal' }}</h1>
                <Link :href="route('sucursales.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input v-model="form.codigo" type="text" maxlength="20" class="input-field" required placeholder="Ej: SUC-LP-001" />
                        <p v-if="form.errors.codigo" class="text-red-500 text-sm mt-1">{{ form.errors.codigo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input v-model="form.nombre" type="text" maxlength="120" class="input-field" required placeholder="Nombre de la sucursal" />
                        <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input v-model="form.departamento" type="text" maxlength="80" class="input-field" required placeholder="Departamento" />
                        <p v-if="form.errors.departamento" class="text-red-500 text-sm mt-1">{{ form.errors.departamento }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                        <input v-model="form.ciudad" type="text" maxlength="80" class="input-field" placeholder="Ciudad" />
                        <p v-if="form.errors.ciudad" class="text-red-500 text-sm mt-1">{{ form.errors.ciudad }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <textarea v-model="form.direccion" rows="3" class="input-field" placeholder="Dirección de la sucursal"></textarea>
                    <p v-if="form.errors.direccion" class="text-red-500 text-sm mt-1">{{ form.errors.direccion }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input v-model="form.telefono" type="text" maxlength="30" class="input-field" placeholder="Teléfono" />
                        <p v-if="form.errors.telefono" class="text-red-500 text-sm mt-1">{{ form.errors.telefono }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="input-field" placeholder="correo@ejemplo.com" />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select v-model="form.tipo" class="input-field" required>
                            <option value="">Seleccione...</option>
                            <option value="CENTRAL">Central</option>
                            <option value="SUCURSAL">Sucursal</option>
                            <option value="AGENCIA">Agencia</option>
                            <option value="OFICINA">Oficina</option>
                        </select>
                        <p v-if="form.errors.tipo" class="text-red-500 text-sm mt-1">{{ form.errors.tipo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select v-model="form.estado" class="input-field" required>
                            <option value="ACTIVO">Activo</option>
                            <option value="INACTIVO">Inactivo</option>
                        </select>
                        <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('sucursales.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Sucursal') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
