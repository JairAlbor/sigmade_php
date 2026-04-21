/**
 * Utilidades globales para notificaciones Premium con SweetAlert2
 * SIGMADE - Modernization Layer
 */

const SIGMADE_UI = {
    colors: {
        crimson: '#a82035',
        gray: '#6b7280',
        success: '#10b981',
        warning: '#f59e0b',
        info: '#3b82f6'
    },

    /**
     * Muestra una alerta de éxito
     * @param {string} title 
     * @param {string} text 
     * @param {number} timer Tiempo en ms (0 para manual)
     */
    success: function(title, text, timer = 2000) {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            timer: timer > 0 ? timer : null,
            confirmButtonColor: this.colors.crimson,
            showConfirmButton: timer === 0
        });
    },

    /**
     * Muestra una alerta de error
     */
    error: function(title, text) {
        return Swal.fire({
            icon: 'error',
            title: title || 'Atención',
            text: text || 'Ocurrió un error inesperado',
            confirmButtonColor: this.colors.crimson
        });
    },

    /**
     * Muestra una advertencia o validación faltante
     */
    warning: function(title, text) {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: this.colors.crimson
        });
    },

    /**
     * Modal de confirmación para acciones críticas
     */
    confirm: function(title, text, confirmText = 'Sí, continuar') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: this.colors.crimson,
            cancelButtonColor: this.colors.gray,
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar'
        });
    }
};
