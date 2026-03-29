<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombres: '',
    apellidos: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const mounted = ref(false);

onMounted(() => {
    setTimeout(() => { mounted.value = true; }, 100);
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />
    <div class="min-h-screen flex">
        <!-- Panel izquierdo -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-brand-blue via-blue-800 to-blue-900 text-white flex-col justify-center items-center p-12 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-72 h-72 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-48 h-48 bg-white/5 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div :class="['max-w-md text-center transition-all duration-1000 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
                <div class="w-28 h-28 mx-auto mb-8 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl">
                    <svg class="w-16 h-16 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h1 class="text-4xl font-extrabold mb-3 tracking-tight">PresupuestoBO</h1>
                <div class="w-16 h-1 bg-brand-gold mx-auto mb-4 rounded-full"></div>
                <p class="text-lg text-blue-200 mb-2 font-medium">Agencia Boliviana de Correos</p>
                <p class="text-sm text-blue-300/80">Sistema Web de Presupuesto y Control Financiero</p>
            </div>
        </div>

        <!-- Panel derecho - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 bg-gray-50">
            <div :class="['w-full max-w-md transition-all duration-700 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']" style="transition-delay: 200ms;">
                <!-- Logo móvil -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-blue to-blue-800 rounded-2xl shadow-xl mb-4">
                        <svg class="w-10 h-10 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-brand-blue">PresupuestoBO</h1>
                    <p class="text-sm text-gray-500">Agencia Boliviana de Correos</p>
                </div>

                <!-- Card del formulario -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Solicitar Acceso</h2>
                    <p class="text-sm text-gray-500 mb-6">Complete el formulario para solicitar acceso al sistema</p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombres</label>
                                <input v-model="form.nombres" type="text" class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white" required autofocus placeholder="Sus nombres" />
                                <p v-if="form.errors.nombres" class="text-red-500 text-xs mt-1">{{ form.errors.nombres }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Apellidos</label>
                                <input v-model="form.apellidos" type="text" class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white" required placeholder="Sus apellidos" />
                                <p v-if="form.errors.apellidos" class="text-red-500 text-xs mt-1">{{ form.errors.apellidos }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo Electrónico</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-blue transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input v-model="form.email" type="email" class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white" required placeholder="correo@agbc.gob.bo" autocomplete="email" />
                            </div>
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contraseña</label>
                            <input v-model="form.password" type="password" class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white" required autocomplete="new-password" placeholder="••••••••" />
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirmar Contraseña</label>
                            <input v-model="form.password_confirmation" type="password" class="w-full py-3 px-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white" required autocomplete="new-password" placeholder="••••••••" />
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-brand-gold to-yellow-500 hover:from-yellow-500 hover:to-brand-gold text-brand-blue font-bold rounded-xl shadow-lg hover:shadow-xl hover:shadow-brand-gold/25 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed text-sm uppercase tracking-wider flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-[0.98]"
                        >
                            <svg v-if="form.processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Registrando...' : 'Solicitar Acceso' }}
                        </button>
                    </form>
                </div>

                <p class="text-center text-xs text-gray-400 mt-6">
                    ¿Ya tiene cuenta? <Link :href="route('login')" class="text-brand-blue hover:underline font-medium">Iniciar Sesión</Link>
                </p>
            </div>
        </div>
    </div>
</template>
