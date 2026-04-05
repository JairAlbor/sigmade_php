/**
 * ARCHIVO: modal-catalogo.js
 * Descripción: Control de apertura, cierre y llenado de datos para los modales de SIGMADE.
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. GESTIÓN DE BIENVENIDA (LocalStorage)
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    const saludoElemento = document.getElementById('userName');
    if (nombreGuardado && saludoElemento) {
        saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
    }

    // 2. ELEMENTOS DE LOS MODALES
    const modalNuevo = document.getElementById('modal-Nuevo');
    const modalAct = document.getElementById('modal-Actualizar');
    
    // 3. BOTONES DE APERTURA (MODAL NUEVO)
    const btnAbrirNuevo = document.getElementById('btn-abrir-formulario');
    if (btnAbrirNuevo) {
        btnAbrirNuevo.addEventListener('click', () => {
            modalNuevo.classList.remove('hidden');
        });
    }

    // 4. FUNCIÓN GLOBAL: ABRIR MODAL EDICIÓN (Método Tradicional)
    // Se llama desde el atributo onclick del botón en el PHP
    window.abrirModalEdicion = function(id, nombre, id_dis, estado, tipo, disp) {
        
        // A. Insertar el ID en el input oculto (CRÍTICO para el WHERE del SQL)
        const inputId = document.getElementById('idMaterialAct');
        if (inputId) inputId.value = id;

        // B. Llenar los campos visibles del formulario de actualización
        document.getElementById('nombreArticuloAct').value = nombre;
        document.getElementById('disciplinaAct').value = id_dis;
        document.getElementById('estadoAct').value = estado;
        document.getElementById('tipoMaterialAct').value = tipo;
        document.getElementById('disponibilidadAct').value = disp;

        // C. Mostrar el modal de actualización
        if (modalAct) {
            modalAct.classList.remove('hidden');
        }
    };

    // 5. LÓGICA DE CIERRE (PARA AMBOS MODALES)
    // Busca todos los botones que tengan la clase 'btn-cerrar'
    const botonesCerrar = document.querySelectorAll('.btn-cerrar');
    
    botonesCerrar.forEach(btn => {
        btn.addEventListener('click', () => {
            cerrarModales();
        });
    });

    // Función auxiliar para cerrar y limpiar
    function cerrarModales() {
        if (modalNuevo) modalNuevo.classList.add('hidden');
        if (modalAct) modalAct.classList.add('hidden');
        
        // Opcional: Resetea los formularios al cerrar para que no queden datos viejos
        const f1 = document.getElementById('formArticulo');
        const f2 = document.getElementById('formArticuloAct');
        if (f1) f1.reset();
        if (f2) f2.reset();
    }

    // 6. CERRAR CON TECLA ESCAPE
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModales();
    });

});