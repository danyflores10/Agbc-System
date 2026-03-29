<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ solicitud: Object });

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const prioridadClasses = {
    BAJA: 'bg-gray-100 text-gray-700',
    MEDIA: 'bg-blue-100 text-blue-700',
    ALTA: 'bg-yellow-100 text-yellow-700',
    URGENTE: 'bg-red-100 text-red-700',
};

const estadoClasses = {
    BORRADOR: 'bg-gray-100 text-gray-700',
    PENDIENTE: 'bg-yellow-100 text-yellow-700',
    EN_REVISION: 'bg-blue-100 text-blue-700',
    APROBADA: 'bg-green-100 text-green-700',
    RECHAZADA: 'bg-red-100 text-red-700',
    EJECUTADA: 'bg-purple-100 text-purple-700',
    ANULADA: 'bg-gray-100 text-gray-600',
};

const estadoLabels = {
    BORRADOR: 'Borrador',
    PENDIENTE: 'Pendiente',
    EN_REVISION: 'En Revisión',
    APROBADA: 'Aprobada',
    RECHAZADA: 'Rechazada',
    EJECUTADA: 'Ejecutada',
    ANULADA: 'Anulada',
};

const aprobacionEstadoClasses = {
    PENDIENTE: 'bg-yellow-100 text-yellow-700',
    APROBADA: 'bg-green-100 text-green-700',
    RECHAZADA: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head :title="`Solicitud ${solicitud.codigo}`" />
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Encabezado -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ solicitud.codigo }}</h1>
                    <p class="text-sm text-gray-500">{{ solicitud.concepto }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <Link v-if="solicitud.estado === 'BORRADOR'" :href="route('solicitudes.edit', solicitud.id)" class="btn-primary">Editar</Link>
                    <Link :href="route('solicitudes.index')" class="btn-secondary">← Volver</Link>
                </div>
            </div>

            <!-- Información general -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Información de la Solicitud</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Código</dt>
                        <dd class="text-sm font-medium font-mono text-blue-700">{{ solicitud.codigo }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Gestión</dt>
                        <dd class="text-sm font-medium">{{ solicitud.gestion?.nombre }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Solicitante</dt>
                        <dd class="text-sm font-medium">{{ solicitud.solicitante?.nombres }} {{ solicitud.solicitante?.apellidos }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Centro de Costo</dt>
                        <dd class="text-sm font-medium">{{ solicitud.centro_costo?.nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Área</dt>
                        <dd class="text-sm font-medium">{{ solicitud.centro_costo?.area?.nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Sucursal</dt>
                        <dd class="text-sm font-medium">{{ solicitud.centro_costo?.sucursal?.nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Moneda</dt>
                        <dd class="text-sm font-medium">{{ solicitud.moneda }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Prioridad</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded-full font-medium" :class="prioridadClasses[solicitud.prioridad] || 'bg-gray-100 text-gray-600'">
                                {{ solicitud.prioridad }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs rounded-full font-medium" :class="estadoClasses[solicitud.estado] || 'bg-gray-100 text-gray-600'">
                                {{ estadoLabels[solicitud.estado] || solicitud.estado }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Fecha de Creación</dt>
                        <dd class="text-sm font-medium">{{ solicitud.created_at?.split('T')[0] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Justificación -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold mb-2">Justificación</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ solicitud.justificacion || '—' }}</p>
            </div>

            <!-- Observaciones -->
            <div v-if="solicitud.observaciones" class="card p-6 mb-6">
                <h2 class="text-lg font-semibold mb-2">Observaciones</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ solicitud.observaciones }}</p>
            </div>

            <!-- Detalles -->
            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Detalles de la Solicitud</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partida</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="d in solicitud.detalles" :key="d.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ d.presupuesto_detalle?.partida?.codigo }} - {{ d.presupuesto_detalle?.partida?.nombre }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ d.presupuesto_detalle?.periodo?.nombre ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ d.proveedor?.razon_social ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ d.descripcion }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ d.cantidad }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ formatMoney(d.precio_unitario) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ formatMoney(d.cantidad * d.precio_unitario) }}</td>
                            </tr>
                            <tr v-if="!solicitud.detalles?.length">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">Sin detalles registrados</td>
                            </tr>
                        </tbody>
                        <tfoot v-if="solicitud.detalles?.length" class="bg-gray-50">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-blue-700">
                                    {{ formatMoney(solicitud.detalles.reduce((s, d) => s + (d.cantidad * d.precio_unitario), 0)) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Aprobaciones -->
            <div v-if="solicitud.aprobaciones?.length" class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Historial de Aprobaciones</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <div v-for="a in solicitud.aprobaciones" :key="a.id" class="px-6 py-4 flex items-start space-x-4">
                        <!-- Timeline indicator -->
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                            :class="{
                                'bg-green-500': a.estado === 'APROBADA',
                                'bg-red-500': a.estado === 'RECHAZADA',
                                'bg-yellow-500': a.estado === 'PENDIENTE',
                                'bg-gray-400': !['APROBADA','RECHAZADA','PENDIENTE'].includes(a.estado),
                            }"
                        >
                            {{ a.orden_nivel }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ a.rol?.nombre ?? 'Nivel ' + a.orden_nivel }}</p>
                                    <p class="text-xs text-gray-500">{{ a.aprobador ? (a.aprobador.nombres + ' ' + a.aprobador.apellidos) : 'Pendiente de asignación' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium" :class="aprobacionEstadoClasses[a.estado] || 'bg-gray-100 text-gray-600'">
                                        {{ estadoLabels[a.estado] || a.estado }}
                                    </span>
                                    <p v-if="a.fecha_decision" class="text-xs text-gray-400 mt-1">{{ a.fecha_decision?.split('T')[0] }}</p>
                                </div>
                            </div>
                            <p v-if="a.comentario" class="text-sm text-gray-600 mt-2 italic">"{{ a.comentario }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
