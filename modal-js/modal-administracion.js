/**
 * SIGMADE - Gestión de Modales y Funcionalidades
 * Versión completa con gestión de préstamos, usuarios, eventos, disciplinas y entrenadores
 */

// ========== INICIALIZACIÓN ==========
document.addEventListener('DOMContentLoaded', () => {
    // Recuperar nombre de usuario
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    const saludoElemento = document.getElementById('userName');
    if (nombreGuardado && saludoElemento) {
        saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
    }

    // Inicializar buscadores
    inicializarBuscadores();

    // Cargar eventos guardados
    cargarEventos();

    // Inicializar gestión de préstamos
    if (document.getElementById('modalPrestamos')) {
        cargarPrestamos();
    }

    // Actualizar estadísticas de la tarjeta principal
    actualizarEstadisticasDashboard();
});

// ========== FUNCIONES DE MODALES ==========

/**
 * Abre o cierra un modal por su ID
 * @param {string} id - ID del modal
 */
window.toggleModal = function (id) {
    const modal = document.getElementById(id);
    if (modal) {
        // Si estamos cerrando el modal de usuarios, regresamos a la vista de tabla
        if (id === 'modalUsuarios' && !modal.classList.contains('hidden')) {
            mostrarTablaUsuarios();
        }
        // Si estamos cerrando el modal de préstamos, recargar datos
        if (id === 'modalPrestamos' && !modal.classList.contains('hidden')) {
            cargarPrestamos();
        }
        modal.classList.toggle('hidden');
    }
};

/**
 * Abre el modal de préstamos
 */
window.openPrestamosModal = function () {
    const modal = document.getElementById('modalPrestamos');
    if (modal) {
        modal.classList.remove('hidden');
        cargarPrestamos();
    }
};

/**
 * Abre el modal de eventos específicamente
 */
window.openEventModal = function () {
    const modal = document.getElementById('modalEventos');
    if (modal) {
        modal.classList.remove('hidden');
        cargarEventos();
    }
};

// ========== FUNCIONES PARA PRÉSTAMOS ==========

/**
 * Carga los préstamos desde el servidor usando la sentencia SQL
 */
function cargarPrestamos() {
    fetch('CRUD/obtenerPrestamo.php')
        .then(response => response.json())
        .then(data => {
            actualizarTablaPrestamos(data);
            actualizarEstadisticasPrestamos(data);
            actualizarEstadisticasDashboard();
        })
        .catch(error => {
            console.error('Error al cargar préstamos:', error);
            const tbody = document.getElementById('tablaPrestamosBody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: red;">Error al cargar los préstamos</td></tr>';
            }
        });
}

/**
 * Actualiza la tabla de préstamos con los datos recibidos
 * @param {Array} prestamos - Lista de préstamos
 */
function actualizarTablaPrestamos(prestamos) {
    const tbody = document.getElementById('tablaPrestamosBody');
    if (!tbody) return;

    if (!prestamos || prestamos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No hay préstamos registrados</td></tr>';
        return;
    }

    tbody.innerHTML = prestamos.map(prestamo => {
        const estadoClass = obtenerClaseEstado(prestamo.estado_general);
        let estadoTexto = prestamo.estado_general;

        // Normalizar estados
        if (estadoTexto === 'Prestado') estadoTexto = 'Activo';

        // Determinar clase de días y texto
        let diasClass = '';
        let diasTexto = '';

        if (prestamo.estado_general === 'Entregado') {
            diasTexto = 'Completado';
            diasClass = 'text-success';
        } else if (prestamo.estado_general === 'Activo' || prestamo.estado_general === 'Prestado') {
            if (prestamo.dias_restantes !== null && prestamo.dias_restantes !== undefined) {
                if (prestamo.dias_restantes < 0) {
                    diasTexto = `Vencido hace ${Math.abs(prestamo.dias_restantes)} días`;
                    diasClass = 'text-danger';
                } else if (prestamo.dias_restantes === 0) {
                    diasTexto = 'Vence hoy';
                    diasClass = 'text-warning';
                } else {
                    diasTexto = `${prestamo.dias_restantes} días restantes`;
                    diasClass = 'text-success';
                }
            } else {
                diasTexto = 'Por definir';
            }
        } else {
            diasTexto = 'Finalizado';
            diasClass = 'text-muted';
        }

        // Determinar si mostrar botón de entregar
        const mostrarBotonEntregar = (prestamo.estado_general === 'Activo' || prestamo.estado_general === 'Prestado');

        return `
            <tr data-estado="${prestamo.estado_general}" 
                data-fecha-limite="${prestamo.fecha_limite || ''}"
                data-prestamo-id="${prestamo.prestamo_id}">
                <td>${escapeHtml(prestamo.usuario_nombre)} ${escapeHtml(prestamo.usuario_apellidos || '')}</td>
                <td>${escapeHtml(prestamo.materiales)}</td>
                <td>${formatFecha(prestamo.fecha_solicitud)}</td>
                <td>${formatFecha(prestamo.fecha_limite)}</td>
                <td><span class="status-pill ${estadoClass}">${estadoTexto}</span></td>
                <td class="${diasClass}">${diasTexto}</td>
                <td class="actions">
                    ${mostrarBotonEntregar ?
                `<button class="btn-icon entregar" onclick="entregarPrestamo(${prestamo.prestamo_id})" title="Marcar como entregado">
                            <i class="fa-solid fa-check-circle"></i>
                        </button>` :
                ''
            }
                    <button class="btn-icon edit" onclick="gestionarPrestamo(${prestamo.prestamo_id})" title="Gestionar">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <button class="btn-icon delete" onclick="eliminarPrestamo(${prestamo.prestamo_id})" title="Eliminar">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Marca un préstamo como entregado
 * @param {number} id - ID del préstamo
 */
window.entregarPrestamo = function (id) {
    if (confirm('¿Confirmar que los materiales han sido entregados?')) {
        // Mostrar indicador de carga
        const boton = event.target.closest('.btn-icon');
        if (boton) {
            const originalHtml = boton.innerHTML;
            boton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            boton.disabled = true;
        }

        // Enviar solicitud para marcar como entregado
        fetch(`CRUD/entregarPrestamo.php?id=${id}`)
            .then(response => {
                if (response.ok) {
                    alert('Préstamo marcado como entregado exitosamente');
                    cargarPrestamos();
                } else {
                    throw new Error('Error al procesar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al marcar el préstamo como entregado');
                cargarPrestamos();
            });
    }
};

/**
 * Obtiene la clase CSS según el estado del préstamo
 * @param {string} estado - Estado del préstamo
 * @returns {string} Clase CSS
 */
function obtenerClaseEstado(estado) {
    const clases = {
        'Activo': 'status-active',
        'Prestado': 'status-active',
        'Vencido': 'status-inactive',
        'Entregado': 'status-returned',
        'Devuelto': 'status-returned',
        'Renovado': 'status-renewed'
    };
    return clases[estado] || 'status-pending';
}

/**
 * Actualiza las tarjetas de estadísticas de préstamos en el modal
 * @param {Array} prestamos - Lista de préstamos
 */
function actualizarEstadisticasPrestamos(prestamos) {
    const total = prestamos.length;
    const activos = prestamos.filter(p => p.estado_general === 'Activo' || p.estado_general === 'Prestado').length;
    const vencidos = prestamos.filter(p => {
        if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
            const fechaLimite = new Date(p.fecha_limite);
            const hoy = new Date();
            return fechaLimite < hoy;
        }
        return false;
    }).length;

    const totalSpan = document.getElementById('totalPrestamos');
    const activosSpan = document.getElementById('prestamosActivos');
    const vencidosSpan = document.getElementById('prestamosVencidos');

    if (totalSpan) totalSpan.textContent = total;
    if (activosSpan) activosSpan.textContent = activos;
    if (vencidosSpan) vencidosSpan.textContent = vencidos;
}

/**
 * Actualiza las estadísticas en el dashboard principal
 */
function actualizarEstadisticasDashboard() {
    fetch('CRUD/obtenerPrestamos.php')
        .then(response => response.json())
        .then(prestamos => {
            const activos = prestamos.filter(p => p.estado_general === 'Activo' || p.estado_general === 'Prestado').length;
            const vencidos = prestamos.filter(p => {
                if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
                    const fechaLimite = new Date(p.fecha_limite);
                    const hoy = new Date();
                    return fechaLimite < hoy;
                }
                return false;
            }).length;
            const vencenHoy = prestamos.filter(p => {
                if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
                    const fechaLimite = new Date(p.fecha_limite);
                    const hoy = new Date();
                    return fechaLimite.toDateString() === hoy.toDateString();
                }
                return false;
            }).length;

            const bigNumber = document.querySelector('.big-number');
            const statusList = document.querySelector('.status-list');

            if (bigNumber) bigNumber.textContent = activos;
            if (statusList) {
                statusList.innerHTML = `
                    <div class="status-item">Vencen hoy: ${vencenHoy}</div>
                    <div class="status-item">Vencidos: ${vencidos}</div>
                `;
            }
        })
        .catch(error => console.error('Error al actualizar dashboard:', error));
}

/**
 * Gestiona un préstamo específico (menú de opciones)
 * @param {number} id - ID del préstamo
 */
window.gestionarPrestamo = function (id) {
    const accion = prompt('¿Qué acción desea realizar?\n1 - Ver detalles\n2 - Renovar préstamo\n3 - Aplicar sanción\n\nIngrese el número de la acción:', '1');

    switch (accion) {
        case '1':
            verDetallePrestamo(id);
            break;
        case '2':
            const nuevosDias = prompt('¿Cuántos días adicionales desea agregar?', '7');
            if (nuevosDias && !isNaN(nuevosDias) && nuevosDias > 0) {
                window.location.href = `CRUD/renovarPrestamo.php?id=${id}&dias=${nuevosDias}`;
            }
            break;
        case '3':
            const motivo = prompt('Motivo de la sanción:');
            if (motivo) {
                window.location.href = `CRUD/sancionarUsuario.php?prestamo_id=${id}&motivo=${encodeURIComponent(motivo)}`;
            }
            break;
        default:
            if (accion !== null) alert('Acción no válida');
    }
};

/**
 * Ver detalle de un préstamo específico
 * @param {number} id - ID del préstamo
 */
function verDetallePrestamo(id) {
    fetch(`CRUD/obtenerDetallePrestamo.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            alert(`Detalle del préstamo #${id}\n\nUsuario: ${data.usuario_nombre} ${data.usuario_apellidos}\nMateriales: ${data.materiales}\nFecha solicitud: ${data.fecha_solicitud}\nFecha límite: ${data.fecha_limite}\nEstado: ${data.estado_general}`);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener detalles del préstamo');
        });
}

/**
 * Elimina un préstamo
 * @param {number} id - ID del préstamo
 */
window.eliminarPrestamo = function (id) {
    if (confirm('¿Está seguro de eliminar este préstamo? Esta acción no se puede deshacer.')) {
        window.location.href = `CRUD/eliminarPrestamo.php?id=${id}`;
    }
};

/**
 * Muestra el formulario para nuevo préstamo
 */
window.mostrarFormularioPrestamo = function () {
    cargarUsuariosParaSelect();
    cargarMaterialesParaSelect();

    const fechaInput = document.getElementById('fechaLimite');
    if (fechaInput) {
        const hoy = new Date();
        const fechaLimite = new Date(hoy);
        fechaLimite.setDate(hoy.getDate() + 7);
        fechaInput.value = fechaLimite.toISOString().split('T')[0];
    }

    toggleModal('modalFormPrestamo');
};

/**
 * Carga los usuarios en el select del formulario
 */
function cargarUsuariosParaSelect() {
    const select = document.getElementById('usuarioPrestamo');
    if (!select) return;

    fetch('CRUD/obtenerUsuariosActivos.php')
        .then(response => response.json())
        .then(usuarios => {
            select.innerHTML = '<option value="">Seleccione un usuario</option>' +
                usuarios.map(u => `<option value="${u.id}">${escapeHtml(u.nombre)}</option>`).join('');
        })
        .catch(() => {
            select.innerHTML = '<option value="">Seleccione un usuario</option>';
        });
}

/**
 * Carga los materiales en el select del formulario
 */
function cargarMaterialesParaSelect() {
    const select = document.getElementById('materialPrestamo');
    if (!select) return;

    fetch('CRUD/obtenerMaterialesDisponibles.php')
        .then(response => response.json())
        .then(materiales => {
            select.innerHTML = materiales.map(m => `<option value="${m.id}">${escapeHtml(m.nombre)}</option>`).join('');
        })
        .catch(() => {
            select.innerHTML = '<option value="">Error al cargar materiales</option>';
        });
}

/**
 * Maneja el envío del formulario de préstamo
 */
document.addEventListener('DOMContentLoaded', () => {
    const formPrestamo = document.getElementById('formNuevoPrestamo');
    if (formPrestamo) {
        formPrestamo.addEventListener('submit', function (e) {
            e.preventDefault();

            const usuarioId = document.getElementById('usuarioPrestamo').value;
            const materialSelect = document.getElementById('materialPrestamo');
            const materialesSeleccionados = Array.from(materialSelect.selectedOptions).map(opt => opt.value);
            const fechaLimite = document.getElementById('fechaLimite').value;

            if (!usuarioId) {
                alert('Por favor seleccione un usuario');
                return;
            }

            if (materialesSeleccionados.length === 0) {
                alert('Por favor seleccione al menos un material');
                return;
            }

            if (!fechaLimite) {
                alert('Por favor seleccione una fecha límite');
                return;
            }

            // Enviar datos al servidor
            const formData = new FormData();
            formData.append('usuario_id', usuarioId);
            materialesSeleccionados.forEach(m => formData.append('materiales[]', m));
            formData.append('fecha_limite', fechaLimite);

            fetch('CRUD/registrarPrestamo.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Préstamo registrado exitosamente');
                        toggleModal('modalFormPrestamo');
                        cargarPrestamos();
                    } else {
                        alert('Error al registrar el préstamo: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al registrar el préstamo');
                });
        });
    }
});

/**
 * Filtra los préstamos según los criterios de búsqueda y filtros
 */
function filtrarPrestamos() {
    const busqueda = document.getElementById('buscarPrestamo')?.value.toLowerCase() || '';
    const estadoFiltro = document.getElementById('filtroEstadoPrestamo')?.value || '';
    const fechaInicio = document.getElementById('filtroFechaInicio')?.value;
    const fechaFin = document.getElementById('filtroFechaFin')?.value;

    const filas = document.querySelectorAll('#tablaPrestamosBody tr');

    if (filas.length === 1 && (filas[0].innerText.includes('No hay préstamos') || filas[0].innerText.includes('Error'))) return;

    filas.forEach(fila => {
        let mostrar = true;
        const textoFila = fila.textContent.toLowerCase();

        if (busqueda && !textoFila.includes(busqueda)) {
            mostrar = false;
        }

        if (mostrar && estadoFiltro) {
            const estadoCelda = fila.querySelector('.status-pill');
            let estadoTexto = estadoCelda ? estadoCelda.textContent : '';
            if (estadoTexto !== estadoFiltro) {
                mostrar = false;
            }
        }

        if (mostrar && (fechaInicio || fechaFin)) {
            const fechaLimite = fila.dataset.fechaLimite;
            if (fechaLimite) {
                if (fechaInicio && fechaLimite < fechaInicio) mostrar = false;
                if (fechaFin && fechaLimite > fechaFin) mostrar = false;
            }
        }

        fila.style.display = mostrar ? '' : 'none';
    });
}

/**
 * Muestra/oculta los filtros avanzados
 */
window.toggleFiltrosPrestamos = function () {
    const filtros = document.getElementById('filtrosPrestamos');
    if (filtros) {
        filtros.classList.toggle('hidden');
    }
};

/**
 * Limpia todos los filtros de préstamos
 */
window.limpiarFiltrosPrestamos = function () {
    const busqueda = document.getElementById('buscarPrestamo');
    const estadoFiltro = document.getElementById('filtroEstadoPrestamo');
    const fechaInicio = document.getElementById('filtroFechaInicio');
    const fechaFin = document.getElementById('filtroFechaFin');

    if (busqueda) busqueda.value = '';
    if (estadoFiltro) estadoFiltro.value = '';
    if (fechaInicio) fechaInicio.value = '';
    if (fechaFin) fechaFin.value = '';

    const filas = document.querySelectorAll('#tablaPrestamosBody tr');
    filas.forEach(fila => {
        fila.style.display = '';
    });
};

// ========== FUNCIONES PARA EVENTOS ==========

/**
 * Muestra u oculta el formulario de eventos
 */
window.toggleFormEventos = function () {
    const form = document.getElementById('addEventForm');
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            document.getElementById('eventTitle').value = '';
            document.getElementById('eventLocation').value = '';
            document.getElementById('eventDate').value = '';
            document.getElementById('eventTime').value = '';
            document.getElementById('eventDesc').value = '';
        }
    }
};

/**
 * Guarda un nuevo evento
 */
window.saveEvent = function () {
    const titulo = document.getElementById('eventTitle').value;
    const ubicacion = document.getElementById('eventLocation').value;
    const fecha = document.getElementById('eventDate').value;
    const hora = document.getElementById('eventTime').value;
    const descripcion = document.getElementById('eventDesc').value;

    if (!titulo || !fecha) {
        alert('Por favor complete al menos el título y la fecha del evento');
        return;
    }

    const evento = {
        id: Date.now(),
        titulo: titulo,
        ubicacion: ubicacion,
        fecha: fecha,
        hora: hora,
        descripcion: descripcion,
        fechaCreacion: new Date().toISOString()
    };

    let eventos = JSON.parse(localStorage.getItem('eventos') || '[]');
    eventos.push(evento);
    localStorage.setItem('eventos', JSON.stringify(eventos));

    toggleFormEventos();
    cargarEventos();
    alert('Evento guardado exitosamente');
};

/**
 * Carga y muestra los eventos guardados
 */
function cargarEventos() {
    const container = document.getElementById('eventList');
    if (!container) return;

    const eventos = JSON.parse(localStorage.getItem('eventos') || '[]');

    if (eventos.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #666;">No hay eventos registrados</p>';
        return;
    }

    eventos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

    container.innerHTML = eventos.map(evento => `
        <div class="event-card">
            <h3><i class="fa-solid fa-calendar-alt"></i> ${escapeHtml(evento.titulo)}</h3>
            <p><i class="fa-solid fa-location-dot"></i> ${escapeHtml(evento.ubicacion || 'No especificada')}</p>
            <p><i class="fa-solid fa-calendar-day"></i> ${formatFecha(evento.fecha)} ${evento.hora ? `a las ${evento.hora}` : ''}</p>
            <p><i class="fa-solid fa-align-left"></i> ${escapeHtml(evento.descripcion || 'Sin descripción')}</p>
            <button class="btn-icon delete" onclick="eliminarEvento(${evento.id})" style="margin-top: 10px;">
                <i class="fa-solid fa-trash-can"></i> Eliminar
            </button>
        </div>
    `).join('');
}

/**
 * Elimina un evento por ID
 * @param {number} id - ID del evento
 */
window.eliminarEvento = function (id) {
    if (confirm('¿Estás seguro de eliminar este evento?')) {
        let eventos = JSON.parse(localStorage.getItem('eventos') || '[]');
        eventos = eventos.filter(e => e.id !== id);
        localStorage.setItem('eventos', JSON.stringify(eventos));
        cargarEventos();
        alert('Evento eliminado');
    }
};

// ========== FUNCIONES PARA USUARIOS ==========

/**
 * Muestra el formulario de edición de usuario
 */
window.mostrarFormularioEditar = function (id, nombre, email, rol) {
    const inputId = document.getElementById('edit_id_usuario');
    const inputNombre = document.getElementById('edit_nombre');
    const inputEmail = document.getElementById('edit_email');
    const inputRol = document.getElementById('edit_rol');

    if (inputId) inputId.value = id;
    if (inputNombre) inputNombre.value = nombre;
    if (inputEmail) inputEmail.value = email;
    if (inputRol) inputRol.value = rol;

    document.getElementById('vistaTablaUsuarios').classList.add('hidden');
    document.getElementById('vistaFormularioUsuario').classList.remove('hidden');

    const titulo = document.getElementById('tituloModalUsuarios');
    if (titulo) titulo.innerHTML = '<i class="fa-solid fa-user-pen"></i> Editar Usuario';
};

/**
 * Muestra el formulario de registro de usuario
 */
window.mostrarFormularioRegistro = function () {
    document.getElementById('vistaTablaUsuarios').classList.add('hidden');
    document.getElementById('vistaRegistroUsuario').classList.remove('hidden');
    document.getElementById('tituloModalUsuarios').innerHTML = '<i class="fa-solid fa-user-plus"></i> Nuevo Registro';
};

/**
 * Muestra la vista de tabla de usuarios
 */
window.mostrarTablaUsuarios = function () {
    const vistaFormulario = document.getElementById('vistaFormularioUsuario');
    const vistaRegistro = document.getElementById('vistaRegistroUsuario');
    const vistaTabla = document.getElementById('vistaTablaUsuarios');

    if (vistaFormulario) vistaFormulario.classList.add('hidden');
    if (vistaRegistro) vistaRegistro.classList.add('hidden');
    if (vistaTabla) vistaTabla.classList.remove('hidden');

    const titulo = document.getElementById('tituloModalUsuarios');
    if (titulo) titulo.innerHTML = '<i class="fa-solid fa-users-gear"></i> Usuarios Registrados';
};

/**
 * Elimina un usuario
 * @param {number} id - ID del usuario
 */
window.eliminarUsuario = function (id) {
    if (confirm('¿Estás seguro de eliminar este usuario?')) {
        window.location.href = `CRUD/eliminarUsuario.php?id=${id}`;
    }
};

// ========== FUNCIONES PARA DISCIPLINAS ==========

/**
 * Muestra el formulario para agregar disciplina
 */
window.mostrarFormularioDisciplina = function () {
    const form = document.getElementById('formDisciplina');
    if (form) {
        form.classList.remove('hidden');
        document.getElementById('disciplinaNombre').value = '';
        document.getElementById('disciplinaEntrenador').value = '';
    }
};

/**
 * Oculta el formulario de disciplina
 */
window.ocultarFormularioDisciplina = function () {
    const form = document.getElementById('formDisciplina');
    if (form) {
        form.classList.add('hidden');
    }
};

/**
 * Guarda una nueva disciplina
 */
window.guardarDisciplina = function () {
    const nombre = document.getElementById('disciplinaNombre').value;
    const entrenador = document.getElementById('disciplinaEntrenador').value;

    if (!nombre) {
        alert('Por favor ingrese el nombre de la disciplina');
        return;
    }

    const tabla = document.getElementById('tablaDisciplinasBody');
    const nuevaFila = document.createElement('tr');

    nuevaFila.innerHTML = `
        <td>${escapeHtml(nombre)}</td>
        <td>${escapeHtml(entrenador || 'Por Asignar')}</td>
        <td><span class="status-pill status-active">Activo</span></td>
        <td class="actions">
            <button class="btn-icon edit" onclick="editarDisciplina(this)"><i class="fa-regular fa-pen-to-square"></i></button>
            <button class="btn-icon delete" onclick="eliminarDisciplina(this)"><i class="fa-regular fa-trash-can"></i></button>
        </td>
    `;

    tabla.appendChild(nuevaFila);
    ocultarFormularioDisciplina();
    alert('Disciplina agregada exitosamente');
};

/**
 * Edita una disciplina existente
 * @param {HTMLElement} btn - Botón que disparó la acción
 */
window.editarDisciplina = function (btn) {
    const fila = btn.closest('tr');
    const nombreCelda = fila.cells[0];
    const entrenadorCelda = fila.cells[1];

    const nuevoNombre = prompt('Editar nombre de la disciplina:', nombreCelda.textContent);
    if (nuevoNombre && nuevoNombre.trim()) {
        nombreCelda.textContent = nuevoNombre.trim();
    }

    const nuevoEntrenador = prompt('Editar entrenador:', entrenadorCelda.textContent);
    if (nuevoEntrenador !== null) {
        entrenadorCelda.textContent = nuevoEntrenador || 'Por Asignar';
    }
};

/**
 * Elimina una disciplina
 * @param {HTMLElement} btn - Botón que disparó la acción
 */
window.eliminarDisciplina = function (btn) {
    if (confirm('¿Estás seguro de eliminar esta disciplina?')) {
        const fila = btn.closest('tr');
        fila.remove();
        alert('Disciplina eliminada');
    }
};

// ========== FUNCIONES PARA ENTRENADORES ==========

/**
 * Muestra formulario para agregar entrenador
 */
window.mostrarFormularioEntrenador = function () {
    const nombre = prompt('Nombre del entrenador:');
    if (nombre) {
        const especialidad = prompt('Especialidad:');
        const contacto = prompt('Email de contacto:');

        const tabla = document.getElementById('tablaEntrenadoresBody');
        const nuevaFila = document.createElement('tr');
        nuevaFila.innerHTML = `
            <td>${escapeHtml(nombre)}</td>
            <td>${escapeHtml(especialidad || 'Por definir')}</td>
            <td>${escapeHtml(contacto || 'No especificado')}</td>
            <td><span class="status-pill status-active">Activo</span></td>
            <td class="actions">
                <button class="btn-icon edit" onclick="editarEntrenador(this)"><i class="fa-regular fa-pen-to-square"></i></button>
                <button class="btn-icon delete" onclick="eliminarEntrenador(this)"><i class="fa-regular fa-trash-can"></i></button>
            </td>
        `;
        tabla.appendChild(nuevaFila);
        alert('Entrenador agregado exitosamente');
    }
};

/**
 * Edita un entrenador
 * @param {HTMLElement} btn - Botón que disparó la acción
 */
window.editarEntrenador = function (btn) {
    const fila = btn.closest('tr');
    if (!fila) return;

    const nombreCelda = fila.cells[0];
    const especialidadCelda = fila.cells[1];
    const contactoCelda = fila.cells[2];

    const nuevoNombre = prompt('Editar nombre:', nombreCelda.textContent);
    if (nuevoNombre && nuevoNombre.trim()) {
        nombreCelda.textContent = nuevoNombre.trim();
    }

    const nuevaEspecialidad = prompt('Editar especialidad:', especialidadCelda.textContent);
    if (nuevaEspecialidad !== null) {
        especialidadCelda.textContent = nuevaEspecialidad || 'Por definir';
    }

    const nuevoContacto = prompt('Editar contacto:', contactoCelda.textContent);
    if (nuevoContacto !== null) {
        contactoCelda.textContent = nuevoContacto || 'No especificado';
    }
};

/**
 * Elimina un entrenador
 * @param {HTMLElement} btn - Botón que disparó la acción
 */
window.eliminarEntrenador = function (btn) {
    if (confirm('¿Estás seguro de eliminar este entrenador?')) {
        const fila = btn.closest('tr');
        if (fila) {
            fila.remove();
            alert('Entrenador eliminado');
        }
    }
};

// ========== FUNCIONES DE BÚSQUEDA ==========

/**
 * Inicializa los buscadores en tiempo real
 */
function inicializarBuscadores() {
    // Buscador de usuarios
    const inputBusqueda = document.getElementById('buscarUsuario');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', () => {
            const valor = inputBusqueda.value.toLowerCase();
            const filasUsuarios = document.querySelectorAll('#tablaUsuariosBody tr');
            filtrarFilas(filasUsuarios, valor);
        });
    }

    // Buscador de disciplinas
    const inputBusquedaDisciplina = document.getElementById('buscarDisciplina');
    if (inputBusquedaDisciplina) {
        inputBusquedaDisciplina.addEventListener('keyup', () => {
            const valor = inputBusquedaDisciplina.value.toLowerCase();
            const filasDisciplinas = document.querySelectorAll('#tablaDisciplinasBody tr');
            filtrarFilas(filasDisciplinas, valor);
        });
    }

    // Buscador de préstamos
    const inputBusquedaPrestamo = document.getElementById('buscarPrestamo');
    if (inputBusquedaPrestamo) {
        inputBusquedaPrestamo.addEventListener('keyup', () => {
            filtrarPrestamos();
        });
    }

    // Buscador de entrenadores
    const inputBusquedaEntrenador = document.getElementById('buscarEntrenador');
    if (inputBusquedaEntrenador) {
        inputBusquedaEntrenador.addEventListener('keyup', () => {
            const valor = inputBusquedaEntrenador.value.toLowerCase();
            const filasEntrenadores = document.querySelectorAll('#tablaEntrenadoresBody tr');
            filtrarFilas(filasEntrenadores, valor);
        });
    }
}

/**
 * Filtra filas de una tabla según texto de búsqueda
 * @param {NodeList} filas - Lista de filas a filtrar
 * @param {string} valor - Texto de búsqueda
 */
function filtrarFilas(filas, valor) {
    filas.forEach(fila => {
        const contenido = fila.textContent.toLowerCase();
        fila.style.display = contenido.includes(valor) ? '' : 'none';
    });
}

// ========== FUNCIONES UTILITARIAS ==========

/**
 * Escapa caracteres HTML para prevenir XSS
 * @param {string} str - Texto a escapar
 * @returns {string} Texto escapado
 */
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/**
 * Formatea una fecha para mostrar
 * @param {string} fechaStr - Fecha en formato YYYY-MM-DD
 * @returns {string} Fecha formateada
 */
function formatFecha(fechaStr) {
    if (!fechaStr) return 'Fecha no especificada';
    const [year, month, day] = fechaStr.split('-');
    return `${day}/${month}/${year}`;
}