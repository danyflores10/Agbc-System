<script setup>
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps({
    usuario: { type: Object, default: null },
    roles: Array,
    centrosCosto: Array,
    rolesSeleccionados: { type: Array, default: () => [] },
    centrosCostoSeleccionados: { type: Array, default: () => [] },
    centroCostoPrincipal: { type: [Number, String], default: null },
});

const isEdit = computed(() => !!props.usuario);
const { confirmAction } = useConfirm();

// Password visibility
const showPassword = ref(false);
const showConfirm = ref(false);

// Avatar
const avatarPreview = ref(props.usuario?.avatar ? `/storage/${props.usuario.avatar}` : null);
const avatarFile = ref(null);
const avatarInput = ref(null);

function onAvatarChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Solo se permiten imágenes JPG, PNG o WebP.');
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('La imagen no debe superar 2 MB.');
        return;
    }
    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
}

async function removeAvatar() {
    const confirmed = await confirmAction({
        title: '¿Eliminar avatar?',
        text: 'Se quitará la foto de perfil de este usuario.',
        type: 'warning',
        confirmText: 'Sí, eliminar',
    });
    if (!confirmed) return;

    if (isEdit.value && props.usuario?.avatar) {
        router.delete(route('usuarios.delete-avatar', props.usuario.id), {
            preserveScroll: true,
            onSuccess: () => {
                avatarPreview.value = null;
                avatarFile.value = null;
            },
        });
    } else {
        avatarPreview.value = null;
        avatarFile.value = null;
        if (avatarInput.value) avatarInput.value.value = '';
    }
}

// Password match
const passwordsTyped = computed(() => form.password.length > 0 || form.password_confirmation.length > 0);
const passwordsMatch = computed(() => form.password.length > 0 && form.password === form.password_confirmation);
const passwordsMismatch = computed(() => form.password_confirmation.length > 0 && form.password !== form.password_confirmation);

const form = useForm({
    nombres: props.usuario?.nombres || '',
    apellidos: props.usuario?.apellidos || '',
    email: props.usuario?.email || '',
    password: '',
    password_confirmation: '',
    telefono: props.usuario?.telefono || '',
    cargo: props.usuario?.cargo || '',
    estado: props.usuario?.estado || 'ACTIVO',
    roles: props.rolesSeleccionados.map(r => r.id ?? r),
    centros_costo: props.centrosCostoSeleccionados.map(c => c.id ?? c),
    centro_costo_principal: props.centroCostoPrincipal,
});

function submit() {
    const data = new FormData();
    Object.keys(form.data()).forEach(key => {
        const val = form[key];
        if (key === 'roles' || key === 'centros_costo') {
            val.forEach(v => data.append(`${key}[]`, v));
        } else if (val !== null && val !== undefined && val !== '') {
            data.append(key, val);
        }
    });

    if (avatarFile.value) {
        data.append('avatar', avatarFile.value);
    }

    if (isEdit.value) {
        data.append('_method', 'PUT');
        router.post(route('usuarios.update', props.usuario.id), data, {
            forceFormData: true,
            onError: (errors) => { form.errors = errors; },
            onProgress: (p) => { form.progress = p; },
            onStart: () => { form.processing = true; },
            onFinish: () => { form.processing = false; },
        });
    } else {
        router.post(route('usuarios.store'), data, {
            forceFormData: true,
            onError: (errors) => { form.errors = errors; },
            onStart: () => { form.processing = true; },
            onFinish: () => { form.processing = false; },
        });
    }
}

function toggleCentroCosto(id) {
    const idx = form.centros_costo.indexOf(id);
    if (idx === -1) {
        form.centros_costo.push(id);
    } else {
        form.centros_costo.splice(idx, 1);
        if (form.centro_costo_principal === id) {
            form.centro_costo_principal = null;
        }
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Editar Usuario' : 'Nuevo Usuario'" />
    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
                <Link :href="route('usuarios.index')" class="btn-secondary">← Volver</Link>
            </div>

            <form @submit.prevent="submit" class="card p-6 space-y-6">
                <!-- Avatar -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Foto de perfil</legend>
                    <div class="flex items-center gap-6">
                        <!-- Preview -->
                        <div class="relative group">
                            <div v-if="avatarPreview"
                                class="w-24 h-24 rounded-full overflow-hidden ring-4 ring-white shadow-lg">
                                <img :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                            </div>
                            <div v-else
                                class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center ring-4 ring-white shadow-lg">
                                <span class="text-3xl font-bold text-white">
                                    {{ form.nombres?.charAt(0)?.toUpperCase() || '?' }}
                                </span>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex flex-col gap-2">
                            <input ref="avatarInput" type="file" accept="image/jpeg,image/png,image/webp"
                                class="hidden" @change="onAvatarChange" />
                            <button type="button" @click="avatarInput?.click()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                {{ avatarPreview ? 'Cambiar foto' : 'Subir foto' }}
                            </button>
                            <button v-if="avatarPreview" type="button" @click="removeAvatar"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Eliminar foto
                            </button>
                            <p class="text-xs text-gray-400">JPG, PNG o WebP. Máx. 2 MB.</p>
                        </div>
                    </div>
                    <p v-if="form.errors.avatar" class="text-red-500 text-sm mt-2">{{ form.errors.avatar }}</p>
                </fieldset>

                <!-- Datos personales -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Datos personales</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                            <input v-model="form.nombres" type="text" class="input-field" maxlength="120" required />
                            <p v-if="form.errors.nombres" class="text-red-500 text-sm mt-1">{{ form.errors.nombres }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                            <input v-model="form.apellidos" type="text" class="input-field" maxlength="120" required />
                            <p v-if="form.errors.apellidos" class="text-red-500 text-sm mt-1">{{ form.errors.apellidos }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="input-field" maxlength="150" required />
                            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input v-model="form.telefono" type="text" class="input-field" maxlength="30" />
                            <p v-if="form.errors.telefono" class="text-red-500 text-sm mt-1">{{ form.errors.telefono }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input v-model="form.cargo" type="text" class="input-field" maxlength="100" />
                            <p v-if="form.errors.cargo" class="text-red-500 text-sm mt-1">{{ form.errors.cargo }}</p>
                        </div>
                    </div>
                </fieldset>

                <!-- Credenciales -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Credenciales</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contraseña
                                <span v-if="isEdit" class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                            </label>
                            <div class="relative">
                                <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                    class="input-field pr-10" :required="!isEdit" autocomplete="new-password" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <!-- Ojo abierto -->
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <!-- Ojo cerrado -->
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                            <div class="relative">
                                <input v-model="form.password_confirmation" :type="showConfirm ? 'text' : 'password'"
                                    class="input-field pr-10" :class="{
                                        'ring-2 ring-green-400 border-green-400': passwordsMatch,
                                        'ring-2 ring-red-400 border-red-400': passwordsMismatch,
                                    }" :required="!isEdit" autocomplete="new-password" />
                                <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg v-if="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Mensajito de coincidencia -->
                            <p v-if="passwordsMatch" class="flex items-center gap-1 text-green-600 text-sm mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Las contraseñas coinciden
                            </p>
                            <p v-else-if="passwordsMismatch" class="flex items-center gap-1 text-red-500 text-sm mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Las contraseñas no coinciden
                            </p>
                        </div>
                    </div>
                </fieldset>

                <!-- Estado -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Estado</legend>
                    <select v-model="form.estado" class="input-field sm:w-64">
                        <option value="ACTIVO">Activo</option>
                        <option value="INACTIVO">Inactivo</option>
                        <option value="BLOQUEADO">Bloqueado</option>
                    </select>
                    <p v-if="form.errors.estado" class="text-red-500 text-sm mt-1">{{ form.errors.estado }}</p>
                </fieldset>

                <!-- Roles -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Roles</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        <label v-for="rol in roles" :key="rol.id" class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" :value="rol.id" v-model="form.roles" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-sm text-gray-700">{{ rol.nombre }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</p>
                </fieldset>

                <!-- Centros de Costo -->
                <fieldset>
                    <legend class="text-lg font-semibold text-gray-800 mb-3">Centros de Costo</legend>
                    <p class="text-sm text-gray-500 mb-3">Seleccione los centros de costo y marque uno como principal.</p>
                    <div class="space-y-2">
                        <div v-for="cc in centrosCosto" :key="cc.id"
                            class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                            <label class="flex items-center space-x-2 cursor-pointer flex-1">
                                <input type="checkbox" :checked="form.centros_costo.includes(cc.id)"
                                    @change="toggleCentroCosto(cc.id)"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-gray-700">
                                    {{ cc.codigo }} - {{ cc.nombre }}
                                    <span v-if="cc.area" class="text-gray-400">({{ cc.area.nombre }})</span>
                                </span>
                            </label>
                            <label v-if="form.centros_costo.includes(cc.id)" class="flex items-center space-x-1 ml-4 cursor-pointer">
                                <input type="radio" :value="cc.id" v-model="form.centro_costo_principal"
                                    class="border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-medium text-gray-500">Principal</span>
                            </label>
                        </div>
                    </div>
                    <p v-if="form.errors.centros_costo" class="text-red-500 text-sm mt-1">{{ form.errors.centros_costo }}</p>
                    <p v-if="form.errors.centro_costo_principal" class="text-red-500 text-sm mt-1">{{ form.errors.centro_costo_principal }}</p>
                </fieldset>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <Link :href="route('usuarios.index')" class="btn-secondary">Cancelar</Link>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear Usuario') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
