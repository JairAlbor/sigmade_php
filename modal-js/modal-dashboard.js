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
let _materialesCache = []; // cache global para búsqueda en tiempo real

function openModalPrestamo() {
    const modal = document.getElementById('modalPrestamo');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';

    // Default: inicio = ahora, límite = hoy a las 6 PM
    const ahora = new Date();
    const limite = new Date();
    limite.setHours(18, 0, 0, 0);
    const tzoffset = ahora.getTimezoneOffset() * 60000;
    const inputInicio = document.getElementById('fechaInicioPrestamo');
    const inputLimite = document.getElementById('fechaLimitePrestamo');
    inputInicio.value = (new Date(ahora - tzoffset)).toISOString().slice(0,16);
    inputLimite.value = (new Date(limite - tzoffset)).toISOString().slice(0,16);

    if (!inputInicio.dataset.listenerFixed) {
        inputInicio.addEventListener('change', cargarMaterialesDisponibles);
        inputLimite.addEventListener('change', cargarMaterialesDisponibles);
        inputInicio.dataset.listenerFixed = "true";
    }

    // Limpiar buscador
    const searchInput = document.getElementById('searchModalMaterial');
    if (searchInput) searchInput.value = '';

    cargarMaterialesDisponibles();
}
function closeModalPrestamo() {
    const modal = document.getElementById('modalPrestamo');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}
function cargarMaterialesDisponibles() {
    const inicio = document.getElementById('fechaInicioPrestamo').value;
    const limite = document.getElementById('fechaLimitePrestamo').value;
    let url = 'CRUD/obtenerMaterialesDisponibles.php?excludeCanchas=true';
    if(inicio && limite) {
        url += `&inicio=${encodeURIComponent(inicio)}&limite=${encodeURIComponent(limite)}`;
    }
    fetch(url)
        .then(res => res.json())
        .then(data => {
            _materialesCache = data;
            renderizarMateriales(data);
        })
        .catch(() => {
            document.getElementById('listaMaterialesDisponibles').innerHTML = '<p style="color:red;">Error cargando materiales</p>';
        });
}

function renderizarMateriales(data) {
    const container = document.getElementById('listaMaterialesDisponibles');
    if (!data || data.length === 0) {
        container.innerHTML = '<p style="text-align:center; color:rgba(var(--text-primary-rgb),0.5); padding:10px;"><i class="fa-solid fa-box-open"></i> No hay materiales libres disponibles</p>';
        return;
    }
    
    // Cambiar el contenedor para el grid
    container.classList.add('material-grid');
    container.classList.remove('lista-materiales-check'); // Limpiar estilos viejos si existen
    
    container.innerHTML = data.map(m => {
        const fotoUrl = m.foto_url ? m.foto_url : 'css/logoSigmade.png';
        const codigo = m.codigo_material || `MAT-${String(m.id).padStart(5,'0')}`;
        const disciplina = m.disciplina || '';
        return `
        <div class="material-card" onclick="toggleMaterialCard(this, '${m.id}')" id="card_${m.id}">
            <div class="material-card-checkbox">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="material-card-img-wrapper">
                <img src="${fotoUrl}" class="material-card-img" onerror="this.src='css/logoSigmade.png'">
            </div>
            <div class="material-card-info">
                <div class="material-card-name">${m.nombre}</div>
                <div class="material-card-meta">
                    <span style="color:var(--crimson-light); font-weight:600; font-size:0.65rem;">${codigo}</span>
                    <span>${disciplina}</span>
                </div>
            </div>
            <input type="checkbox" name="materiales" value="${m.id}" id="mat_${m.id}" style="display:none;">
        </div>
        `;
    }).join('');
}

window.toggleMaterialCard = function(card, materialId) {
    const checkbox = document.getElementById('mat_' + materialId);
    checkbox.checked = !checkbox.checked;
    card.classList.toggle('selected', checkbox.checked);
};

window.filtrarMaterialesEnModal = function(texto) {
    const filtrado = _materialesCache.filter(m => {
        const haystack = `${m.nombre} ${m.disciplina || ''} ${m.estado}`.toLowerCase();
        return haystack.includes(texto.toLowerCase());
    });
    renderizarMateriales(filtrado);
};
function registrarPrestamo() {
    const seleccionados = Array.from(document.querySelectorAll('input[name="materiales"]:checked')).map(cb => cb.value);
    const fechaInicio = document.getElementById('fechaInicioPrestamo').value;
    const fechaLimite = document.getElementById('fechaLimitePrestamo').value;

    if (seleccionados.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Sin material', text: 'Selecciona al menos un material', confirmButtonColor: '#8B1A2B' });
        return;
    }
    if (!fechaInicio || !fechaLimite) {
        Swal.fire({ icon: 'warning', title: 'Fechas incompletas', text: 'Debes seleccionar fecha de inicio y fecha límite', confirmButtonColor: '#8B1A2B' });
        return;
    }
    if (new Date(fechaLimite) <= new Date(fechaInicio)) {
        Swal.fire({ icon: 'error', title: 'Fechas inválidas', text: 'La fecha límite debe ser posterior a la fecha de inicio', confirmButtonColor: '#8B1A2B' });
        return;
    }
    if (!currentUserId) {
        Swal.fire({ icon: 'error', title: 'Error de sesión', text: 'Vuelve a iniciar sesión', confirmButtonColor: '#8B1A2B' });
        return;
    }

    const formData = new URLSearchParams();
    formData.append('usuario_id', currentUserId);
    formData.append('fecha_inicio', fechaInicio);
    formData.append('fecha_limite', fechaLimite);
    seleccionados.forEach(id => formData.append('materiales[]', id));

    fetch('CRUD/registrarPrestamo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud Enviada!',
                text: 'Tu préstamo está en espera de aprobación del administrador.',
                confirmButtonColor: '#8B1A2B'
            }).then(() => closeModalPrestamo());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'No se pudo registrar',
                html: `<p>${data.message}</p>`,
                confirmButtonColor: '#8B1A2B'
            });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor', confirmButtonColor: '#8B1A2B' });
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
            container.classList.add('material-grid');
            container.innerHTML = data.map(e => {
                const fotoUrl = e.foto_url ? e.foto_url : 'css/logoSigmade.png';
                return `
                <div class="material-card" onclick="toggleEspacioCard(this, '${e.id}')" id="espcard_${e.id}">
                    <div class="material-card-checkbox">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="material-card-img-wrapper">
                        <img src="${fotoUrl}" class="material-card-img" onerror="this.src='css/logoSigmade.png'">
                    </div>
                    <div class="material-card-info">
                        <div class="material-card-name">${e.nombre}</div>
                        <div class="material-card-meta">
                            <span>Espacio Deportivo</span>
                        </div>
                    </div>
                    <input type="radio" name="espacioRadio" value="${e.id}" id="espc_${e.id}" required style="display:none;">
                </div>
                `;
            }).join('');
        });
}

window.toggleEspacioCard = function(card, espacioId) {
    const parent = card.parentElement;
    parent.querySelectorAll('.material-card').forEach(c => c.classList.remove('selected'));
    
    const radio = document.getElementById('espc_' + espacioId);
    if (radio) radio.checked = true;
    card.classList.add('selected');
};
function registrarReserva() {
    const selector = document.querySelector('input[name="espacioRadio"]:checked');
    const espacioId = selector ? selector.value : null;
    const inicio = document.getElementById('fechaInicioReserva').value;
    const fin = document.getElementById('fechaFinReserva').value;
    const motivo = document.getElementById('motivoReserva').value;

    if (!espacioId) {
        SIGMADE_UI.warning('Cancha no seleccionada', 'Por favor, elige una cancha para continuar.');
        return;
    }
    if (!inicio || !fin || !motivo) {
        SIGMADE_UI.warning('Campos incompletos', 'Rellena todos los campos para solicitar la reserva.');
        return;
    }
    if (new Date(fin) <= new Date(inicio)) {
        SIGMADE_UI.error('Fechas inválidas', 'La fecha de fin debe ser posterior a la de inicio.');
        return;
    }
    if (!currentUserId) {
        SIGMADE_UI.error('Error de sesión', 'No se detectó el usuario. Por favor inicia sesión.');
        return;
    }

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
                Swal.fire({
                    icon: 'success',
                    title: '¡Reserva Solicitada!',
                    text: 'Tu solicitud ha sido enviada al administrador.',
                    confirmButtonColor: '#a82035'
                }).then(() => closeModalReserva());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo reservar',
                    text: data.message,
                    confirmButtonColor: '#a82035'
                });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo conectar con el servidor', confirmButtonColor: '#a82035' });
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
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.fecha_inicio}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">${p.fecha_limite}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <span class="badge ${p.estado_general === 'Pendiente' ? 'bg-warning' : (p.estado_general === 'Rechazado' ? 'bg-danger' : 'bg-primary')}">${p.estado_general}</span>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                        ${(p.estado_general === 'Pendiente' || p.estado_general === 'Aprobado') 
                          ? `<button onclick="cancelarPrestamo(${p.prestamo_id})" style="background:transparent; border:none; color:var(--crimson-light); cursor:pointer;" title="Cancelar Solicitud"><i class="fa-solid fa-trash-can"></i></button>` 
                          : `<span style="color:#aaa; font-size: 0.8rem;">-</span>`}
                    </td>
                </tr>
            `).join('');
        });
}

function cancelarPrestamo(prestamoId) {
    SIGMADE_UI.confirm('¿Deseas cancelar?', 'Esta acción retirará tu solicitud de préstamo. No se puede deshacer.', 'Sí, cancelar')
    .then((result) => {
        if (result.isConfirmed) {
            fetch('CRUD/cancelarPrestamoEstudiante.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prestamo_id: prestamoId, usuario_id: currentUserId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    SIGMADE_UI.success('¡Cancelado!', data.message, 2000).then(() => {
                        if (typeof cargarHistorial === 'function') cargarHistorial(currentUserId);
                        else if (typeof cargarHistorialUsuario === 'function') cargarHistorialUsuario(currentUserId);
                        verificarAdeudos(currentUserId);
                    });
                } else {
                    SIGMADE_UI.error('Error', data.message);
                }
            })
            .catch(err => {
                console.error("Error al cancelar préstamo: ", err);
                SIGMADE_UI.error('Error de red', 'No se pudo procesar la cancelación.');
            });
        }
    });
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
                // Cambiar diseño si hay préstamo pendiente o activo mediante clases CSS
                card.classList.remove('state-success');
                card.classList.add('state-warning');
                // Se limpian overrides inline que bloqueaban colores
                card.style = '';
                title.style = '';
                subtext.style = '';
                
                // Actualizar icono
                icon.outerHTML = '<i class="fa-solid fa-bell check-icon" id="adeudosIcon"></i>';
                
                let textoEstado = prestamoActivo.estado_general === 'Pendiente' ? 'Préstamo Solicitado (Pendiente)' : 'Préstamo en Curso';
                title.textContent = textoEstado;
                
                subtext.innerHTML = `<strong>Material:</strong> ${prestamoActivo.materiales}<br>
                                     <strong>Límite:</strong> ${prestamoActivo.fecha_limite}`;
            } else {
                // Restaurar estado normal ("al día")
                card.classList.remove('state-warning');
                card.classList.add('state-success');
                card.style = '';
                title.style = '';
                subtext.style = '';
                
                icon.outerHTML = '<i class="fa-solid fa-award check-icon" id="adeudosIcon"></i>';
                
                title.textContent = 'Sin adeudos pendientes';
                subtext.textContent = 'Tu cuenta se encuentra al día.';
            }
        })
        .catch(err => console.error("Error al verificar adeudos: ", err));
}