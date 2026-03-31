<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-[#1a365d] via-[#1e4080] to-[#2a5298] px-4">

        <!-- Logo grande con brillo dorado -->
        <div class="mb-8 flex flex-col items-center space-y-4">
            <Link href="/">
                <div class="relative group">
                    <!-- Halo dorado detrás del logo -->
                    <div class="absolute inset-0 rounded-2xl bg-[#F5C518]/25 blur-2xl scale-125 group-hover:scale-150 transition-transform duration-500"></div>
                    <!-- Contenedor del logo -->
                    <div class="relative flex items-center justify-center w-36 h-36 rounded-2xl bg-white/10 border-2 border-[#F5C518]/50 shadow-2xl backdrop-blur-sm p-3">
                        <img src="/images/logoAGBC.png" alt="Logo AGBC" class="w-24 h-24 object-contain drop-shadow-lg" />
                    </div>
                </div>
            </Link>
            <!-- Nombre del sistema -->
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-white tracking-wide drop-shadow">PresupuestoBO</h1>
                <div class="w-12 h-1 bg-[#F5C518] rounded-full mx-auto mt-1.5 mb-1.5"></div>
                <p class="text-[#F5C518] text-sm font-medium tracking-widest uppercase">Agencia Boliviana de Correos</p>
            </div>
        </div>

        <!-- Tarjeta del formulario -->
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white/95 backdrop-blur-md shadow-2xl border border-white/20 px-8 py-7">
            <slot />
        </div>

    </div>
</template>
