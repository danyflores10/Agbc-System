import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermisos() {
    const page = usePage();

    const permisos = computed(() => page.props.auth?.user?.permisos ?? []);
    const roles = computed(() => page.props.auth?.user?.roles ?? []);
    const esAdmin = computed(() => page.props.auth?.user?.es_admin ?? false);

    function tienePermiso(permiso) {
        if (esAdmin.value) return true;
        return permisos.value.includes(permiso);
    }

    function tieneAlgunPermiso(listaPermisos) {
        if (esAdmin.value) return true;
        return listaPermisos.some(p => permisos.value.includes(p));
    }

    function tieneTodosLosPermisos(listaPermisos) {
        if (esAdmin.value) return true;
        return listaPermisos.every(p => permisos.value.includes(p));
    }

    function tieneRol(rol) {
        return roles.value.includes(rol);
    }

    return {
        permisos,
        roles,
        esAdmin,
        tienePermiso,
        tieneAlgunPermiso,
        tieneTodosLosPermisos,
        tieneRol,
    };
}
