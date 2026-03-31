import Swal from 'sweetalert2';

// Barra tricolor Bolivia: rojo, amarillo, verde
const tricolorBar = `
    <div style="position:absolute;top:0;left:0;right:0;display:flex;height:6px;border-radius:16px 16px 0 0;overflow:hidden;">
        <div style="flex:1;background:#dc2626;"></div>
        <div style="flex:1;background:#F5C518;"></div>
        <div style="flex:1;background:#16a34a;"></div>
    </div>
`;

// Iconos SVG con fondo circular
function iconCircle(svg, bgColor) {
    return `
        <div style="display:flex;justify-content:center;margin:12px 0 8px 0;">
            <div style="width:72px;height:72px;border-radius:50%;background:${bgColor};display:flex;align-items:center;justify-content:center;box-shadow:0 4px 15px ${bgColor}66;">
                ${svg}
            </div>
        </div>
    `;
}

const svgs = {
    delete: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>`,
    warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
    success: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
    error: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
    info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
};

const bgColors = {
    delete: '#dc2626',
    warning: '#F5C518',
    success: '#16a34a',
    error: '#dc2626',
    info: '#1a365d',
};

const titleColors = {
    delete: '#dc2626',
    warning: '#92400e',
    success: '#15803d',
    error: '#dc2626',
    info: '#1a365d',
};

export function useConfirm() {
    async function confirmAction({
        title = '¿Está seguro?',
        text = 'Esta acción no se puede deshacer.',
        type = 'delete',
        confirmText = 'Sí, eliminar',
        cancelText = 'Cancelar',
    } = {}) {
        const bg = bgColors[type] || bgColors.delete;
        const titleColor = titleColors[type] || titleColors.delete;

        const result = await Swal.fire({
            html: `
                ${tricolorBar}
                ${iconCircle(svgs[type] || svgs.delete, bg)}
                <div style="font-size:1.25rem;font-weight:800;color:${titleColor};margin:4px 0 6px 0;letter-spacing:-0.01em;">${title}</div>
                <div style="font-size:0.875rem;color:#6b7280;line-height:1.6;max-width:320px;margin:0 auto;">${text}</div>
            `,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: bg,
            cancelButtonColor: '#e5e7eb',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'swal-modern-popup',
                confirmButton: 'swal-modern-confirm',
                cancelButton: 'swal-modern-cancel',
                actions: 'swal-modern-actions',
            },
            buttonsStyling: true,
            showClass: { popup: 'swal-animate-in' },
            hideClass: { popup: 'swal-animate-out' },
        });

        return result.isConfirmed;
    }

    async function confirmDelete(text = 'El registro será eliminado permanentemente.') {
        return confirmAction({
            title: '¿Eliminar registro?',
            text,
            type: 'delete',
            confirmText: 'Sí, eliminar',
        });
    }

    async function confirmWarning({ title, text, confirmText = 'Continuar' } = {}) {
        return confirmAction({
            title,
            text,
            type: 'warning',
            confirmText,
        });
    }

    function showSuccess(title = '¡Operación exitosa!', text = 'Los cambios se han guardado correctamente.') {
        return Swal.fire({
            html: `
                ${tricolorBar}
                ${iconCircle(svgs.success, bgColors.success)}
                <div style="font-size:1.25rem;font-weight:800;color:#15803d;margin:4px 0 6px 0;">${title}</div>
                <div style="font-size:0.875rem;color:#6b7280;line-height:1.6;max-width:320px;margin:0 auto;">${text}</div>
            `,
            confirmButtonColor: bgColors.success,
            confirmButtonText: 'Aceptar',
            timer: 2500,
            timerProgressBar: true,
            customClass: {
                popup: 'swal-modern-popup',
                confirmButton: 'swal-modern-confirm',
                timerProgressBar: 'swal-modern-timer',
            },
            showClass: { popup: 'swal-animate-in' },
            hideClass: { popup: 'swal-animate-out' },
        });
    }

    function showError(title = 'Error', text = 'Ocurrió un error inesperado.') {
        return Swal.fire({
            html: `
                ${tricolorBar}
                ${iconCircle(svgs.error, bgColors.error)}
                <div style="font-size:1.25rem;font-weight:800;color:#dc2626;margin:4px 0 6px 0;">${title}</div>
                <div style="font-size:0.875rem;color:#6b7280;line-height:1.6;max-width:320px;margin:0 auto;">${text}</div>
            `,
            confirmButtonColor: bgColors.error,
            confirmButtonText: 'Aceptar',
            customClass: {
                popup: 'swal-modern-popup',
                confirmButton: 'swal-modern-confirm',
            },
            showClass: { popup: 'swal-animate-in' },
            hideClass: { popup: 'swal-animate-out' },
        });
    }

    return { confirmAction, confirmDelete, confirmWarning, showSuccess, showError };
}
