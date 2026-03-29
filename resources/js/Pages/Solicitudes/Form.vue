<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    solicitud: { type: Object, default: null },
    gestiones: Array,
    centrosCosto: Array,
    proveedores: Array,
    gestionActual: { type: Object, default: null },
});

const isEdit = !!props.solicitud;

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const form = useForm({
    gestion_id: props.solicitud?.gestion_id || props.gestionActual?.id || '',
    centro_costo_id: props.solicitud?.centro_costo_id || '',
    concepto: props.solicitud?.concepto || '',
    justificacion: props.solicitud?.justificacion || '',
    prioridad: props.solicitud?.prioridad || 'BAJA',
    estado: props.solicitud?.estado || 'BORRADOR',
    moneda: props.solicitud?.moneda || 'BOB',
    observaciones: props.solicitud?.observaciones || '',
    detalles: props.solicitud?.detalles?.length
        ? props.solicitud.detalles.map(d => ({
            presupuesto_detalle_id: d.presupuesto_detalle_id || '',
            proveedor_id: d.proveedor_id || '',
            descripcion: d.descripcion || '',
            cantidad: d.cantidad || 1,
            precio_unitario: d.precio_unitario || 0,
        }))
        : [blankDetalle()],
});

function blankDetalle() {
    return {
        presupuesto_detalle_id: '',
        proveedor_id: '',
        descripcion: '',
        cantidad: 1,
        precio_unitario: 0,
    };
}

function addDetalle() {
    form.detalles.push(blankDetalle());
}

function removeDetalle(index) {
    if (form.detalles.length > 1) {
        form.detalles.splice(index, 1);
    }
}

function montoDetalle(d) {
    return (parseFloat(d.cantidad) || 0) * (parseFloat(d.precio_unitario) || 0);
}

const totalSolicitado = computed(() =>
    form.detalles.reduce((sum, d) => sum + montoDetalle(d), 0)
);

function submit() {
    if (isEdit) {
        form.put(route('solicitudes.update', props.solicitud.id));
    } else {
        form.post(route('solicitudes.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Solicitud' : 'Nueva Solicitud'" />
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Solicitud' : 'Nueva Solicitud de Gasto' }}</h1>
                <Link :href="route('solicitudes.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Datos generales -->
                <div class="card p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Datos Generales</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gestión</label>
                            <select v-model="form.gestion_id" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option v-for="g in gestiones" :key="g.id" :value="g.id">{{ g.nombre }} ({{ g.anio }})</option>
                            </select>
                            <p v-if="form.errors.gestion_id" class="text-red-500 text-sm mt-1">{{ form.errors.gestion_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Centro de Costo</label>
                            <select v-model="form.centro_costo_id" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option v-for="cc in centrosCosto" :key="cc.id" :value="cc.id">
                                    {{ cc.area?.nombre }} - {{ cc.nombre }}
                                </option>
                            </select>
                            <p v-if="form.errors.centro_costo_id" class="text-red-500 text-sm mt-1">{{ form.errors.centro_costo_id }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                        <input v-model="form.concepto" type="text" class="input-field" maxlength="200" required placeholder="Describa brevemente el gasto" />
                        <p v-if="form.errors.concepto" class="text-red-500 text-sm mt-1">{{ form.errors.concepto }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Justificación</label>
                        <textarea v-model="form.justificacion" class="input-field" rows="3" required placeholder="Justifique la necesidad del gasto"></textarea>
                        <p v-if="form.errors.justificacion" class="text-red-500 text-sm mt-1">{{ form.errors.justificacion }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                            <select v-model="form.prioridad" class="input-field">
                                <option value="BAJA">Baja</option>
                                <option value="MEDIA">Media</option>
                                <option value="ALTA">Alta</option>
                                <option value="URGENTE">Urgente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select v-model="form.estado" class="input-field">
                                <option value="BORRADOR">Borrador</option>
                                <option value="PENDIENTE">Pendiente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Moneda</label>
                            <select v-model="form.moneda" class="input-field">
                                <option value="BOB">BOB - Bolivianos</option>
                                <option value="USD">USD - Dólares</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones (opcional)</label>
                        <textarea v-model="form.observaciones" class="input-field" rows="2" placeholder="Notas adicionales"></textarea>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Detalles de la Solicitud</h2>
                        <button type="button" @click="addDetalle" class="btn-secondary text-sm">
                            + Agregar detalle
                        </button>
                    </div>

                    <p v-if="form.errors.detalles" class="text-red-500 text-sm mb-3">{{ form.errors.detalles }}</p>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Presup. Detalle ID</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(det, idx) in form.detalles" :key="idx">
                                    <td class="px-3 py-2">
                                        <input v-model="det.presupuesto_detalle_id" type="number" min="1" class="input-field text-sm w-24" placeholder="ID" />
                                        <p v-if="form.errors[`detalles.${idx}.presupuesto_detalle_id`]" class="text-red-500 text-xs mt-1">{{ form.errors[`detalles.${idx}.presupuesto_detalle_id`] }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <select v-model="det.proveedor_id" class="input-field text-sm">
                                            <option value="">— Ninguno —</option>
                                            <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.razon_social }}</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input v-model="det.descripcion" type="text" class="input-field text-sm" placeholder="Descripción del ítem" />
                                        <p v-if="form.errors[`detalles.${idx}.descripcion`]" class="text-red-500 text-xs mt-1">{{ form.errors[`detalles.${idx}.descripcion`] }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input v-model="det.cantidad" type="number" min="1" step="1" class="input-field text-sm text-right w-20" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input v-model="det.precio_unitario" type="number" min="0" step="0.01" class="input-field text-sm text-right w-28" />
                                    </td>
                                    <td class="px-3 py-2 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        {{ formatMoney(montoDetalle(det)) }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button
                                            v-if="form.detalles.length > 1"
                                            type="button"
                                            @click="removeDetalle(idx)"
                                            class="text-red-500 hover:text-red-700"
                                            title="Eliminar fila"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="5" class="px-3 py-3 text-right text-sm font-semibold text-gray-700">Total Solicitado:</td>
                                    <td class="px-3 py-3 text-right text-sm font-bold text-blue-700">{{ formatMoney(totalSolicitado) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <Link :href="route('solicitudes.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar Solicitud' : 'Crear Solicitud') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
