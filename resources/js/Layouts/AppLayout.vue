<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import AlertNotification from '@/Components/AlertNotification.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);

const sidebarOpen = ref(true);
const mobileMenuOpen = ref(false);
const isFullscreen = ref(false);
const searchQuery = ref('');

// Login transition animation
const showLoginTransition = ref(false);
const transitionPhase = ref('closed'); // 'closed' | 'opening' | 'done'

onMounted(() => {
    if (sessionStorage.getItem('loginAnimation')) {
        sessionStorage.removeItem('loginAnimation');
        showLoginTransition.value = true;
        transitionPhase.value = 'closed';
        // Small delay then open the curtains
        setTimeout(() => { transitionPhase.value = 'opening'; }, 600);
        // Remove the overlay after animation completes
        setTimeout(() => { showLoginTransition.value = false; }, 2400);
    }
});

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        isFullscreen.value = true;
    } else {
        document.exitFullscreen();
        isFullscreen.value = false;
    }
}

const menuItems = [
    {
        title: 'Dashboard',
        icon: 'dashboard',
        route: 'dashboard',
        active: 'dashboard',
    },
    {
        title: 'PRESUPUESTOS',
        isHeader: true,
    },
    {
        title: 'Gestiones',
        icon: 'calendar',
        route: 'gestiones.index',
        active: 'gestiones.*',
    },
    {
        title: 'Partidas Presupuestarias',
        icon: 'list',
        route: 'partidas.index',
        active: 'partidas.*',
    },
    {
        title: 'Presupuestos',
        icon: 'money',
        route: 'presupuestos.index',
        active: 'presupuestos.*',
        children: [
            { title: 'Lista', route: 'presupuestos.index' },
            { title: 'Nuevo Presupuesto', route: 'presupuestos.create' },
        ],
    },
    {
        title: 'GASTOS',
        isHeader: true,
    },
    {
        title: 'Solicitudes de Gasto',
        icon: 'request',
        route: 'solicitudes.index',
        active: 'solicitudes.*',
        children: [
            { title: 'Lista', route: 'solicitudes.index' },
            { title: 'Nueva Solicitud', route: 'solicitudes.create' },
        ],
    },
    {
        title: 'Aprobaciones',
        icon: 'check',
        route: 'aprobaciones.index',
        active: 'aprobaciones.*',
    },
    {
        title: 'Ejecuciones',
        icon: 'execute',
        route: 'ejecuciones.index',
        active: 'ejecuciones.*',
    },
    {
        title: 'ORGANIZACIÓN',
        isHeader: true,
    },
    {
        title: 'Áreas',
        icon: 'building',
        route: 'areas.index',
        active: 'areas.*',
    },
    {
        title: 'Sucursales',
        icon: 'location',
        route: 'sucursales.index',
        active: 'sucursales.*',
    },
    {
        title: 'Centros de Costo',
        icon: 'money',
        route: 'centros-costo.index',
        active: 'centros-costo.*',
    },
    {
        title: 'Proveedores',
        icon: 'users',
        route: 'proveedores.index',
        active: 'proveedores.*',
    },
    {
        title: 'REPORTES',
        isHeader: true,
    },
    {
        title: 'Reportes',
        icon: 'chart',
        route: 'reportes.index',
        active: 'reportes.*',
        children: [
            { title: 'General', route: 'reportes.index' },
            { title: 'Por Área', route: 'reportes.por-area' },
            { title: 'Por Sucursal', route: 'reportes.por-sucursal' },
            { title: 'Comparativo', route: 'reportes.comparativo' },
            { title: 'Por Mes', route: 'reportes.por-mes' },
        ],
    },
    {
        title: 'SISTEMA',
        isHeader: true,
    },

    {
        title: 'Auditoría',
        icon: 'shield',
        route: 'auditoria.index',
        active: 'auditoria.*',
    },
    {
        title: 'Laravel Pulse',
        icon: 'chart',
        route: 'sistema.pulse',
        active: 'sistema.pulse',
        adminOnly: true,
    },
    {
        title: 'Roles y Permisos',
        icon: 'shield',
        route: 'roles.index',
        active: 'roles.*',
    },
    {
        title: 'Usuarios',
        icon: 'users',
        route: 'usuarios.index',
        active: 'usuarios.*',
    },
];

const filteredMenuItems = computed(() => {
    const isAdmin = user.value?.es_admin;
    const query = searchQuery.value.trim().toLowerCase();

    // Primero filtrar por permisos
    let items = menuItems.filter(item => {
        if (item.adminOnly && !isAdmin) return false;
        return true;
    });

    // Si no hay búsqueda, devolver todo
    if (!query) return items;

    // Filtrar por búsqueda: sólo items que coincidan (y sus headers)
    const matched = [];
    let lastHeader = null;
    let headerAdded = false;

    for (const item of items) {
        if (item.isHeader) {
            lastHeader = item;
            headerAdded = false;
            continue;
        }

        const titleMatch = item.title?.toLowerCase().includes(query);
        const childMatch = item.children?.some(c => c.title.toLowerCase().includes(query));

        if (titleMatch || childMatch) {
            if (lastHeader && !headerAdded) {
                matched.push(lastHeader);
                headerAdded = true;
            }
            matched.push(item);
        }
    }

    return matched;
});

const openSubmenus = ref({});

function toggleSubmenu(title) {
    openSubmenus.value[title] = !openSubmenus.value[title];
}

function isActive(pattern) {
    if (!pattern) return false;
    return route().current(pattern);
}

function logout() {
    router.post(route('logout'));
}

const iconMap = {
    dashboard: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
    calendar: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
    list: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
    money: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    request: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    check: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    execute: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
    building: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`,
    location: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
    chart: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`,
    bell: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`,
    shield: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`,
    users: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`,
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Loading Spinner -->
        <LoadingSpinner />

        <!-- Login Transition - Golden Curtain Effect -->
        <Teleport to="body">
            <div v-if="showLoginTransition" class="login-curtain-overlay">
                <!-- Left curtain -->
                <div :class="['login-curtain login-curtain-left', transitionPhase === 'opening' ? 'curtain-open-left' : '']"></div>
                <!-- Right curtain -->
                <div :class="['login-curtain login-curtain-right', transitionPhase === 'opening' ? 'curtain-open-right' : '']"></div>
                <!-- Center content -->
                <div :class="['login-curtain-center', transitionPhase === 'opening' ? 'curtain-center-fade' : '']">
                    <div class="login-curtain-logo">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="login-curtain-text">Bienvenido</p>
                    <p class="login-curtain-subtext">Accediendo al sistema...</p>
                    <!-- Decorative particles -->
                    <div class="login-particles">
                        <span v-for="i in 12" :key="i" class="particle" :style="{ '--i': i }"></span>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Flash Messages -->
        <div class="fixed top-4 right-4 z-50 space-y-3 w-96 max-w-[calc(100vw-2rem)]">
            <AlertNotification
                type="success"
                :message="flash?.success"
                :duration="5000"
            />
            <AlertNotification
                type="error"
                :message="flash?.error"
                :duration="7000"
            />
        </div>

        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-brand-gold flex items-center justify-between px-4 h-14 shadow-lg border-b border-sidebar-dark">
            <div class="flex items-center space-x-3">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-brand-blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="text-brand-blue font-bold text-lg">PresupuestoBO</span>
            </div>
            <div class="flex items-center space-x-3">

            </div>
        </div>

        <!-- Sidebar overlay (mobile) -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden" @click="mobileMenuOpen = false"></div>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed top-0 left-0 z-40 h-screen flex flex-col transition-all duration-300',
                sidebarOpen ? 'w-64' : 'w-20',
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            class="bg-sidebar border-r border-sidebar-dark"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 bg-brand-blue flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span v-if="sidebarOpen" class="text-white font-bold text-lg">PresupuestoBO</span>
                    <span v-else class="text-white font-bold text-base">PBO</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hidden lg:block hover:bg-brand-blue-light rounded p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

          

            <!-- Search (when expanded) -->
            <div v-if="sidebarOpen" class="px-3 py-2 flex-shrink-0">
                <div class="relative">
                    <input v-model="searchQuery" type="text" placeholder="Buscar en menú..." class="w-full text-sm rounded-lg border-0 bg-white/80 py-2 pl-8 pr-3 text-sidebar-text placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-brand-blue">
                    <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="px-2 pb-4 flex-1 min-h-0 overflow-y-auto">
                <template v-for="item in filteredMenuItems" :key="item.title">
                    <!-- Section Header -->
                    <div v-if="item.isHeader && sidebarOpen" class="px-3 py-2 mt-4 first:mt-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-sidebar-text/60">{{ item.title }}</span>
                    </div>
                    <div v-else-if="item.isHeader && !sidebarOpen" class="border-t border-sidebar-dark my-2 mx-2"></div>

                    <!-- Regular Menu Item -->
                    <div v-else>
                        <!-- External link (non-Inertia) -->
                        <a
                            v-if="item.external"
                            :href="item.href"
                            target="_blank"
                            :class="[
                                'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 mb-0.5',
                                'text-sidebar-text hover:bg-sidebar-dark/50'
                            ]"
                        >
                            <span v-html="iconMap[item.icon]" class="flex-shrink-0"></span>
                            <span v-if="sidebarOpen" class="ml-3">{{ item.title }}</span>
                            <svg v-if="sidebarOpen" class="w-3.5 h-3.5 ml-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>

                        <!-- Item without children -->
                        <Link
                            v-else-if="!item.children"
                            :href="route(item.route)"
                            :class="[
                                'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 mb-0.5',
                                isActive(item.active)
                                    ? 'bg-brand-blue text-white shadow-md'
                                    : 'text-sidebar-text hover:bg-sidebar-dark/50'
                            ]"
                        >
                            <span v-html="iconMap[item.icon]" class="flex-shrink-0"></span>
                            <span v-if="sidebarOpen" class="ml-3">{{ item.title }}</span>
                            <span v-if="item.badge && item.badge.value > 0 && sidebarOpen" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ item.badge.value }}</span>
                        </Link>

                        <!-- Item with children -->
                        <div v-else>
                            <button
                                @click="toggleSubmenu(item.title)"
                                :class="[
                                    'w-full flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 mb-0.5',
                                    isActive(item.active)
                                        ? 'bg-brand-blue text-white shadow-md'
                                        : 'text-sidebar-text hover:bg-sidebar-dark/50'
                                ]"
                            >
                                <span v-html="iconMap[item.icon]" class="flex-shrink-0"></span>
                                <span v-if="sidebarOpen" class="ml-3 flex-1 text-left">{{ item.title }}</span>
                                <svg v-if="sidebarOpen" :class="['w-4 h-4 transition-transform', openSubmenus[item.title] ? 'rotate-90' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div v-if="openSubmenus[item.title] && sidebarOpen" class="ml-8 space-y-0.5">
                                <Link
                                    v-for="child in item.children"
                                    :key="child.route"
                                    :href="route(child.route)"
                                    class="block px-3 py-2 rounded-lg text-xs font-medium text-sidebar-text/80 hover:bg-sidebar-dark/50 hover:text-sidebar-text transition-colors"
                                >
                                    {{ child.title }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- User Info at bottom -->
            <div class="border-t border-sidebar-dark bg-sidebar-dark/30 p-3 flex-shrink-0">
                <div class="flex items-center">
                    <img v-if="user?.avatar_url" :src="user.avatar_url" :alt="user.nombre_completo" class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-brand-gold" />
                    <div v-else class="w-9 h-9 rounded-full bg-brand-blue flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ user?.nombre_completo?.charAt(0)?.toUpperCase() || 'U' }}
                    </div>
                    <div v-if="sidebarOpen" class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-sidebar-text truncate">{{ user?.nombre_completo }}</p>
                        <p class="text-xs text-sidebar-text/60 truncate">{{ user?.roles?.[0] || 'Usuario' }}</p>
                    </div>
                    <button v-if="sidebarOpen" @click="logout" class="ml-2 text-sidebar-text/60 hover:text-red-600 transition-colors" title="Cerrar sesión">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main :class="['transition-all duration-300 min-h-screen flex flex-col', sidebarOpen ? 'lg:ml-64' : 'lg:ml-20']" class="pt-14 lg:pt-0">
            <!-- Top Bar - Amarillo permanente -->
            <header class="bg-brand-gold shadow-lg border-b border-sidebar-dark hidden lg:block sticky top-0 z-30">
                <div class="flex items-center justify-between h-14 px-6">
                    <div class="text-brand-blue font-bold text-lg">
                        <slot name="header" />
                    </div>
                    <div class="flex items-center space-x-4">
                        <button @click="toggleFullscreen" class="p-2 rounded-lg text-brand-blue hover:bg-sidebar-dark/30 transition-colors" title="Pantalla completa">
                            <svg v-if="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5v4m0-4l5 5m6-5h4v4m0-4l-5 5M9 20H5v-4m0 4l5-5m6 5h4v-4m0 4l-5-5"/></svg>
                        </button>
                        <Link :href="route('profile.edit')" class="flex items-center space-x-3 text-sm text-brand-blue hover:text-brand-blue-light transition-colors">
                            <img v-if="user?.avatar_url" :src="user.avatar_url" :alt="user.nombre_completo" class="w-9 h-9 rounded-full object-cover shadow ring-2 ring-brand-gold" />
                            <div v-else class="w-9 h-9 rounded-full bg-brand-blue flex items-center justify-center text-white font-bold text-sm shadow">
                                {{ user?.nombre_completo?.charAt(0)?.toUpperCase() || 'U' }}
                            </div>
                            <span class="font-semibold">{{ user?.nombre_completo }}</span>
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 lg:p-6 flex-1">
                <slot />
            </div>

            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-white px-6 py-3 text-center text-xs text-gray-500">
                Todos los derechos reservados <strong>Agencia Boliviana de Correos</strong> &copy; {{ new Date().getFullYear() }}
            </footer>
        </main>
    </div>
</template>

<style scoped>
/* Login Curtain Transition */
.login-curtain-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    pointer-events: all;
    overflow: hidden;
}

.login-curtain {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 50%;
    background: linear-gradient(135deg, #1a365d 0%, #1e40af 50%, #1a365d 100%);
    transition: transform 1.2s cubic-bezier(0.77, 0, 0.175, 1);
    z-index: 2;
}

.login-curtain-left {
    left: 0;
    transform: translateX(0);
    border-right: 2px solid #F5C518;
}

.login-curtain-right {
    right: 0;
    transform: translateX(0);
    border-left: 2px solid #F5C518;
}

.curtain-open-left {
    transform: translateX(-105%);
}

.curtain-open-right {
    transform: translateX(105%);
}

.login-curtain-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 3;
    transition: opacity 0.8s ease;
}

.curtain-center-fade {
    opacity: 0;
    transition: opacity 0.6s ease 0.3s;
}

.login-curtain-logo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F5C518, #f59e0b);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: logoPulse 1.5s ease-in-out infinite, logoEntry 0.6s ease-out;
    box-shadow: 0 0 40px rgba(245, 197, 24, 0.5), 0 0 80px rgba(245, 197, 24, 0.2);
}

.login-curtain-text {
    margin-top: 24px;
    font-size: 2rem;
    font-weight: 800;
    color: white;
    letter-spacing: 0.05em;
    animation: textEntry 0.5s ease-out 0.2s both;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.login-curtain-subtext {
    margin-top: 8px;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    animation: textEntry 0.5s ease-out 0.4s both;
}

/* Decorative particles */
.login-particles {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #F5C518;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    opacity: 0;
    animation: particleBurst 1.8s ease-out 0.1s both;
}

.particle:nth-child(odd) {
    background: rgba(255, 255, 255, 0.8);
    width: 4px;
    height: 4px;
}

.particle:nth-child(1)  { animation-delay: 0.05s; --tx: 120px;  --ty: 0; }
.particle:nth-child(2)  { animation-delay: 0.1s;  --tx: 85px;   --ty: 85px; }
.particle:nth-child(3)  { animation-delay: 0.15s; --tx: 0;      --ty: 120px; }
.particle:nth-child(4)  { animation-delay: 0.2s;  --tx: -85px;  --ty: 85px; }
.particle:nth-child(5)  { animation-delay: 0.25s; --tx: -120px; --ty: 0; }
.particle:nth-child(6)  { animation-delay: 0.3s;  --tx: -85px;  --ty: -85px; }
.particle:nth-child(7)  { animation-delay: 0.35s; --tx: 0;      --ty: -120px; }
.particle:nth-child(8)  { animation-delay: 0.4s;  --tx: 85px;   --ty: -85px; }
.particle:nth-child(9)  { animation-delay: 0.45s; --tx: 105px;  --ty: 50px; }
.particle:nth-child(10) { animation-delay: 0.5s;  --tx: -105px; --ty: 50px; }
.particle:nth-child(11) { animation-delay: 0.55s; --tx: 50px;   --ty: -105px; }
.particle:nth-child(12) { animation-delay: 0.6s;  --tx: -50px;  --ty: -105px; }

@keyframes logoPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(245, 197, 24, 0.5), 0 0 80px rgba(245, 197, 24, 0.2); }
    50% { transform: scale(1.05); box-shadow: 0 0 60px rgba(245, 197, 24, 0.6), 0 0 100px rgba(245, 197, 24, 0.3); }
}

@keyframes logoEntry {
    from { transform: scale(0) rotate(-180deg); opacity: 0; }
    to { transform: scale(1) rotate(0deg); opacity: 1; }
}

@keyframes textEntry {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes particleBurst {
    0% { transform: translate(0, 0) scale(0); opacity: 1; }
    50% { opacity: 1; }
    100% {
        transform: translate(var(--tx), var(--ty)) scale(1);
        opacity: 0;
    }
}
</style>
