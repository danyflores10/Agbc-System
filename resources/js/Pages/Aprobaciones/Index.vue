<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

defineProps({ pendientes: Object });

function formatMoney(val) {
    return new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(val || 0);
}

const activeId = ref(null);
const activeAction = ref(null); // 'aprobar' | 'rechazar'
const comentario = ref('');

function openAprobar(id) {
    activeId.value = id;
    activeAction.value = 'aprobar';
    comentario.value = '';
}

function openRechazar(id) {
    activeId.value = id;
    activeAction.value = 'rechazar';
    comentario.value = '';
}

function cancelar() {
    activeId.value = null;
    activeAction.value = null;
    comentario.value = '';
}

function submitAprobar() {
    router.put(route('aprobaciones.aprobar', activeId.value), { comentario: comentario.value }, {
        onSuccess: () => cancelar(),
    });
}

function submitRechazar() {
    if (comentario.value.trim().length < 10) {
        alert('El comentario de rechazo debe tener al menos 10 caracteres.');
        return;
    }
    router.put(route('aprobaciones.rechazar', activeId.value), { comentario: comentario.value }, {
        onSuccess: () => cancelar(),
    });
}
</script>

<template>
    <Head title="Bandeja de Aprobaciones" />
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Bandeja de Aprobaciones</h1>
            <p class="text-sm text-gray-500">Solicitudes pendientes de su aprobación</p>
        </div>

        <!-- Vista desktop: tabla -->
        <div class="card overflow-hidden hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitud</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gestión</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Nivel</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template v-for="item in pendientes.data" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono text-brand-blue">{{ item.solicitud?.codigo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ item.solicitud?.concepto }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ item.solicitud?.centro_costo?.area?.nombre || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ item.solicitud?.solicitante?.name || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ item.solicitud?.gestion?.anio || '—' }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">{{ item.orden_nivel }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ item.rol?.nombre || '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button @click="openAprobar(item.id)" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                        Aprobar
                                    </button>
                                    <button @click="openRechazar(item.id)" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
                                        Rechazar
                                    </button>
                                </td>
                            </tr>
                            <!-- Fila expandible: Aprobar -->
                            <tr v-if="activeId === item.id && activeAction === 'aprobar'">
                                <td colspan="8" class="px-4 py-4 bg-green-50 border-t border-green-200">
                                    <p class="text-sm font-medium text-green-700 mb-2">Aprobar solicitud {{ item.solicitud?.codigo }}</p>
                                    <textarea v-model="comentario" class="input-field" rows="2" placeholder="Comentario (opcional)"></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button @click="cancelar" class="btn-secondary text-xs">Cancelar</button>
                                        <button @click="submitAprobar" class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                            Confirmar Aprobación
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Fila expandible: Rechazar -->
                            <tr v-if="activeId === item.id && activeAction === 'rechazar'">
                                <td colspan="8" class="px-4 py-4 bg-red-50 border-t border-red-200">
                                    <p class="text-sm font-medium text-red-700 mb-2">Rechazar solicitud {{ item.solicitud?.codigo }}</p>
                                    <textarea v-model="comentario" class="input-field" rows="2" placeholder="Motivo del rechazo (mínimo 10 caracteres) *" required></textarea>
                                    <p v-if="comentario.trim().length > 0 && comentario.trim().length < 10" class="text-xs text-red-500 mt-1">
                                        Mínimo 10 caracteres ({{ comentario.trim().length }}/10)
                                    </p>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button @click="cancelar" class="btn-secondary text-xs">Cancelar</button>
                                        <button @click="submitRechazar" class="btn-danger text-xs">Confirmar Rechazo</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="!pendientes.data?.length">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                No hay solicitudes pendientes de aprobación
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :data="pendientes" />
        </div>

        <!-- Vista mobile: cards -->
        <div class="md:hidden space-y-4">
            <div v-for="item in pendientes.data" :key="'m-' + item.id" class="card p-4">
                <div class="flex items-start justify-between mb-2">
                    <span class="font-mono font-bold text-brand-blue text-sm">{{ item.solicitud?.codigo }}</span>
                    <span class="text-xs text-gray-400">Nivel {{ item.orden_nivel }}</span>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ item.solicitud?.concepto }}</h3>
                <div class="text-xs text-gray-500 space-y-0.5 mb-3">
                    <p>Área: {{ item.solicitud?.centro_costo?.area?.nombre || '—' }}</p>
                    <p>Solicitante: {{ item.solicitud?.solicitante?.name || '—' }}</p>
                    <p>Gestión: {{ item.solicitud?.gestion?.anio || '—' }}</p>
                    <p>Rol: {{ item.rol?.nombre || '—' }}</p>
                </div>
                <div class="flex space-x-2">
                    <button @click="openAprobar(item.id)" class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors text-center">
                        Aprobar
                    </button>
                    <button @click="openRechazar(item.id)" class="flex-1 bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-medium hover:bg-red-700 transition-colors text-center">
                        Rechazar
                    </button>
                </div>

                <!-- Inline: Aprobar -->
                <div v-if="activeId === item.id && activeAction === 'aprobar'" class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-green-700 mb-2">Aprobar solicitud</p>
                    <textarea v-model="comentario" class="input-field text-sm" rows="2" placeholder="Comentario (opcional)"></textarea>
                    <div class="flex justify-end space-x-2 mt-2">
                        <button @click="cancelar" class="btn-secondary text-xs">Cancelar</button>
                        <button @click="submitAprobar" class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                            Confirmar
                        </button>
                    </div>
                </div>

                <!-- Inline: Rechazar -->
                <div v-if="activeId === item.id && activeAction === 'rechazar'" class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm font-medium text-red-700 mb-2">Rechazar solicitud</p>
                    <textarea v-model="comentario" class="input-field text-sm" rows="2" placeholder="Motivo del rechazo (mínimo 10 caracteres) *"></textarea>
                    <p v-if="comentario.trim().length > 0 && comentario.trim().length < 10" class="text-xs text-red-500 mt-1">
                        Mínimo 10 caracteres ({{ comentario.trim().length }}/10)
                    </p>
                    <div class="flex justify-end space-x-2 mt-2">
                        <button @click="cancelar" class="btn-secondary text-xs">Cancelar</button>
                        <button @click="submitRechazar" class="btn-danger text-xs">Confirmar Rechazo</button>
                    </div>
                </div>
            </div>

            <div v-if="!pendientes.data?.length" class="card p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-gray-400">No hay solicitudes pendientes de aprobación</p>
            </div>

            <Pagination :data="pendientes" />
        </div>
    </AppLayout>
</template>
