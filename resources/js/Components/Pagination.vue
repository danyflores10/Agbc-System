<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    data: Object,
});

const links = computed(() => {
    const raw = props.data?.links || [];
    return raw.map(link => ({
        ...link,
        label: link.label
            ?.replace('pagination.previous', '« Anterior')
            ?.replace('pagination.next', 'Siguiente »')
            ?.replace('&laquo; Previous', '« Anterior')
            ?.replace('Next &raquo;', 'Siguiente »'),
    }));
});
</script>

<template>
    <nav v-if="data && data.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <Link
                v-if="data.prev_page_url"
                :href="data.prev_page_url"
                class="btn-secondary text-sm"
            >
                Anterior
            </Link>
            <Link
                v-if="data.next_page_url"
                :href="data.next_page_url"
                class="btn-secondary text-sm ml-3"
            >
                Siguiente
            </Link>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando <span class="font-semibold">{{ data.from }}</span> a
                    <span class="font-semibold">{{ data.to }}</span> de
                    <span class="font-semibold">{{ data.total }}</span> resultados
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                    <template v-for="(link, index) in links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'relative inline-flex items-center px-3 py-2 text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-brand-blue text-white z-10'
                                    : 'text-gray-500 hover:bg-gray-50',
                                index === 0 ? 'rounded-l-md' : '',
                                index === links.length - 1 ? 'rounded-r-md' : '',
                                'border border-gray-300'
                            ]"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            :class="[
                                'relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 border border-gray-300',
                                index === 0 ? 'rounded-l-md' : '',
                                index === links.length - 1 ? 'rounded-r-md' : '',
                            ]"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </nav>
</template>
