document.addEventListener('DOMContentLoaded', () => {
    // RECUPERAR el nombre
    const nombreGuardado = localStorage.getItem('nombreUsuario');
    if (nombreGuardado) {
        const saludoElemento = document.getElementById('userName');
        if (saludoElemento) {
            saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
        }
    }
    if (typeof currentUserId !== 'undefined' && currentUserId !== null) {
        verificarAdeudos(currentUserId);
    }
});

// ======= MODAL NUEVO PRESTAMO =======
function openModalPrestamo() {
    const modal = document.getElementById('modalPrestamo');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    // Default the date to today at 4 PM
    const inputFecha = document.getElementById('fechaLimitePrestamo');
    const hoy = new Date();
    hoy.setHours(16, 0, 0, 0); // 4 PM local
    const tzoffset = hoy.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(hoy - tzoffset)).toISOString().slice(0,16);
    inputFecha.value = localISOTime;

    cargarMaterialesDisponibles();
}
function closeModalPrestamo() {
    const modal = document.getElementById('modalPrestamo');
    modal.classList.add('hidden');
    modal.style.display = 'none';
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
            container.innerHTML = data.map(m => {
                const fotoUrl = m.foto_url ? m.foto_url : 'css/logoSigmade.png';
                return `
                <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <input type="checkbox" name="materiales" value="${m.id}" id="mat_${m.id}">
                    <img src="${fotoUrl}" onerror="this.src='css/logoSigmade.png'" style="width: 80px; height: 70px; object-fit: cover; border-radius: 5px;">
                    <label for="mat_${m.id}" style="cursor: pointer; display:flex; flex-direction:column;">
                        <strong>${m.nombre}</strong> 
                        <small style="color: #666;">Estado: ${m.estado}</small>
                    </label>
                </div>
                `;
            }).join('');
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
    const modal = document.getElementById('modalReserva');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    cargarEspacios();
}
function closeModalReserva() {
    const modal = document.getElementById('modalReserva');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}
function cargarEspacios() {
    fetch('CRUD/obtenerEspacios.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('listaEspaciosDisponibles');
            if (data.length === 0) {
                container.innerHTML = '<p>No hay canchas libres</p>';
                return;
            }
            container.innerHTML = data.map(e => {
                const fotoUrl = e.foto_url ? e.foto_url : 'css/logoSigmade.png';
                return `
                <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <input type="radio" name="espacioRadio" value="${e.id}" id="espc_${e.id}" required>
                    <img src="${fotoUrl}" onerror="this.src='css/logoSigmade.png'" style="width: 80px; height: 70px; object-fit: cover; border-radius: 5px;">
                    <label for="espc_${e.id}" style="cursor: pointer; display:flex; flex-direction:column;">
                        <strong>${e.nombre}</strong> 
                    </label>
                </div>
                `;
            }).join('');
        });
}
function registrarReserva() {
    const selector = document.querySelector('input[name="espacioRadio"]:checked');
    const espacioId = selector ? selector.value : null;
    const inicio = document.getElementById('fechaInicioReserva').value;
    const fin = document.getElementById('fechaFinReserva').value;
    const motivo = document.getElementById('motivoReserva').value;

    if (!espacioId) return alert('Debes seleccionar una cancha');
    if (!inicio || !fin || !motivo) return alert('Campos incompletos');
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
    const modal = document.getElementById('modalEventosDashboard');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    cargarEventosDashboard();
}
function closeModalEventos() {
    const modal = document.getElementById('modalEventosDashboard');
    modal.classList.add('hidden');
    modal.style.display = 'none';
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
    const modal = document.getElementById('modalHistorial');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    if (currentUserId) cargarHistorial(currentUserId);
}

function closeModalHistorial() {
    const modal = document.getElementById('modalHistorial');
    modal.classList.add('hidden');
    modal.style.display = 'none';
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
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                        ${(p.estado_general === 'Pendiente' || p.estado_general === 'Aprobado') 
                          ? `<button onclick="cancelarPrestamo(${p.prestamo_id})" style="background:transparent; border:none; color:red; cursor:pointer;" title="Cancelar Solicitud"><i class="fa-solid fa-ban"></i></button>` 
                          : `<span style="color:#aaa; font-size: 0.8rem;">-</span>`}
                    </td>
                </tr>
            `).join('');
        });
}

function cancelarPrestamo(prestamoId) {
    if (!confirm('¿Estás seguro de que deseas cancelar esta solicitud?')) return;

    fetch('CRUD/cancelarPrestamoEstudiante.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prestamo_id: prestamoId, usuario_id: currentUserId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            cargarHistorial(currentUserId);
            verificarAdeudos(currentUserId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => console.error("Error al cancelar préstamo: ", err));
}

// ======= VERIFICAR ADEUDOS ========
function verificarAdeudos(userId) {
    if (!userId) return;
    
    fetch('CRUD/obtenerHistorialUsuario.php?usuario_id=' + userId)
        .then(res => res.json())
        .then(data => {
            const prestamoActivo = data.find(p => p.estado_general === 'Pendiente' || p.estado_general === 'Activo' || p.estado_general === 'Prestado');
            
            const card = document.getElementById('adeudosCard');
            const icon = document.getElementById('adeudosIcon');
            const title = document.getElementById('adeudosTitle');
            const subtext = document.getElementById('adeudosSubtext');
            
            if (prestamoActivo) {
                // Cambiar diseño si hay préstamo pendiente o activo
                card.style.backgroundColor = '#fff3cd'; /* Tono advertencia */
                card.style.borderColor = '#ffe69c';
                card.style.color = '#664d03';
                
                // Actualizar icono (usamos FontAwesome porque Lucide requiere recarga)
                icon.outerHTML = '<i class="fa-solid fa-bell check-icon" id="adeudosIcon" style="color: #ffc107;"></i>';
                
                let textoEstado = prestamoActivo.estado_general === 'Pendiente' ? 'Préstamo Solicitado (Pendiente)' : 'Préstamo en Curso';
                title.textContent = textoEstado;
                title.style.color = '#856404';
                
                subtext.innerHTML = `<strong>Material:</strong> ${prestamoActivo.materiales}<br>
                                     <strong>Límite:</strong> ${prestamoActivo.fecha_limite}`;
                subtext.style.color = '#856404';
            } else {
                // Restaurar o asegurar estado normal ("al día")
                card.style.backgroundColor = '#d4edda';
                card.style.borderColor = '#c3e6cb';
                card.style.color = '#155724';
                
                icon.outerHTML = '<i class="fa-solid fa-award check-icon" id="adeudosIcon" style="color: #28a745;"></i>';
                
                title.textContent = 'Sin adeudos pendientes';
                title.style.color = '#155724';
                
                subtext.textContent = 'Tu cuenta se encuentra al día.';
                subtext.style.color = '#155724';
            }
        })
        .catch(err => console.error("Error al verificar adeudos: ", err));
}