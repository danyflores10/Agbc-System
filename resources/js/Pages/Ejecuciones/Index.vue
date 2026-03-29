<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';

const props = defineProps({ ejecuciones: Object, filters: Object });

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const search = ref(props.filters?.search || '');
const estado = ref(props.filters?.estado || '');

let debounce = null;
watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => applyFilters(), 300);
});

watch(estado, () => applyFilters());

function applyFilters() {
    router.get(route('ejecuciones.index'), {
        search: search.value || undefined,
        estado: estado.value || undefined,
    }, { preserveState: true, replace: true });
}

const estadoClasses = {
    REGISTRADO: 'bg-blue-100 text-blue-700',
    PAGADO: 'bg-green-100 text-green-700',
    ANULADO: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Ejecuciones" />
    <AppLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ejecuciones Presupuestarias</h1>
                <p class="text-sm text-gray-500">Registro de gastos ejecutados contra presupuesto</p>
            </div>
            <Link :href="route('ejecuciones.create')" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nueva Ejecución
            </Link>
        </div>

        <div class="card overflow-hidden">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row gap-3">
                <input v-model="search" type="text" class="input-field flex-1" placeholder="Buscar por código o descripción..." />
                <select v-model="estado" class="input-field sm:w-48">
                    <option value="">Todos los estados</option>
                    <option value="REGISTRADO">Registrado</option>
                    <option value="PAGADO">Pagado</option>
                    <option value="ANULADO">Anulado</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitud</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="e in ejecuciones.data" :key="e.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-brand-blue">{{ e.codigo }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ e.fecha_ejecucion }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ e.solicitud?.codigo || '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ e.proveedor?.razon_social || '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ formatMoney(e.total) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-medium" :class="estadoClasses[e.estado] || 'bg-gray-100 text-gray-600'">
                                    {{ e.estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="route('ejecuciones.show', e.id)" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!ejecuciones.data?.length">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No hay ejecuciones registradas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :data="ejecuciones" />
        </div>
    </AppLayout>
</template>
