<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ proveedor: { type: Object, default: null } });
const isEdit = !!props.proveedor;

const form = useForm({
    tipo_persona: props.proveedor?.tipo_persona || 'NATURAL',
    razon_social: props.proveedor?.razon_social || '',
    nit: props.proveedor?.nit || '',
    telefono: props.proveedor?.telefono || '',
    email: props.proveedor?.email || '',
    direccion: props.proveedor?.direccion || '',
    estado: props.proveedor?.estado || 'ACTIVO',
});

function submit() {
    if (isEdit) {
        form.put(route('proveedores.update', props.proveedor.id));
    } else {
        form.post(route('proveedores.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Proveedor' : 'Nuevo Proveedor'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</h1>
                <Link :href="route('proveedores.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Persona</label>
                        <select v-model="form.tipo_persona" class="input-field" required>
                            <option value="NATURAL">Natural</option>
                            <option value="JURIDICA">Jurídica</option>
                        </select>
                        <p v-if="form.errors.tipo_persona" class="text-red-500 text-sm mt-1">{{ form.errors.tipo_persona }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIT</label>
                        <input v-model="form.nit" type="text" class="input-field" maxlength="30" required placeholder="Ej: 1234567890" />
                        <p v-if="form.errors.nit" class="text-red-500 text-sm mt-1">{{ form.errors.nit }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social</label>
                    <input v-model="form.razon_social" type="text" class="input-field" maxlength="180" required placeholder="Razón social del proveedor" />
                    <p v-if="form.errors.razon_social" class="text-red-500 text-sm mt-1">{{ form.errors.razon_social }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input v-model="form.telefono" type="text" class="input-field" maxlength="30" placeholder="Ej: +591 70000000" />
                        <p v-if="form.errors.telefono" class="text-red-500 text-sm mt-1">{{ form.errors.telefono }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="input-field" maxlength="150" placeholder="correo@ejemplo.com" />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <textarea v-model="form.direccion" class="input-field" rows="3" placeholder="Dirección del proveedor"></textarea>
                    <p v-if="form.errors.direccion" class="text-red-500 text-sm mt-1">{{ form.errors.direccion }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select v-model="form.estado" class="input-field">
                        <option value="ACTIVO">Activo</option>
                        <option value="INACTIVO">Inactivo</option>
                    </select>
                    <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <Link :href="route('proveedores.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Proveedor') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
