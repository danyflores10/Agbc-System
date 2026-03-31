<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps({
    presupuesto: Object,
    detalles: Object,
    totales: Object,
    periodos: Array,
    centrosCosto: Array,
    partidas: Array,
});

const { confirmDelete } = useConfirm();

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const estadoClasses = {
    BORRADOR: 'bg-gray-100 text-gray-700',
    EN_REVISION: 'bg-yellow-100 text-yellow-700',
    APROBADO: 'bg-green-100 text-green-700',
    CERRADO: 'bg-blue-100 text-blue-700',
    ANULADO: 'bg-red-100 text-red-700',
};

const detalleForm = useForm({
    presupuesto_id: props.presupuesto.id,
    centro_costo_id: '',
    partida_id: '',
    periodo_id: '',
    monto_inicial: 0,
    monto_ajuste: 0,
    observacion: '',
});

function addDetalle() {
    detalleForm.post(route('presupuesto-detalles.store'), {
        preserveScroll: true,
        onSuccess: () => detalleForm.reset('centro_costo_id', 'partida_id', 'periodo_id', 'monto_inicial', 'monto_ajuste', 'observacion'),
    });
}

async function destroyDetalle(id) {
    if (await confirmDelete('El detalle del presupuesto será eliminado permanentemente.')) {
        router.delete(route('presupuesto-detalles.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head :title="'Presupuesto: ' + presupuesto.nombre" />
    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ presupuesto.nombre }}</h1>
                    <p class="text-sm text-gray-500">Gestión {{ presupuesto.gestion?.anio }} · Versión {{ presupuesto.version }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <Link :href="route('presupuestos.edit', presupuesto.id)" class="btn-secondary">Editar</Link>
                    <Link :href="route('presupuestos.index')" class="btn-secondary">← Volver</Link>
                </div>
            </div>

            <!-- Info del presupuesto -->
            <div class="card p-6 mb-6">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Estado</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="estadoClasses[presupuesto.estado] || 'bg-gray-100 text-gray-700'">
                                {{ presupuesto.estado?.replace('_', ' ') }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Creado por</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ presupuesto.creador?.nombre_completo || (presupuesto.creador?.nombres && presupuesto.creador?.apellidos ? presupuesto.creador.nombres + ' ' + presupuesto.creador.apellidos : '—') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Aprobado por</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            <template v-if="presupuesto.aprobador">
                                {{ presupuesto.aprobador?.nombre_completo || (presupuesto.aprobador?.nombres + ' ' + presupuesto.aprobador?.apellidos) }}
                                <span v-if="presupuesto.fecha_aprobacion" class="text-gray-400 text-xs ml-1">({{ presupuesto.fecha_aprobacion }})</span>
                            </template>
                            <span v-else class="text-gray-400">—</span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Totales -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="card p-4 text-center">
                    <p class="text-sm text-gray-500">Total Inicial</p>
                    <p class="text-lg font-bold text-gray-900">{{ formatMoney(totales?.total_inicial) }}</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-sm text-gray-500">Total Ajuste</p>
                    <p class="text-lg font-bold text-yellow-600">{{ formatMoney(totales?.total_ajuste) }}</p>
                </div>
                <div class="card p-4 text-center">
                    <p class="text-sm text-gray-500">Total Vigente</p>
                    <p class="text-lg font-bold text-green-600">{{ formatMoney(totales?.total_vigente) }}</p>
                </div>
            </div>

            <!-- Formulario inline para agregar detalle -->
            <div class="card p-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Agregar Detalle</h2>
                <form @submit.prevent="addDetalle" class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Centro Costo</label>
                        <select v-model="detalleForm.centro_costo_id" class="input-field text-sm" required>
                            <option value="">Seleccione...</option>
                            <option v-for="cc in centrosCosto" :key="cc.id" :value="cc.id">
                                {{ cc.area?.nombre }} - {{ cc.nombre }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Partida</label>
                        <select v-model="detalleForm.partida_id" class="input-field text-sm" required>
                            <option value="">Seleccione...</option>
                            <option v-for="p in partidas" :key="p.id" :value="p.id">{{ p.codigo }} - {{ p.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Periodo</label>
                        <select v-model="detalleForm.periodo_id" class="input-field text-sm" required>
                            <option value="">Seleccione...</option>
                            <option v-for="per in periodos" :key="per.id" :value="per.id">{{ per.nombre }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Monto Inicial</label>
                        <input v-model="detalleForm.monto_inicial" type="number" step="0.01" min="0" class="input-field text-sm" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Monto Ajuste</label>
                        <input v-model="detalleForm.monto_ajuste" type="number" step="0.01" class="input-field text-sm" />
                    </div>
                    <div>
                        <button type="submit" class="btn-primary w-full text-sm" :disabled="detalleForm.processing">
                            {{ detalleForm.processing ? 'Agregando...' : 'Agregar' }}
                        </button>
                    </div>
                </form>
                <div class="mt-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Observación (opcional)</label>
                    <input v-model="detalleForm.observacion" type="text" class="input-field text-sm" placeholder="Observación" />
                </div>
                <div v-if="Object.keys(detalleForm.errors).length" class="mt-2 space-y-1">
                    <p v-for="(error, key) in detalleForm.errors" :key="key" class="text-red-500 text-sm">{{ error }}</p>
                </div>
            </div>

            <!-- Tabla de detalles -->
            <div class="card overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detalles del Presupuesto</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Centro Costo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partida</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Inicial</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Ajuste</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Vigente</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="d in detalles.data" :key="d.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ d.centro_costo?.area?.nombre }} - {{ d.centro_costo?.nombre }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ d.partida?.codigo }} - {{ d.partida?.nombre }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ d.periodo?.nombre }}</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ formatMoney(d.monto_inicial) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-yellow-600">{{ formatMoney(d.monto_ajuste) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ formatMoney(d.monto_vigente) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        @click="destroyDetalle(d.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold text-red-600 bg-red-50 border border-red-200 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all duration-150 shadow-sm"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!detalles.data?.length">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay detalles registrados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :data="detalles" />
            </div>
        </div>
    </AppLayout>
</template>
