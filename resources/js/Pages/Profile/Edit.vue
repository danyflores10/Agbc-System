<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps({
    usuario: Object,
});

const page = usePage();

const profileForm = useForm({
    nombres: props.usuario.nombres,
    apellidos: props.usuario.apellidos,
    email: props.usuario.email,
    telefono: props.usuario.telefono || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const passwordSuccess = ref(false);
const profileSuccess = ref(false);
const avatarSuccess = ref(false);
const avatarPreview = ref(null);
const avatarInput = ref(null);
const uploadingAvatar = ref(false);

// Toggle de visibilidad para cada campo de contraseña
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const currentAvatarUrl = computed(() => {
    return page.props.auth?.user?.avatar_url || null;
});

// Validaciones de contraseña en tiempo real
const passwordMinLength = computed(() => passwordForm.password.length >= 8);
const passwordsMatch = computed(() => {
    if (!passwordForm.password_confirmation) return null;
    return passwordForm.password === passwordForm.password_confirmation;
});
const passwordStrength = computed(() => {
    const p = passwordForm.password;
    if (!p) return 0;
    let score = 0;
    if (p.length >= 8) score++;
    if (/[A-Z]/.test(p)) score++;
    if (/[a-z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;
    return score;
});
const strengthLabel = computed(() => {
    const labels = ['', 'Muy débil', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'];
    return labels[passwordStrength.value] || '';
});
const strengthColor = computed(() => {
    const colors = ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-emerald-600'];
    return colors[passwordStrength.value] || '';
});

function selectAvatar() {
    avatarInput.value?.click();
}

function onAvatarSelected(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (ev) => { avatarPreview.value = ev.target.result; };
    reader.readAsDataURL(file);

    uploadingAvatar.value = true;
    router.post(route('profile.avatar'), { avatar: file }, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            avatarSuccess.value = true;
            avatarPreview.value = null;
            setTimeout(() => avatarSuccess.value = false, 3000);
        },
        onFinish: () => { uploadingAvatar.value = false; },
    });
}

const { confirmDelete } = useConfirm();

async function removeAvatar() {
    if (!await confirmDelete('La foto de perfil será eliminada.')) return;
    router.delete(route('profile.avatar.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            avatarSuccess.value = true;
            setTimeout(() => avatarSuccess.value = false, 3000);
        },
    });
}

function updateProfile() {
    profileForm.patch(route('profile.update'), {
        onSuccess: () => {
            profileSuccess.value = true;
            setTimeout(() => profileSuccess.value = false, 3000);
        },
    });
}

function updatePassword() {
    passwordForm.put(route('profile.password'), {
        onSuccess: () => {
            passwordForm.reset();
            showCurrentPassword.value = false;
            showNewPassword.value = false;
            showConfirmPassword.value = false;
            passwordSuccess.value = true;
            setTimeout(() => passwordSuccess.value = false, 3000);
        },
    });
}
</script>

<template>
    <Head title="Mi Perfil" />
    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mi Perfil</h1>
                <p class="text-sm text-gray-500">Administre su información personal y contraseña</p>
            </div>

            <!-- Avatar -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Foto de Perfil</h2>
                <div v-if="avatarSuccess" class="text-sm text-green-600 font-medium mb-3">Avatar actualizado correctamente.</div>
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <img v-if="avatarPreview" :src="avatarPreview" class="w-24 h-24 rounded-full object-cover ring-4 ring-brand-gold shadow-lg" />
                        <img v-else-if="currentAvatarUrl" :src="currentAvatarUrl" class="w-24 h-24 rounded-full object-cover ring-4 ring-brand-gold shadow-lg" />
                        <div v-else class="w-24 h-24 rounded-full bg-brand-blue flex items-center justify-center text-white text-3xl font-bold ring-4 ring-brand-gold shadow-lg">
                            {{ usuario.nombres?.charAt(0)?.toUpperCase() || 'U' }}
                        </div>
                        <div v-if="uploadingAvatar" class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center">
                            <svg class="animate-spin w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <input ref="avatarInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onAvatarSelected" />
                        <button type="button" @click="selectAvatar" class="btn-primary text-sm" :disabled="uploadingAvatar">
                            {{ currentAvatarUrl ? 'Cambiar Foto' : 'Subir Foto' }}
                        </button>
                        <button v-if="currentAvatarUrl" type="button" @click="removeAvatar" class="block text-sm text-red-600 hover:text-red-800 transition-colors">
                            Eliminar foto
                        </button>
                        <p class="text-xs text-gray-400">JPG, PNG o WebP. Máx 2 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Info de perfil (solo lectura) -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Nombre completo</p>
                        <p class="text-sm text-gray-900">{{ usuario.nombres }} {{ usuario.apellidos }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Email</p>
                        <p class="text-sm text-gray-900">{{ usuario.email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Teléfono</p>
                        <p class="text-sm text-gray-900">{{ usuario.telefono || '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Cargo</p>
                        <p class="text-sm text-gray-900">{{ usuario.cargo || '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Roles asignados -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Roles Asignados</h2>
                <div v-if="usuario.roles?.length" class="flex flex-wrap gap-2">
                    <span v-for="rol in usuario.roles" :key="rol.id"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                        {{ rol.nombre }}
                    </span>
                </div>
                <p v-else class="text-sm text-gray-500">No tiene roles asignados.</p>
            </div>

            <!-- Centros de Costo asignados -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Centros de Costo Asignados</h2>
                <div v-if="usuario.centros_costo?.length" class="space-y-2">
                    <div v-for="cc in usuario.centros_costo" :key="cc.id"
                        class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-sm text-gray-700">
                            {{ cc.codigo }} - {{ cc.nombre }}
                            <span v-if="cc.area" class="text-gray-400">({{ cc.area.nombre }})</span>
                        </span>
                        <span v-if="cc.pivot?.es_principal" class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                            Principal
                        </span>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500">No tiene centros de costo asignados.</p>
            </div>

            <!-- Editar datos personales -->
            <form @submit.prevent="updateProfile" class="card p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Editar Información</h2>
                <div v-if="profileSuccess" class="text-sm text-green-600 font-medium">Información actualizada correctamente.</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input v-model="profileForm.nombres" type="text" class="input-field" required />
                        <p v-if="profileForm.errors.nombres" class="text-red-500 text-sm mt-1">{{ profileForm.errors.nombres }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                        <input v-model="profileForm.apellidos" type="text" class="input-field" required />
                        <p v-if="profileForm.errors.apellidos" class="text-red-500 text-sm mt-1">{{ profileForm.errors.apellidos }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="profileForm.email" type="email" class="input-field" required />
                        <p v-if="profileForm.errors.email" class="text-red-500 text-sm mt-1">{{ profileForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input v-model="profileForm.telefono" type="text" class="input-field" />
                        <p v-if="profileForm.errors.telefono" class="text-red-500 text-sm mt-1">{{ profileForm.errors.telefono }}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary" :disabled="profileForm.processing">
                        {{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>

            <!-- Cambiar contraseña -->
            <form @submit.prevent="updatePassword" class="card p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Cambiar Contraseña</h2>
                <div v-if="passwordSuccess" class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 font-medium">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Contraseña actualizada correctamente.
                </div>
                <div class="space-y-4">
                    <!-- Contraseña actual -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                        <div class="relative">
                            <input
                                v-model="passwordForm.current_password"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="input-field pr-11"
                                required
                                autocomplete="current-password"
                            />
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-blue transition-colors">
                                <svg v-if="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <!-- Nueva contraseña + Confirmar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                            <div class="relative">
                                <input
                                    v-model="passwordForm.password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    class="input-field pr-11"
                                    required
                                    autocomplete="new-password"
                                    minlength="8"
                                />
                                <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-blue transition-colors">
                                    <svg v-if="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">{{ passwordForm.errors.password }}</p>
                            <!-- Indicador de fortaleza -->
                            <div v-if="passwordForm.password" class="mt-2">
                                <div class="flex gap-1 mb-1">
                                    <div v-for="i in 5" :key="i" :class="[i <= passwordStrength ? strengthColor : 'bg-gray-200']" class="h-1.5 flex-1 rounded-full transition-all duration-300"></div>
                                </div>
                                <p class="text-xs" :class="passwordStrength >= 4 ? 'text-green-600' : passwordStrength >= 3 ? 'text-yellow-600' : 'text-red-500'">
                                    {{ strengthLabel }}
                                    <span v-if="!passwordMinLength" class="text-red-500"> — Mínimo 8 caracteres</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                            <div class="relative">
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    class="input-field pr-11"
                                    :class="{
                                        'border-red-400 focus:border-red-500': passwordsMatch === false,
                                        'border-green-400 focus:border-green-500': passwordsMatch === true,
                                    }"
                                    required
                                    autocomplete="new-password"
                                />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-blue transition-colors">
                                    <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Validación en tiempo real -->
                            <div v-if="passwordForm.password_confirmation" class="mt-1.5 flex items-center gap-1.5">
                                <svg v-if="passwordsMatch" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg v-else class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span :class="passwordsMatch ? 'text-green-600' : 'text-red-500'" class="text-xs font-medium">
                                    {{ passwordsMatch ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="btn-primary"
                        :disabled="passwordForm.processing || passwordsMatch === false || !passwordMinLength"
                    >
                        {{ passwordForm.processing ? 'Actualizando...' : 'CAMBIAR CONTRASEÑA' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
