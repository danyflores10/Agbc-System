<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const loading = ref(true);
const iframeRef = ref(null);

function onIframeLoad() {
    loading.value = false;
}

onMounted(() => {
    setTimeout(() => { loading.value = false; }, 5000);
});
</script>

<template>
    <Head title="Laravel Pulse - Monitoreo" />
    <AppLayout>
        <div class="flex flex-col h-[calc(100vh-3.5rem)]">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Laravel Pulse</h1>
                            <p class="text-xs text-gray-500">Monitoreo en tiempo real del sistema</p>
                        </div>
                    </div>
                    <a href="/pulse" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Abrir en nueva pestaña
                    </a>
                </div>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-purple-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">Cargando panel de monitoreo...</p>
                </div>
            </div>

            <!-- Pulse iframe -->
            <iframe
                ref="iframeRef"
                src="/pulse"
                :class="['flex-1 w-full border-0', loading ? 'hidden' : '']"
                @load="onIframeLoad"
            ></iframe>
        </div>
    </AppLayout>
</template>
