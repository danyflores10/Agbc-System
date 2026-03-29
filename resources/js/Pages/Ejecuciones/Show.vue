<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    ejecucion: Object,
    adjuntos: Array,
});

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

function formatBytes(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

const estadoClasses = {
    REGISTRADO: 'bg-blue-100 text-blue-700',
    PAGADO: 'bg-green-100 text-green-700',
    ANULADO: 'bg-red-100 text-red-700',
};

const archivo = ref(null);
const uploading = ref(false);

function uploadAdjunto() {
    if (!archivo.value) return;
    uploading.value = true;
    router.post(route('ejecuciones.adjunto', props.ejecucion.id), { archivo: archivo.value }, {
        forceFormData: true,
        onSuccess: () => {
            archivo.value = null;
            if (fileInput.value) fileInput.value.value = '';
        },
        onFinish: () => { uploading.value = false; },
    });
}

const fileInput = ref(null);

function onFileChange(e) {
    archivo.value = e.target.files[0] || null;
}
</script>

<template>
    <Head title="Detalle Ejecución" />
    <AppLayout>
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Ejecución {{ ejecucion.codigo }}</h1>
                    <p class="text-sm text-gray-500">{{ ejecucion.descripcion }}</p>
                </div>
                <Link :href="route('ejecuciones.index')" class="btn-secondary">← Volver</Link>
            </div>

            <!-- Info general -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Código</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.codigo }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Fecha</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.fecha_ejecucion }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Solicitud</dt>
                        <dd class="text-sm font-medium">
                            <Link v-if="ejecucion.solicitud" :href="route('solicitudes.show', ejecucion.solicitud.id)" class="text-brand-blue hover:underline">
                                {{ ejecucion.solicitud.codigo }}
                            </Link>
                            <span v-else class="text-gray-400">—</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Proveedor</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.proveedor?.razon_social || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Registrado por</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.registrador?.name || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Moneda</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.moneda }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Nro. Factura</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.numero_factura || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Nro. Comprobante</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ ejecucion.numero_comprobante || '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded-full font-medium" :class="estadoClasses[ejecucion.estado] || 'bg-gray-100 text-gray-600'">
                                {{ ejecucion.estado }}
                            </span>
                        </dd>
                    </div>
                </dl>

                <!-- Totales -->
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200 space-x-8 text-sm">
                    <div class="text-right">
                        <p class="text-gray-500">Subtotal</p>
                        <p class="font-semibold text-gray-900">{{ formatMoney(ejecucion.subtotal) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500">Impuestos</p>
                        <p class="font-semibold text-gray-900">{{ formatMoney(ejecucion.impuestos) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500">Total</p>
                        <p class="text-xl font-bold text-green-600">{{ formatMoney(ejecucion.total) }}</p>
                    </div>
                </div>
            </div>

            <!-- Detalles -->
            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detalles</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partida</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Centro Costo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="d in ejecucion.detalles" :key="d.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ d.presupuesto_detalle?.partida?.nombre || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ d.presupuesto_detalle?.centro_costo?.area?.nombre || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ d.descripcion }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500">{{ d.cantidad }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500">{{ formatMoney(d.precio_unitario) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ formatMoney(d.cantidad * d.precio_unitario) }}</td>
                            </tr>
                            <tr v-if="!ejecucion.detalles?.length">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Sin detalles</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Adjuntos -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Adjuntos</h2>

                <div v-if="adjuntos?.length" class="space-y-2 mb-4">
                    <div v-for="a in adjuntos" :key="a.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ a.nombre_original }}</p>
                                <p class="text-xs text-gray-400">{{ a.mime_type }} · {{ formatBytes(a.peso_bytes) }}</p>
                            </div>
                        </div>
                        <a :href="'/storage/' + a.ruta_archivo" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                            Descargar
                        </a>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-400 mb-4">No hay archivos adjuntos</p>

                <!-- Upload -->
                <form @submit.prevent="uploadAdjunto" class="border-t pt-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Agregar Adjunto</h3>
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <input ref="fileInput" type="file" @change="onFileChange" class="input-field text-sm" />
                        </div>
                        <button type="submit" class="btn-primary text-sm" :disabled="uploading || !archivo">
                            {{ uploading ? 'Subiendo...' : 'Subir Archivo' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
