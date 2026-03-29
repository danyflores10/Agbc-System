<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const mounted = ref(false);

onMounted(() => {
    setTimeout(() => { mounted.value = true; }, 100);
});

const submit = () => {
    sessionStorage.setItem('loginAnimation', '1');
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: () => {
            sessionStorage.removeItem('loginAnimation');
        },
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />
    <div class="min-h-screen flex">
        <!-- Panel izquierdo con diseño geométrico profesional -->
        <div class="hidden lg:flex lg:w-1/2 text-white flex-col justify-center items-center p-12 relative overflow-hidden" style="background: linear-gradient(135deg, #0a1628 0%, #1a365d 30%, #1e40af 60%, #2563eb 100%);">

            <!-- Formas geométricas decorativas estilo diamante -->
            <!-- Diamante grande izquierdo -->
            <div class="absolute -left-20 top-1/4 w-80 h-80 rotate-45 rounded-3xl opacity-20" style="background: linear-gradient(135deg, #F5C518 0%, #d4a017 100%);"></div>
            <div class="absolute -left-24 top-[22%] w-80 h-80 rotate-45 rounded-3xl opacity-10 border-4 border-brand-gold"></div>

            <!-- Línea dorada diagonal gruesa -->
            <div class="absolute -left-10 top-0 w-32 h-[140%] -rotate-[35deg] opacity-15" style="background: linear-gradient(180deg, transparent 0%, #F5C518 20%, #d4a017 50%, #F5C518 80%, transparent 100%);"></div>
            <div class="absolute left-20 -top-10 w-24 h-[140%] -rotate-[35deg] opacity-10" style="background: linear-gradient(180deg, transparent 0%, #F5C518 30%, #d4a017 60%, transparent 100%);"></div>

            <!-- Formas geométricas azules superpuestas -->
            <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-blue-400/10"></div>
            <div class="absolute right-20 bottom-32 w-40 h-40 rounded-full bg-blue-300/8"></div>
            <div class="absolute -right-8 top-16 w-48 h-48 rotate-45 rounded-2xl bg-white/5 border border-white/10"></div>

            <!-- Diamantes pequeños decorativos -->
            <div class="absolute top-16 left-1/3 w-12 h-12 rotate-45 bg-brand-gold/20 rounded-md"></div>
            <div class="absolute bottom-24 left-1/4 w-8 h-8 rotate-45 bg-brand-gold/15 rounded-sm"></div>
            <div class="absolute top-1/2 right-12 w-6 h-6 rotate-45 bg-white/10 rounded-sm"></div>

            <!-- Línea luminosa vertical sutil -->
            <div class="absolute left-1/2 top-0 w-px h-full bg-gradient-to-b from-transparent via-brand-gold/20 to-transparent"></div>

            <!-- Contenido central -->
            <div :class="['max-w-md text-center relative z-10 transition-all duration-1000 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10']">
                <!-- Logo con efecto glow -->
                <div class="relative mx-auto mb-8 w-36 h-36">
                    <div class="absolute inset-0 bg-brand-gold/20 rounded-3xl blur-xl animate-pulse"></div>
                    <div class="relative w-36 h-36 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl p-4">
                        <img src="/images/Logooriginal.png" alt="Logo AGBC" class="w-full h-full object-contain drop-shadow-[0_0_15px_rgba(245,197,24,0.3)]" />
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold mb-3 tracking-tight drop-shadow-lg">PresupuestoBO</h1>
                <div class="w-20 h-1.5 bg-gradient-to-r from-transparent via-brand-gold to-transparent mx-auto mb-4 rounded-full"></div>
                <p class="text-lg text-blue-200 mb-2 font-medium">Agencia Boliviana de Correos</p>
                <p class="text-sm text-blue-300/80">Sistema Web de Presupuesto y Control Financiero</p>
            </div>
        </div>

        <!-- Panel derecho - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-8 bg-gray-50 relative">
            <div :class="['w-full max-w-md transition-all duration-700 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8']" style="transition-delay: 200ms;">
                <!-- Logo móvil -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-brand-blue to-blue-800 rounded-2xl shadow-xl mb-4 p-2">
                        <img src="/images/Logooriginal.png" alt="Logo AGBC" class="w-full h-full object-contain" />
                    </div>
                    <h1 class="text-2xl font-bold text-brand-blue">PresupuestoBO</h1>
                    <p class="text-sm text-gray-500">Agencia Boliviana de Correos</p>
                </div>

                <!-- Card del formulario -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Bienvenido</h2>
                    <p class="text-sm text-gray-500 mb-6">Ingrese sus credenciales para acceder al sistema</p>

                    <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Email -->
                        <div :class="['transition-all duration-500 ease-out', mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-6']" style="transition-delay: 400ms;">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Correo Electrónico</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-blue transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    maxlength="200"
                                    class="w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white focus:shadow-[0_0_0_3px_rgba(245,197,24,0.15)]"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.email }"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="correo@agbc.gob.bo"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div :class="['transition-all duration-500 ease-out', mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-6']" style="transition-delay: 550ms;">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Contraseña</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-blue transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    maxlength="50"
                                    class="w-full pl-11 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold transition-all duration-300 text-sm bg-gray-50 focus:bg-white focus:shadow-[0_0_0_3px_rgba(245,197,24,0.15)]"
                                    :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.password }"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-brand-blue transition-colors">
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember & Forgot -->
                        <div :class="['flex items-center justify-between transition-all duration-500 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4']" style="transition-delay: 700ms;">
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-brand-blue focus:ring-brand-blue w-4 h-4" />
                                <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Recordarme</span>
                            </label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-brand-blue hover:text-brand-blue-light font-medium hover:underline transition-colors">
                                ¿Olvidó su contraseña?
                            </Link>
                        </div>

                        <!-- Submit -->
                        <div :class="['transition-all duration-500 ease-out', mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4']" style="transition-delay: 850ms;">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="group w-full py-3.5 px-4 bg-gradient-to-r from-brand-gold to-yellow-500 hover:from-yellow-500 hover:to-brand-gold text-brand-blue font-bold rounded-xl shadow-lg hover:shadow-xl hover:shadow-brand-gold/25 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed text-sm uppercase tracking-wider flex items-center justify-center gap-2 transform hover:scale-[1.02] active:scale-[0.98]"
                            >
                                <svg v-if="form.processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                {{ form.processing ? 'Ingresando...' : 'Iniciar Sesión' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <p :class="['text-center text-xs text-gray-400 mt-6 transition-all duration-500', mounted ? 'opacity-100' : 'opacity-0']" style="transition-delay: 1s;">
                    &copy; {{ new Date().getFullYear() }} Agencia Boliviana de Correos &mdash; Todos los derechos reservados
                </p>
            </div>
        </div>
    </div>
</template>
