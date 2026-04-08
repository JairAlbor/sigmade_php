/**
 * ARCHIVO: modal-catalogo.js
 * Versión completa con filtros por disponibilidad, estado y búsqueda
 */

// Variables globales
let filtroDisponibilidadActual = 'todos';
let filtroEstadoActual = 'todos';
let busquedaActual = '';

document.addEventListener('DOMContentLoaded', () => {

    // 1. GESTIÓN DE BIENVENIDA
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    const saludoElemento = document.getElementById('userName');
    if (nombreGuardado && saludoElemento) {
        saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
    }

    // 2. ELEMENTOS DE LOS MODALES
    const modalNuevo = document.getElementById('modal-Nuevo');
    const modalAct = document.getElementById('modal-Actualizar');

    // 3. BOTÓN DE APERTURA (MODAL NUEVO)
    const btnAbrirNuevo = document.getElementById('btn-abrir-formulario');
    if (btnAbrirNuevo) {
        btnAbrirNuevo.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (modalNuevo) {
                modalNuevo.classList.remove('hidden');
                crearOverlay();
            }
        });
    }

    // 4. BUSCADOR EN TIEMPO REAL
    const buscador = document.getElementById('searchMaterial');
    if (buscador) {
        buscador.addEventListener('keyup', function () {
            busquedaActual = this.value;
            aplicarFiltrosCompletos();
            mostrarBadgesFiltros();
        });
    }

    // 5. FILTRO POR DISPONIBILIDAD
    const filtroDisponibilidad = document.getElementById('filtroDisponibilidad');
    if (filtroDisponibilidad) {
        filtroDisponibilidad.addEventListener('change', function () {
            filtroDisponibilidadActual = this.value;
            aplicarFiltrosCompletos();
            mostrarBadgesFiltros();
        });
    }

    // 6. FILTRO POR ESTADO
    const filtroEstado = document.getElementById('filtroEstado');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', function () {
            filtroEstadoActual = this.value;
            aplicarFiltrosCompletos();
            mostrarBadgesFiltros();
        });
    }

    // 7. ACTUALIZAR ESTADÍSTICAS
    actualizarEstadisticas();
});

/**
 * Crea un overlay de fondo para los modales
 */
function crearOverlay() {
    // Eliminar overlay existente si lo hay
    const overlayExistente = document.querySelector('.modal-overlay-backdrop');
    if (overlayExistente) overlayExistente.remove();

    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay-backdrop';
    overlay.onclick = function (e) {
        e.preventDefault();
        e.stopPropagation();
        cerrarModales();
    };
    document.body.appendChild(overlay);
}

/**
 * Elimina el overlay
 */
function eliminarOverlay() {
    const overlay = document.querySelector('.modal-overlay-backdrop');
    if (overlay) overlay.remove();
}

/**
 * Cierra todos los modales y limpia formularios
 */
window.cerrarModales = function () {
    const modalNuevo = document.getElementById('modal-Nuevo');
    const modalAct = document.getElementById('modal-Actualizar');

    if (modalNuevo) modalNuevo.classList.add('hidden');
    if (modalAct) modalAct.classList.add('hidden');

    // Limpiar formularios
    const f1 = document.getElementById('formArticulo');
    const f2 = document.getElementById('formArticuloAct');
    if (f1) f1.reset();
    if (f2) f2.reset();

    eliminarOverlay();
};

/**
 * Abre el modal de edición con los datos del material
 */
window.abrirModalEdicion = function (id, nombre, id_dis, estado, tipo, disp) {
    const modalAct = document.getElementById('modal-Actualizar');

    // Insertar el ID en el input oculto
    const inputId = document.getElementById('idMaterialAct');
    if (inputId) inputId.value = id;

    // Llenar los campos del formulario
    const nombreInput = document.getElementById('nombreArticuloAct');
    const disciplinaSelect = document.getElementById('disciplinaAct');
    const estadoSelect = document.getElementById('estadoAct');
    const tipoSelect = document.getElementById('tipoMaterialAct');
    const disponibilidadSelect = document.getElementById('disponibilidadAct');

    if (nombreInput) nombreInput.value = nombre;
    if (disciplinaSelect) disciplinaSelect.value = id_dis;
    if (estadoSelect) estadoSelect.value = estado;
    if (tipoSelect) tipoSelect.value = tipo;
    if (disponibilidadSelect) disponibilidadSelect.value = disp;

    // Mostrar el modal
    if (modalAct) {
        modalAct.classList.remove('hidden');
        crearOverlay();
    }
};

/**
 * Elimina un material con confirmación
 */
window.eliminarMaterial = function (id) {
    if (confirm('¿Estás seguro de que deseas eliminar este material? Esta acción no se puede deshacer.')) {
        window.location.href = `CRUD/eliminarMat.php?id=${id}`;
    }
};

/**
 * Aplica todos los filtros combinados (búsqueda + disponibilidad + estado)
 */
function aplicarFiltrosCompletos() {
    const filas = document.querySelectorAll('#tabla-cuerpo tr');
    let contadorVisibles = 0;

    filas.forEach(fila => {
        // Saltar fila de mensaje
        if (fila.querySelector('.text-center')) return;

        // Obtener datos de la fila
        const nombre = fila.getAttribute('data-nombre') || '';
        const disciplina = fila.getAttribute('data-disciplina') || '';
        const tipo = fila.getAttribute('data-tipo') || '';
        const disponibleSpan = fila.querySelector('.badge-disponible');
        const estadoSpan = fila.querySelector('.badge-estado');

        const disponibilidad = disponibleSpan ? disponibleSpan.textContent : '';
        const estado = estadoSpan ? estadoSpan.textContent : '';

        const textoCompleto = `${nombre} ${disciplina} ${tipo}`.toLowerCase();

        // Aplicar filtros
        const coincideBusqueda = textoCompleto.includes(busquedaActual.toLowerCase());
        const coincideDisponibilidad = filtroDisponibilidadActual === 'todos' || disponibilidad === filtroDisponibilidadActual;
        const coincideEstado = filtroEstadoActual === 'todos' || estado === filtroEstadoActual;

        if (coincideBusqueda && coincideDisponibilidad && coincideEstado) {
            fila.style.display = '';
            contadorVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    // Mostrar mensaje si no hay resultados
    const tbody = document.getElementById('tabla-cuerpo');
    let filaNoResultados = document.getElementById('fila-no-resultados');

    if (contadorVisibles === 0 && !filaNoResultados) {
        const nuevaFila = document.createElement('tr');
        nuevaFila.id = 'fila-no-resultados';
        const primerFila = document.querySelector('#tabla-cuerpo tr:first-child td');
        const colspan = primerFila ? primerFila.colSpan : 6;
        nuevaFila.innerHTML = `<td colspan='${colspan}' class='text-center'>❌ No se encontraron materiales con los filtros seleccionados</td>`;
        tbody.appendChild(nuevaFila);
    } else if (contadorVisibles > 0 && filaNoResultados) {
        filaNoResultados.remove();
    }

    // Actualizar estadísticas según filtros
    actualizarEstadisticasConFiltro();
}

/**
 * Limpia todos los filtros del catálogo
 */
window.limpiarFiltrosCatalogo = function () {
    // Resetear variables
    busquedaActual = '';
    filtroDisponibilidadActual = 'todos';
    filtroEstadoActual = 'todos';

    // Resetear inputs
    const buscador = document.getElementById('searchMaterial');
    const filtroDisponibilidad = document.getElementById('filtroDisponibilidad');
    const filtroEstado = document.getElementById('filtroEstado');

    if (buscador) buscador.value = '';
    if (filtroDisponibilidad) filtroDisponibilidad.value = 'todos';
    if (filtroEstado) filtroEstado.value = 'todos';

    // Limpiar badges
    const badgesContainer = document.querySelector('.filtros-activos-badge');
    if (badgesContainer) badgesContainer.innerHTML = '';

    // Aplicar filtros reseteados
    aplicarFiltrosCompletos();
};

/**
 * Muestra badges de filtros activos
 */
function mostrarBadgesFiltros() {
    let container = document.querySelector('.filtros-activos-badge');
    if (!container) {
        // Crear el contenedor si no existe
        container = document.createElement('div');
        container.className = 'filtros-activos-badge';
        const statsCards = document.querySelector('.stats-cards');
        if (statsCards && statsCards.parentNode) {
            statsCards.insertAdjacentElement('afterend', container);
        }
    }

    let badgesHtml = '';

    if (filtroDisponibilidadActual !== 'todos') {
        const textoDisponibilidad = {
            'Libre': '✅ Disponibles',
            'Reservado': '🔄 Reservados',
            'Ocupado': '❌ Ocupados'
        }[filtroDisponibilidadActual] || filtroDisponibilidadActual;

        badgesHtml += `<span class="badge-filtro-activo">
            ${textoDisponibilidad}
            <i class="fa-solid fa-xmark" onclick="eliminarFiltroIndividual('disponibilidad')"></i>
        </span>`;
    }

    if (filtroEstadoActual !== 'todos') {
        const textoEstado = {
            'Nuevo': '✨ Nuevo',
            'Bueno': '👍 Bueno',
            'Regular': '⚠️ Regular',
            'Muy-desgastado': '🔧 Muy desgastado',
            'Roto': '💔 Roto'
        }[filtroEstadoActual] || filtroEstadoActual;

        badgesHtml += `<span class="badge-filtro-activo">
            ${textoEstado}
            <i class="fa-solid fa-xmark" onclick="eliminarFiltroIndividual('estado')"></i>
        </span>`;
    }

    if (busquedaActual) {
        badgesHtml += `<span class="badge-filtro-activo">
            🔍 Buscar: "${busquedaActual.substring(0, 20)}${busquedaActual.length > 20 ? '...' : ''}"
            <i class="fa-solid fa-xmark" onclick="eliminarFiltroIndividual('busqueda')"></i>
        </span>`;
    }

    container.innerHTML = badgesHtml;
}

/**
 * Elimina un filtro individual
 */
window.eliminarFiltroIndividual = function (tipo) {
    switch (tipo) {
        case 'disponibilidad':
            filtroDisponibilidadActual = 'todos';
            const selectDispo = document.getElementById('filtroDisponibilidad');
            if (selectDispo) selectDispo.value = 'todos';
            break;
        case 'estado':
            filtroEstadoActual = 'todos';
            const selectEstado = document.getElementById('filtroEstado');
            if (selectEstado) selectEstado.value = 'todos';
            break;
        case 'busqueda':
            busquedaActual = '';
            const buscador = document.getElementById('searchMaterial');
            if (buscador) buscador.value = '';
            break;
    }
    aplicarFiltrosCompletos();
    mostrarBadgesFiltros();
};

/**
 * Actualiza las estadísticas según los filtros aplicados
 */
function actualizarEstadisticasConFiltro() {
    const filas = document.querySelectorAll('#tabla-cuerpo tr');
    let total = 0;
    let disponibles = 0;
    let reservados = 0;

    filas.forEach(fila => {
        if (fila.querySelector('.text-center')) return;
        if (fila.style.display === 'none') return;

        total++;
        const disponibleSpan = fila.querySelector('.badge-disponible');
        if (disponibleSpan) {
            const texto = disponibleSpan.textContent;
            if (texto === 'Libre') {
                disponibles++;
            } else {
                reservados++;
            }
        }
    });

    const totalSpan = document.getElementById('articulos');
    const disponiblesSpan = document.getElementById('disponibles');
    const reservadosSpan = document.getElementById('reservados');

    if (totalSpan) totalSpan.textContent = total;
    if (disponiblesSpan) disponiblesSpan.textContent = disponibles;
    if (reservadosSpan) reservadosSpan.textContent = reservados;
}

/**
 * Actualiza las estadísticas totales (sin filtros)
 */
function actualizarEstadisticas() {
    const filas = document.querySelectorAll('#tabla-cuerpo tr');
    let total = 0;
    let disponibles = 0;
    let reservados = 0;

    filas.forEach(fila => {
        if (fila.querySelector('.text-center')) return;

        total++;
        const disponibleSpan = fila.querySelector('.badge-disponible');
        if (disponibleSpan) {
            const texto = disponibleSpan.textContent;
            if (texto === 'Libre') {
                disponibles++;
            } else {
                reservados++;
            }
        }
    });

    const totalSpan = document.getElementById('articulos');
    const disponiblesSpan = document.getElementById('disponibles');
    const reservadosSpan = document.getElementById('reservados');

    if (totalSpan) totalSpan.textContent = total;
    if (disponiblesSpan) disponiblesSpan.textContent = disponibles;
    if (reservadosSpan) reservadosSpan.textContent = reservados;
}

/**
 * Cerrar modales con tecla ESC
 */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarModales();
    }
});

/**
 * Prevenir propagación de clics dentro del modal
 */
document.addEventListener('click', (e) => {
    const modalNuevo = document.getElementById('modal-Nuevo');
    const modalAct = document.getElementById('modal-Actualizar');
    const overlay = document.querySelector('.modal-overlay-backdrop');

    if (overlay && e.target === overlay) {
        cerrarModales();
    }
});

/**
 * Función para filtrar materiales (mantener compatibilidad)
 */
function filtrarMateriales(texto) {
    busquedaActual = texto;
    aplicarFiltrosCompletos();
    mostrarBadgesFiltros();
}