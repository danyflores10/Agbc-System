<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="loading" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-[2px]">
                <div class="flex flex-col items-center gap-5">
                    <div class="bouncing-loader">
                        <div class="ball blue"></div>
                        <div class="ball red"></div>
                        <div class="ball yellow"></div>
                        <div class="ball green"></div>
                    </div>
                    <p class="text-sm font-semibold text-white tracking-wider drop-shadow-lg">Cargando...</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const loading = ref(false);
let timeout = null;

onMounted(() => {
    router.on('start', () => {
        timeout = setTimeout(() => { loading.value = true; }, 150);
    });
    router.on('finish', () => {
        clearTimeout(timeout);
        loading.value = false;
    });
});

onUnmounted(() => {
    clearTimeout(timeout);
});
</script>

<style scoped>
.bouncing-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    height: 50px;
}

.ball {
    --size: 18px;
    width: var(--size);
    height: var(--size);
    border-radius: 50%;
    animation: bounce 2s ease infinite;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.blue {
    background-color: #4285f5;
}

.red {
    background-color: #ea4436;
    animation-delay: 0.25s;
}

.yellow {
    background-color: #fbbd06;
    animation-delay: 0.5s;
}

.green {
    background-color: #34a952;
    animation-delay: 0.75s;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-25px);
    }
}
</style>
