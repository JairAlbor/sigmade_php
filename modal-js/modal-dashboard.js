document.addEventListener('DOMContentLoaded', () => {
    // RECUPERAR el nombre
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    if (nombreGuardado) {
        const saludoElemento = document.getElementById('userName');
        if (saludoElemento) {
            saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
        }
    }
});

// ======= MODAL NUEVO PRESTAMO =======
function openModalPrestamo() {
    document.getElementById('modalPrestamo').style.display = 'block';
    cargarMaterialesDisponibles();
}
function closeModalPrestamo() {
    document.getElementById('modalPrestamo').style.display = 'none';
}
function cargarMaterialesDisponibles() {
    fetch('CRUD/obtenerMaterialesDisponibles.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('listaMaterialesDisponibles');
            if (data.length === 0) {
                container.innerHTML = '<p>No hay materiales libres</p>';
                return;
            }
            container.innerHTML = data.map(m => `
                <div style="margin-bottom:5px;">
                    <input type="checkbox" name="materiales" value="${m.id}" id="mat_${m.id}">
                    <label for="mat_${m.id}">${m.nombre} (${m.estado})</label>
                </div>
            `).join('');
        });
}
function registrarPrestamo() {
    const seleccionados = Array.from(document.querySelectorAll('input[name="materiales"]:checked')).map(cb => cb.value);
    const fechaLimite = document.getElementById('fechaLimitePrestamo').value;

    if (seleccionados.length === 0) return alert('Selecciona al menos un material');
    if (!fechaLimite) return alert('Selecciona una fecha límite');
    if (!currentUserId) return alert('Error de sesión');

    const formData = new URLSearchParams();
    formData.append('usuario_id', currentUserId);
    formData.append('fecha_limite', fechaLimite);
    seleccionados.forEach(id => formData.append('materiales[]', id));

    fetch('CRUD/registrarPrestamo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Préstamo solicitado. En espera de aprobación.');
                closeModalPrestamo();
            } else {
                alert(data.message || 'Error al solicitar el préstamo');
            }
        });
}

// ======= MODAL RESERVAR CANCHA =======
function openModalReserva() {
    document.getElementById('modalReserva').style.display = 'block';
    cargarEspacios();
}
function closeModalReserva() {
    document.getElementById('modalReserva').style.display = 'none';
}
function cargarEspacios() {
    fetch('CRUD/obtenerEspacios.php')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('selectEspacio');
            select.innerHTML = '<option value="">Seleccione...</option>' +
                data.map(e => `<option value="${e.id}">${e.nombre} (Capacidad: ${e.capacidad})</option>`).join('');
        });
}
function registrarReserva() {
    const espacioId = document.getElementById('selectEspacio').value;
    const inicio = document.getElementById('fechaInicioReserva').value;
    const fin = document.getElementById('fechaFinReserva').value;
    const motivo = document.getElementById('motivoReserva').value;

    if (!espacioId || !inicio || !fin || !motivo) return alert('Campos incompletos');
    if (!currentUserId) return alert('Error de sesión');

    fetch('CRUD/registrarReserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            espacio_id: espacioId,
            usuario_id: currentUserId,
            inicio: inicio,
            fin: fin,
            motivo: motivo
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Reserva solicitada correctamente');
                closeModalReserva();
            } else {
                alert(data.message);
            }
        });
}

// ======= MODAL MIS EVENTOS =======
function openModalEventos() {
    document.getElementById('modalEventosDashboard').style.display = 'block';
    cargarEventosDashboard();
}
function closeModalEventos() {
    document.getElementById('modalEventosDashboard').style.display = 'none';
}
function cargarEventosDashboard() {
    fetch('CRUD/obtenerEventos.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('listaEventos');
            if (data.length === 0) {
                container.innerHTML = '<p>No hay eventos registrados</p>';
                return;
            }
            container.innerHTML = data.map(e => `
                <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius:5px;">
                    <h3 style="margin-top:0;">${e.titulo}</h3>
                    <p><strong>Ubicación:</strong> ${e.ubicacion}</p>
                    <p><strong>Fecha:</strong> ${e.fecha} ${e.hora}</p>
                    <p>${e.descripcion}</p>
                </div>
            `).join('');
        });
}

// ======= MODAL HISTORIAL =======
function openModalHistorial() {
    document.getElementById('modalHistorial').style.display = 'block';
    if (currentUserId) cargarHistorial(currentUserId);
}

function closeModalHistorial() {
    document.getElementById('modalHistorial').style.display = 'none';
}

function cargarHistorial(userId) {
    fetch('CRUD/obtenerHistorialUsuario.php?usuario_id=' + userId)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#tablaHistorialUsuario tbody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:10px;">No hay historial disponible</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(p => `
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.prestamo_id}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.materiales || 'N/A'}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.fecha_solicitud}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.fecha_limite}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <span class="badge ${p.estado_general === 'Pendiente' ? 'bg-warning' : (p.estado_general === 'Rechazado' ? 'bg-danger' : 'bg-primary')}">${p.estado_general}</span>
                    </td>
                </tr>
            `).join('');
        });
}