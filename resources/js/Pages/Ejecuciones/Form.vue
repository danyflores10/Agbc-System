<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    solicitudes: Array,
    proveedores: Array,
});

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const form = useForm({
    solicitud_id: '',
    proveedor_id: '',
    descripcion: '',
    numero_factura: '',
    numero_comprobante: '',
    moneda: 'BOB',
    impuestos: 0,
    detalles: [newDetalle()],
});

function newDetalle() {
    return {
        presupuesto_detalle_id: '',
        solicitud_detalle_id: '',
        descripcion: '',
        cantidad: 1,
        precio_unitario: 0,
    };
}

function addDetalle() {
    form.detalles.push(newDetalle());
}

function removeDetalle(index) {
    if (form.detalles.length > 1) {
        form.detalles.splice(index, 1);
    }
}

function montoDetalle(d) {
    return (parseFloat(d.cantidad) || 0) * (parseFloat(d.precio_unitario) || 0);
}

const subtotal = computed(() => form.detalles.reduce((sum, d) => sum + montoDetalle(d), 0));
const total = computed(() => subtotal.value + (parseFloat(form.impuestos) || 0));

function submit() {
    form.post(route('ejecuciones.store'));
}
</script>

<template>
    <Head title="Nueva Ejecución" />
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Nueva Ejecución Presupuestaria</h1>
                <Link :href="route('ejecuciones.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Datos generales -->
                <div class="card p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Datos Generales</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Solicitud Aprobada</label>
                            <select v-model="form.solicitud_id" class="input-field">
                                <option value="">Sin solicitud asociada</option>
                                <option v-for="s in solicitudes" :key="s.id" :value="s.id">
                                    {{ s.codigo }} — {{ s.concepto }}
                                </option>
                            </select>
                            <p v-if="form.errors.solicitud_id" class="text-red-500 text-sm mt-1">{{ form.errors.solicitud_id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                            <select v-model="form.proveedor_id" class="input-field">
                                <option value="">Sin proveedor</option>
                                <option v-for="p in proveedores" :key="p.id" :value="p.id">
                                    {{ p.razon_social }}
                                </option>
                            </select>
                            <p v-if="form.errors.proveedor_id" class="text-red-500 text-sm mt-1">{{ form.errors.proveedor_id }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea v-model="form.descripcion" class="input-field" rows="2" placeholder="Descripción del gasto ejecutado"></textarea>
                        <p v-if="form.errors.descripcion" class="text-red-500 text-sm mt-1">{{ form.errors.descripcion }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nro. Factura</label>
                            <input v-model="form.numero_factura" type="text" maxlength="60" class="input-field" placeholder="Nro. factura" />
                            <p v-if="form.errors.numero_factura" class="text-red-500 text-sm mt-1">{{ form.errors.numero_factura }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nro. Comprobante</label>
                            <input v-model="form.numero_comprobante" type="text" maxlength="60" class="input-field" placeholder="Nro. comprobante" />
                            <p v-if="form.errors.numero_comprobante" class="text-red-500 text-sm mt-1">{{ form.errors.numero_comprobante }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Moneda</label>
                            <select v-model="form.moneda" class="input-field">
                                <option value="BOB">BOB</option>
                                <option value="USD">USD</option>
                            </select>
                            <p v-if="form.errors.moneda" class="text-red-500 text-sm mt-1">{{ form.errors.moneda }}</p>
                        </div>
                    </div>

                    <div class="sm:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Impuestos</label>
                        <input v-model="form.impuestos" type="number" step="0.01" min="0" class="input-field" />
                        <p v-if="form.errors.impuestos" class="text-red-500 text-sm mt-1">{{ form.errors.impuestos }}</p>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Detalles</h2>
                        <button type="button" @click="addDetalle" class="btn-secondary text-xs">+ Agregar línea</button>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(d, i) in form.detalles" :key="i" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-600">Línea {{ i + 1 }}</span>
                                <button v-if="form.detalles.length > 1" type="button" @click="removeDetalle(i)" class="text-red-500 hover:text-red-700 text-xs">
                                    Eliminar
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Presupuesto Detalle ID</label>
                                    <input v-model="d.presupuesto_detalle_id" type="number" class="input-field text-sm" placeholder="ID presupuesto detalle" />
                                    <p v-if="form.errors['detalles.' + i + '.presupuesto_detalle_id']" class="text-red-500 text-xs mt-1">{{ form.errors['detalles.' + i + '.presupuesto_detalle_id'] }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Solicitud Detalle ID <span class="text-gray-400">(opcional)</span></label>
                                    <input v-model="d.solicitud_detalle_id" type="number" class="input-field text-sm" placeholder="ID solicitud detalle" />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                                <input v-model="d.descripcion" type="text" class="input-field text-sm" placeholder="Descripción del ítem" />
                                <p v-if="form.errors['detalles.' + i + '.descripcion']" class="text-red-500 text-xs mt-1">{{ form.errors['detalles.' + i + '.descripcion'] }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 mt-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                                    <input v-model="d.cantidad" type="number" step="0.01" min="0.01" class="input-field text-sm" />
                                    <p v-if="form.errors['detalles.' + i + '.cantidad']" class="text-red-500 text-xs mt-1">{{ form.errors['detalles.' + i + '.cantidad'] }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Precio Unitario</label>
                                    <input v-model="d.precio_unitario" type="number" step="0.01" min="0" class="input-field text-sm" />
                                    <p v-if="form.errors['detalles.' + i + '.precio_unitario']" class="text-red-500 text-xs mt-1">{{ form.errors['detalles.' + i + '.precio_unitario'] }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Monto</label>
                                    <p class="input-field text-sm bg-gray-100 flex items-center font-semibold text-green-600">
                                        {{ formatMoney(montoDetalle(d)) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.detalles" class="text-red-500 text-sm mt-2">{{ form.errors.detalles }}</p>

                    <!-- Totales -->
                    <div class="flex justify-end mt-6 space-x-6 text-sm">
                        <div class="text-right">
                            <p class="text-gray-500">Subtotal</p>
                            <p class="font-semibold text-gray-900">{{ formatMoney(subtotal) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500">Impuestos</p>
                            <p class="font-semibold text-gray-900">{{ formatMoney(form.impuestos) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500">Total</p>
                            <p class="text-xl font-bold text-green-600">{{ formatMoney(total) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <Link :href="route('ejecuciones.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Registrando...' : 'Registrar Ejecución' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
