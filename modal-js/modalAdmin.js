/**
 * SIGMADE - Gestión de Modales y Funcionalidades
 * Versión completa con gestión de préstamos, usuarios, eventos, disciplinas y entrenadores
 */

// ========== INICIALIZACIÓN ==========
document.addEventListener('DOMContentLoaded', () => {
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    const saludoElemento = document.getElementById('userName');
    if (nombreGuardado && saludoElemento) {
        saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
    }

    inicializarBuscadores();
    cargarEventos();

    if (document.getElementById('modalPrestamos')) {
        cargarPrestamos();
    }

    actualizarEstadisticasDashboard();

    // Buscador de materiales dentro del formulario
    document.addEventListener('keyup', (e) => {
        if (e.target && e.target.id === 'buscarMaterial') {
            const valor = e.target.value.toLowerCase();
            document.querySelectorAll('.material-check-item').forEach(item => {
                const texto = item.textContent.toLowerCase();
                item.classList.toggle('oculto', !texto.includes(valor));
            });
        }
    });

    // Cerrar dropdown de materiales al hacer clic fuera
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('dropdownMateriales');
        if (dropdown && !dropdown.contains(e.target)) {
            document.getElementById('dropdownMaterialesCuerpo')?.classList.add('hidden');
            document.querySelector('.custom-dropdown-header')?.classList.remove('active');
        }
    });

    // Submit del formulario de préstamo
    const formPrestamo = document.getElementById('formNuevoPrestamo');
    if (formPrestamo) {
        formPrestamo.addEventListener('submit', function (e) {
            e.preventDefault();

            const usuarioId = document.getElementById('usuarioPrestamo').value;
            const checkboxes = document.querySelectorAll('#listaMateriales input[type="checkbox"]:checked');
            const materialesSeleccionados = Array.from(checkboxes).map(cb => cb.value);
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
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al registrar el préstamo');
                });
        });
    }
});

// ========== FUNCIONES DE MODALES ==========

window.toggleModal = function (id) {
    const modal = document.getElementById(id);
    if (modal) {
        if (id === 'modalUsuarios' && !modal.classList.contains('hidden')) {
            mostrarTablaUsuarios();
        }
        if (id === 'modalPrestamos' && !modal.classList.contains('hidden')) {
            cargarPrestamos();
        }
        if (id === 'modalDisciplinas' && modal.classList.contains('hidden')) {
            cargarDisciplinas();
        }
        if (id === 'modalEntrenadores' && modal.classList.contains('hidden')) {
            cargarEntrenadores();
        }
        modal.classList.toggle('hidden');
    }
};

window.openPrestamosModal = function () {
    const modal = document.getElementById('modalPrestamos');
    if (modal) {
        modal.classList.remove('hidden');
        cargarPrestamos();
    }
};

window.openEventModal = function () {
    const modal = document.getElementById('modalEventos');
    if (modal) {
        modal.classList.remove('hidden');
        cargarEventos();
    }
};

// ========== FUNCIONES PARA PRÉSTAMOS ==========

function cargarPrestamos() {
    fetch('CRUD/obtenerPrestamos.php')
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

        if (estadoTexto === 'Prestado') estadoTexto = 'Activo';

        let diasClass = '';
        let diasTexto = '';

        if (prestamo.estado_general === 'Entregado' || prestamo.estado_general === 'Finalizado') {
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
                    ${prestamo.estado_general === 'Pendiente' ?
                `<button class="btn-icon" onclick="cambiarEstadoPrestamo(${prestamo.prestamo_id}, 'Aprobado')" title="Aprobar Solicitud">
                            <i class="fa-solid fa-thumbs-up"></i>
                        </button>
                        <button class="btn-icon delete" onclick="cambiarEstadoPrestamo(${prestamo.prestamo_id}, 'Denegado')" title="Denegar Solicitud">
                            <i class="fa-solid fa-thumbs-down"></i>
                        </button>` : ''
            }
                    ${prestamo.estado_general === 'Aprobado' ?
                `<button class="btn-icon entregar" onclick="cambiarEstadoPrestamo(${prestamo.prestamo_id}, 'Activo')" title="Comenzar Préstamo">
                            <i class="fa-solid fa-play"></i>
                        </button>` : ''
            }
                    ${mostrarBotonEntregar ?
                `<button class="btn-icon entregar" onclick="mostrarModalFinalizarPrestamo(${prestamo.prestamo_id})" title="Finalizar Préstamo (Entregar)">
                            <i class="fa-solid fa-check-circle"></i>
                        </button>` : ''
            }
                    <button class="btn-icon edit" onclick="gestionarPrestamo(${prestamo.prestamo_id})" title="Gestionar">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    ${prestamo.estado_general !== 'Activo' && prestamo.estado_general !== 'Pendiente' ?
                `<button class="btn-icon delete" onclick="eliminarPrestamo(${prestamo.prestamo_id})" title="Eliminar">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>` : ''
            }
                </td>
            </tr>
        `;
    }).join('');
}

window.cambiarEstadoPrestamo = function (id, nuevoEstado) {
    if (confirm(`¿Confirmar que desea cambiar el estado a ${nuevoEstado}?`)) {
        fetch('CRUD/cambiarEstadoPrestamo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, estado: nuevoEstado })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    cargarPrestamos();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar estado.');
            });
    }
};

window.mostrarModalFinalizarPrestamo = function (id) {
    document.getElementById('inputFinalizarPrestamoId').value = id;
    document.getElementById('textareaObservaciones').value = '';
    toggleModal('modalFinalizarPrestamo');
};

window.finalizarPrestamo = function () {
    const id = document.getElementById('inputFinalizarPrestamoId').value;
    const observaciones = document.getElementById('textareaObservaciones').value;

    fetch('CRUD/finalizarPrestamo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, observaciones: observaciones })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                toggleModal('modalFinalizarPrestamo');
                cargarPrestamos();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al finalizar el préstamo.');
        });
};

function obtenerClaseEstado(estado) {
    const clases = {
        'Activo': 'status-active',
        'Prestado': 'status-active',
        'Vencido': 'status-inactive',
        'Entregado': 'status-returned',
        'Devuelto': 'status-returned',
        'Finalizado': 'status-returned',
        'Renovado': 'status-renewed'
    };
    return clases[estado] || 'status-pending';
}

function actualizarEstadisticasPrestamos(prestamos) {
    const total = prestamos.length;
    const activos = prestamos.filter(p => p.estado_general === 'Activo' || p.estado_general === 'Prestado').length;
    const vencidos = prestamos.filter(p => {
        if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
            return new Date(p.fecha_limite) < new Date();
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

function actualizarEstadisticasDashboard() {
    fetch('CRUD/obtenerPrestamos.php')
        .then(response => response.json())
        .then(prestamos => {
            const activos = prestamos.filter(p => p.estado_general === 'Activo' || p.estado_general === 'Prestado').length;
            const vencidos = prestamos.filter(p => {
                if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
                    return new Date(p.fecha_limite) < new Date();
                }
                return false;
            }).length;
            const vencenHoy = prestamos.filter(p => {
                if (p.estado_general === 'Activo' || p.estado_general === 'Prestado') {
                    return new Date(p.fecha_limite).toDateString() === new Date().toDateString();
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

window.gestionarPrestamo = function (id) {
    document.getElementById('inputDetallePrestamoId').value = id;

    // Fetch details first
    fetch(`CRUD/obtenerDetallePrestamo.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const body = document.getElementById('detallePrestamoBody');
            body.innerHTML = `
                <p><strong>ID Préstamo:</strong> #${id}</p>
                <p><strong>Usuario:</strong> ${data.usuario_nombre} ${data.usuario_apellidos}</p>
                <p><strong>Materiales:</strong> ${data.materiales}</p>
                <p><strong>Fecha Solicitud:</strong> ${data.fecha_solicitud}</p>
                <p><strong>Fecha Límite:</strong> ${data.fecha_limite}</p>
                <p><strong>Estado:</strong> <span class="status-pill status-active">${data.estado_general}</span></p>
            `;
            toggleModal('modalDetallePrestamo');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener detalles del préstamo');
        });
};

window.accionRenovarPrestamo = function () {
    const id = document.getElementById('inputDetallePrestamoId').value;
    const nuevosDias = prompt('¿Cuántos días adicionales desea agregar?', '7');
    if (nuevosDias && !isNaN(nuevosDias) && nuevosDias > 0) {
        window.location.href = `CRUD/renovarPrestamo.php?id=${id}&dias=${nuevosDias}`;
    }
};

window.accionSancionarUsuario = function () {
    const id = document.getElementById('inputDetallePrestamoId').value;
    const motivo = prompt('Motivo de la sanción:');
    if (motivo) {
        window.location.href = `CRUD/sancionarUsuario.php?prestamo_id=${id}&motivo=${encodeURIComponent(motivo)}`;
    }
};

window.eliminarPrestamo = function (id) {
    if (confirm('¿Está seguro de eliminar este préstamo? Esta acción no se puede deshacer.')) {
        window.location.href = `CRUD/eliminarPrestamo.php?id=${id}`;
    }
};

// ========== FORMULARIO NUEVO PRÉSTAMO ==========

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

function cargarUsuariosParaSelect() {
    const select = document.getElementById('usuarioPrestamo');
    if (!select) return;

    select.innerHTML = '<option value="">Cargando usuarios...</option>';

    fetch('CRUD/obtenerUsuariosActivos.php')
        .then(response => response.json())
        .then(usuarios => {
            select.innerHTML = '<option value="">Seleccione un usuario</option>' +
                usuarios.map(u => `<option value="${u.id}">${escapeHtml(u.nombre)}</option>`).join('');
        })
        .catch(() => {
            select.innerHTML = '<option value="">Error al cargar usuarios</option>';
        });
}

/**
 * Carga los materiales disponibles como checkboxes
 */
function cargarMaterialesParaSelect() {
    const lista = document.getElementById('listaMateriales');
    if (!lista) return;

    lista.innerHTML = '<p style="padding:10px; color:#888;">Cargando materiales...</p>';

    // Limpiar resumen
    const resumen = document.getElementById('resumenSeleccion');
    if (resumen) resumen.classList.add('hidden');

    // Limpiar buscador
    const buscar = document.getElementById('buscarMaterial');
    if (buscar) buscar.value = '';

    fetch('CRUD/obtenerMaterialesDisponibles.php')
        .then(response => response.json())
        .then(materiales => {
            if (materiales.length === 0) {
                lista.innerHTML = '<p style="padding:10px; color:#888; text-align:center;"><i class="fa-solid fa-box-open"></i> No hay materiales disponibles en este momento.</p>';
                return;
            }

            lista.innerHTML = materiales.map(m => `
                <div class="material-check-item" id="item-${m.id}">
                    <input 
                        type="checkbox" 
                        id="mat-${m.id}" 
                        name="materiales[]" 
                        value="${m.id}"
                        onchange="actualizarResumenSeleccion()"
                    >
                    <label for="mat-${m.id}">
                        <span>${escapeHtml(m.nombre)}</span>
                        <span class="material-estado-badge">${escapeHtml(m.estado)}</span>
                    </label>
                </div>
            `).join('');
        })
        .catch(() => {
            lista.innerHTML = '<p style="padding:10px; color:red; text-align:center;"><i class="fa-solid fa-triangle-exclamation"></i> Error al cargar materiales.</p>';
        });
}

/**
 * Actualiza el resumen de materiales seleccionados
 */
window.actualizarResumenSeleccion = function () {
    const seleccionados = document.querySelectorAll('#listaMateriales input[type="checkbox"]:checked');
    const resumen = document.getElementById('resumenSeleccion');
    const texto = document.getElementById('textoResumen');
    const dropdownTexto = document.getElementById('dropdownMaterialesTexto');

    if (!resumen || !texto) return;

    if (seleccionados.length === 0) {
        resumen.classList.add('hidden');
        if (dropdownTexto) dropdownTexto.textContent = 'Seleccione materiales...';
    } else {
        resumen.classList.remove('hidden');
        const nombres = Array.from(seleccionados).map(cb => {
            // Obtener el nombre del label correspondiente
            const label = document.querySelector(`label[for="${cb.id}"] span:first-child`);
            return label ? label.textContent : cb.value;
        });
        texto.textContent = `${seleccionados.length} seleccionado(s): ${nombres.join(', ')}`;
        if (dropdownTexto) dropdownTexto.textContent = `${seleccionados.length} seleccionado(s)`;
    }
};

window.toggleMaterialesDropdown = function (event) {
    if (event) event.stopPropagation();
    const cuerpo = document.getElementById('dropdownMaterialesCuerpo');
    const header = document.querySelector('.custom-dropdown-header');

    if (cuerpo && header) {
        cuerpo.classList.toggle('hidden');
        header.classList.toggle('active');
    }
};

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

        if (busqueda && !textoFila.includes(busqueda)) mostrar = false;

        if (mostrar && estadoFiltro) {
            const estadoCelda = fila.querySelector('.status-pill');
            if (estadoCelda && estadoCelda.textContent !== estadoFiltro) mostrar = false;
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

window.toggleFiltrosPrestamos = function () {
    const filtros = document.getElementById('filtrosPrestamos');
    if (filtros) filtros.classList.toggle('hidden');
};

window.limpiarFiltrosPrestamos = function () {
    const campos = ['buscarPrestamo', 'filtroEstadoPrestamo', 'filtroFechaInicio', 'filtroFechaFin'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    document.querySelectorAll('#tablaPrestamosBody tr').forEach(fila => {
        fila.style.display = '';
    });
};

// ========== FUNCIONES PARA EVENTOS ==========

window.toggleFormEventos = function () {
    const form = document.getElementById('addEventForm');
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            ['eventTitle', 'eventLocation', 'eventDate', 'eventTime', 'eventDesc'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
        }
    }
};

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

    const formData = new URLSearchParams();
    formData.append('titulo', titulo);
    formData.append('ubicacion', ubicacion);
    formData.append('fecha', fecha);
    formData.append('hora', hora);
    formData.append('descripcion', descripcion);

    fetch('CRUD/registrarEvento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                toggleFormEventos();
                cargarEventos();
                alert('Evento guardado exitosamente');
            } else {
                alert(data.message);
            }
        })
        .catch(err => alert('Error salvando evento'));
};

function cargarEventos() {
    const container = document.getElementById('eventList');
    if (!container) return;

    fetch('CRUD/obtenerEventos.php')
        .then(res => res.json())
        .then(eventos => {
            if (eventos.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666;">No hay eventos registrados</p>';
                return;
            }

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
        })
        .catch(() => {
            container.innerHTML = '<p style="color:red; text-align:center;">Error cargando eventos.</p>';
        });
}

window.eliminarEvento = function (id) {
    if (confirm('¿Estás seguro de eliminar este evento?')) {
        const formData = new URLSearchParams();
        formData.append('id', id);

        fetch('CRUD/eliminarEvento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    cargarEventos();
                    alert('Evento eliminado');
                } else {
                    alert(data.message);
                }
            });
    }
};

// ========== FUNCIONES PARA USUARIOS ==========

window.mostrarFormularioEditar = function (id, nombre, email, rol) {
    const campos = {
        'edit_id_usuario': id,
        'edit_nombre': nombre,
        'edit_email': email,
        'edit_rol': rol
    };

    Object.entries(campos).forEach(([elementId, valor]) => {
        const el = document.getElementById(elementId);
        if (el) el.value = valor;
    });

    document.getElementById('vistaTablaUsuarios').classList.add('hidden');
    document.getElementById('vistaFormularioUsuario').classList.remove('hidden');

    const titulo = document.getElementById('tituloModalUsuarios');
    if (titulo) titulo.innerHTML = '<i class="fa-solid fa-user-pen"></i> Editar Usuario';
};

window.mostrarFormularioRegistro = function () {
    document.getElementById('vistaTablaUsuarios').classList.add('hidden');
    document.getElementById('vistaRegistroUsuario').classList.remove('hidden');
    document.getElementById('tituloModalUsuarios').innerHTML = '<i class="fa-solid fa-user-plus"></i> Nuevo Registro';
};

window.mostrarTablaUsuarios = function () {
    ['vistaFormularioUsuario', 'vistaRegistroUsuario'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    const vistaTabla = document.getElementById('vistaTablaUsuarios');
    if (vistaTabla) vistaTabla.classList.remove('hidden');

    const titulo = document.getElementById('tituloModalUsuarios');
    if (titulo) titulo.innerHTML = '<i class="fa-solid fa-users-gear"></i> Usuarios Registrados';
};

window.eliminarUsuario = function (id) {
    if (confirm('¿Estás seguro de eliminar este usuario?')) {
        window.location.href = `CRUD/eliminarUsuario.php?id=${id}`;
    }
};

// ========== FUNCIONES PARA DISCIPLINAS ==========

window.cargarDisciplinas = function () {
    fetch('CRUD/obtenerDisciplinas.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tablaDisciplinasBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No hay disciplinas registradas</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(d => `
              <tr>
                <td>${escapeHtml(d.disciplina)}</td>
                <td>${escapeHtml(d.entrenador || 'Por Asignar')}</td>
                <td class="actions">
                  <button class="btn-icon delete" onclick="eliminarDisciplina(${d.id})"><i class="fa-regular fa-trash-can"></i></button>
                </td>
              </tr>
            `).join('');
        });
};

window.mostrarFormularioDisciplina = function () {
    const form = document.getElementById('formDisciplina');
    if (form) {
        form.classList.remove('hidden');
        document.getElementById('disciplinaNombre').value = '';

        const select = document.getElementById('disciplinaEntrenador');
        select.innerHTML = '<option value="">Cargando entrenadores...</option>';
        fetch('CRUD/obtenerEntrenadores.php')
            .then(res => res.json())
            .then(entrenadores => {
                select.innerHTML = '<option value="">Seleccione Entrenador...</option>' +
                    entrenadores.map(e => `<option value="${e.id}">${escapeHtml(e.nombre)}</option>`).join('');
            });
    }
};

window.ocultarFormularioDisciplina = function () {
    const form = document.getElementById('formDisciplina');
    if (form) form.classList.add('hidden');
};

window.guardarDisciplina = function () {
    const nombre = document.getElementById('disciplinaNombre').value;
    const entrenador_id = document.getElementById('disciplinaEntrenador').value;

    if (!nombre) {
        alert('Por favor ingrese el nombre de la disciplina');
        return;
    }

    const formData = new FormData();
    formData.append('nombre_disciplina', nombre);
    if (entrenador_id) {
        formData.append('entrenador_id', entrenador_id);
    }

    fetch('CRUD/insertarDisciplina.php', {
        method: 'POST',
        body: formData
    })
        .then(() => {
            alert('Disciplina guardada exitosamente');
            ocultarFormularioDisciplina();
            cargarDisciplinas();
        })
        .catch(error => alert('Error al guardar disciplina'));
};

window.eliminarDisciplina = function (id) {
    if (confirm('¿Estás seguro de eliminar esta disciplina?')) {
        fetch('CRUD/eliminarDisciplina.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Disciplina eliminada');
                    cargarDisciplinas();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
};

// ========== FUNCIONES PARA ENTRENADORES ==========

window.cargarEntrenadores = function () {
    fetch('CRUD/obtenerEntrenadores.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tablaEntrenadoresBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No hay entrenadores</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(e => `
              <tr>
                <td>${escapeHtml(e.nombre)} ${escapeHtml(e.apellidos || '')}</td>
                <td>Entrenador / Docente</td>
                <td>${escapeHtml(e.email || 'No especificado')}</td>
                <td><span class="status-pill status-active">Activo</span></td>
                <td class="actions">
                  <button class="btn-icon delete" onclick="eliminarEntrenador(${e.id})"><i class="fa-regular fa-trash-can"></i></button>
                </td>
              </tr>
            `).join('');
        });
};

window.mostrarFormularioEntrenador = function () {
    const form = document.getElementById('formEntrenador');
    if (form) {
        form.classList.remove('hidden');
        const select = document.getElementById('selectUsuarioEntrenador');
        select.innerHTML = '<option value="">Cargando candidatos...</option>';
        fetch('CRUD/obtenerCandidatosEntrenador.php')
            .then(res => res.json())
            .then(candidatos => {
                select.innerHTML = '<option value="">Seleccione usuario...</option>' +
                    candidatos.map(c => `<option value="${c.id}">${escapeHtml(c.nombre)} ${escapeHtml(c.apellidos || '')}</option>`).join('');
            });
    }
};

window.ocultarFormularioEntrenador = function () {
    const form = document.getElementById('formEntrenador');
    if (form) form.classList.add('hidden');
};

window.guardarEntrenador = function () {
    const id = document.getElementById('selectUsuarioEntrenador').value;
    if (!id) {
        alert('Seleccione un usuario para promover');
        return;
    }

    fetch('CRUD/promoverAEntrenador.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Usuario promovido a Entrenador exitosamente');
                ocultarFormularioEntrenador();
                cargarEntrenadores();

                // Actualizar tabla principal de usuarios si existe la fila
                const filaUsuario = document.getElementById('user-row-' + id);
                if (filaUsuario) {
                    const celdaRol = filaUsuario.querySelector('.user-rol-cell');
                    if (celdaRol) celdaRol.textContent = 'Docente';
                }
            } else {
                alert('Error: ' + data.message);
            }
        });
};

window.eliminarEntrenador = function (id) {
    if (confirm('¿Estás seguro de remover a este usuario del rol de Entrenador? (Será asignado como Alumno)')) {
        fetch('CRUD/removerEntrenador.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Entrenador removido. Ahora tiene rol de Alumno.');
                    cargarEntrenadores();

                    // Actualizar tabla principal de usuarios si existe la fila
                    const filaUsuario = document.getElementById('user-row-' + id);
                    if (filaUsuario) {
                        const celdaRol = filaUsuario.querySelector('.user-rol-cell');
                        if (celdaRol) celdaRol.textContent = 'Alumno';
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
};

// ========== FUNCIONES DE BÚSQUEDA ==========

function inicializarBuscadores() {
    const buscadores = [
        { inputId: 'buscarUsuario', tablaId: '#tablaUsuariosBody tr' },
        { inputId: 'buscarDisciplina', tablaId: '#tablaDisciplinasBody tr' },
        { inputId: 'buscarEntrenador', tablaId: '#tablaEntrenadoresBody tr' }
    ];

    buscadores.forEach(({ inputId, tablaId }) => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('keyup', () => {
                const valor = input.value.toLowerCase();
                filtrarFilas(document.querySelectorAll(tablaId), valor);
            });
        }
    });

    // Buscador de préstamos (usa filtrarPrestamos para considerar todos los filtros)
    const inputPrestamo = document.getElementById('buscarPrestamo');
    if (inputPrestamo) {
        inputPrestamo.addEventListener('keyup', filtrarPrestamos);
    }
}

function filtrarFilas(filas, valor) {
    filas.forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(valor) ? '' : 'none';
    });
}

// ========== FUNCIONES UTILITARIAS ==========

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatFecha(fechaStr) {
    if (!fechaStr) return 'Fecha no especificada';

    let fechaParte = fechaStr.includes(' ') ? fechaStr.split(' ')[0] : fechaStr;
    const partes = fechaParte.split('-');

    if (partes.length === 3) {
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }

    return fechaStr;
}