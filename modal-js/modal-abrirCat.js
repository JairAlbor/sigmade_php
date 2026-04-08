/**codigo para conseguir el nombre del usuario */

document.addEventListener('DOMContentLoaded', () => {
    // RECUPERAR el nombre
    const nombreGuardado = localStorage.getItem('nombreUsuario');

    // Verificar si existe (por seguridad)
    if (nombreGuardado) {
        // Ejemplo: Ponerlo en un elemento con id="bienvenida"
        const saludoElemento = document.getElementById('userName');
        if (saludoElemento) {
            saludoElemento.textContent = `Bienvenido, ${nombreGuardado}`;
        }

        // Si quieres usarlo dentro de un modal específico:
        console.log("Nombre listo para usar en modales:", nombreGuardado);
    } else {
        // Si no hay nombre, quizá el usuario no se ha logueado
        ///  window.location.href = 'login.html'; 
    }
});


const btnAbrir = document.getElementById('btn-abrir-formulario');
const btnCancelar = document.getElementById('btn-cancelar');
const contenedorForm = document.getElementById('contenedor-formulario');
const btnGuardar = document.getElementById('btn-exito');
// Mostrar formulario
btnAbrir.addEventListener('click', () => {
    contenedorForm.classList.remove('hidden');
});

// Ocultar formulario
btnCancelar.addEventListener('click', () => {
    contenedorForm.classList.add('hidden');
});

btnGuardar.addEventListener('click', () => {
    contenedorForm.classList.add('hidden');
    // codigo para prellenar el formulario de actualizar con los datos del articulo seleccionado


});

/*codigo para controlar el modal */
// Función principal para manejar el modal
function modalActualizar() {
    // Elementos del DOM
    const btnCancelar = document.getElementById('btn-cancelar');
    const contenedorForm = document.getElementById('contenedor-formulario');
    const btnGuardar = document.getElementById('btn-exito');

    // Variable para almacenar el ID del material a actualizar
    let materialId = null;

    // Función para abrir el modal y llenar los campos (se llama desde el botón Editar)
    window.abrirModalActualizacion = function (id, nombre, id_disciplina, estado, tipoMaterial, disponible) {
        // Guardar el ID
        materialId = id;

        // Llenar los campos del formulario
        document.getElementById('nombreArticuloAct').value = nombre;
        document.getElementById('disciplina').value = id_disciplina;
        document.getElementById('estadoAct').value = estado;
        document.getElementById('tipoMaterialAct').value = tipoMaterial;
        document.getElementById('disponibleAct').value = disponible;

        // Mostrar el modal
        contenedorForm.classList.remove('hidden');
    };

    // Función para cerrar el modal
    function cerrarModal() {
        contenedorForm.classList.add('hidden');
        document.getElementById('formArticuloAct').reset();
        materialId = null;
    }

    // Ocultar formulario (Cancelar)
    btnCancelar.addEventListener('click', cerrarModal);

    // Guardar cambios
    btnGuardar.addEventListener('click', () => {
        // Crear FormData con los datos del formulario
        const formData = new FormData();
        formData.append('id', materialId);
        formData.append('nombreArticulo', document.getElementById('nombreArticuloAct').value);
        formData.append('disciplina', document.getElementById('disciplina').value);
        formData.append('estado', document.getElementById('estadoAct').value);
        formData.append('tipoMaterial', document.getElementById('tipoMaterialAct').value);
        formData.append('disponibleAct', document.getElementById('disponibleAct').value);

        // Enviar mediante fetch
        fetch('CRUD/actualizarMat.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Material actualizado correctamente');
                    cerrarModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión');
            });
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !contenedorForm.classList.contains('hidden')) {
            cerrarModal();
        }
    });
}

// Inicializar la función cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', modalActualizar);