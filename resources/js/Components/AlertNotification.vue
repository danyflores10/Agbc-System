<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    type: { type: String, default: 'success', validator: v => ['success', 'error', 'warning', 'info'].includes(v) },
    message: { type: String, default: '' },
    dismissible: { type: Boolean, default: true },
    duration: { type: Number, default: 5000 },
});

const emit = defineEmits(['dismissed']);
const visible = ref(false);
const progress = ref(100);
let timer = null;
let progressTimer = null;

const config = {
    success: {
        bg: 'bg-green-50 border-green-400',
        text: 'text-green-800',
        icon: 'text-green-500',
        progressBg: 'bg-green-500',
        iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    error: {
        bg: 'bg-red-50 border-red-400',
        text: 'text-red-800',
        icon: 'text-red-500',
        progressBg: 'bg-red-500',
        iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    warning: {
        bg: 'bg-yellow-50 border-yellow-400',
        text: 'text-yellow-800',
        icon: 'text-yellow-500',
        progressBg: 'bg-yellow-500',
        iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
    },
    info: {
        bg: 'bg-blue-50 border-blue-400',
        text: 'text-blue-800',
        icon: 'text-blue-500',
        progressBg: 'bg-blue-500',
        iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    },
};

function show() {
    visible.value = true;
    progress.value = 100;
    clearTimeout(timer);
    clearInterval(progressTimer);

    if (props.duration > 0) {
        const step = 50;
        const decrement = (100 / props.duration) * step;
        progressTimer = setInterval(() => {
            progress.value = Math.max(0, progress.value - decrement);
        }, step);

        timer = setTimeout(() => {
            dismiss();
        }, props.duration);
    }
}

function dismiss() {
    visible.value = false;
    clearTimeout(timer);
    clearInterval(progressTimer);
    setTimeout(() => emit('dismissed'), 300);
}

watch(() => props.message, (val) => {
    if (val) show();
});

onMounted(() => {
    if (props.message) show();
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="opacity-0 translate-x-8 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-8 scale-95"
    >
        <div v-if="visible && message" class="relative overflow-hidden rounded-xl border-l-4 shadow-lg backdrop-blur-sm" :class="config[type].bg">
            <div class="flex items-start gap-3 p-4">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="w-6 h-6 animate-bounce-once" :class="config[type].icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="config[type].iconPath" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold" :class="config[type].text">
                        {{ type === 'success' ? '¡Operación Exitosa!' : type === 'error' ? '¡Ocurrió un Error!' : type === 'warning' ? '¡Atención Requerida!' : 'Información del Sistema' }}
                    </p>
                    <p class="text-sm mt-1 leading-relaxed" :class="config[type].text">{{ message }}</p>
                </div>

                <!-- Close button -->
                <button v-if="dismissible" @click="dismiss" class="flex-shrink-0 rounded-lg p-1 transition-colors hover:bg-black/10" :class="config[type].text">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Progress bar -->
            <div v-if="duration > 0" class="h-1 w-full bg-black/5">
                <div class="h-full transition-all duration-100 ease-linear rounded-full" :class="config[type].progressBg" :style="{ width: progress + '%' }"></div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes bounce-once {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
.animate-bounce-once {
    animation: bounce-once 0.5s ease-in-out;
}
</style>
