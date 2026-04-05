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
/**********************************************/
// Función para abrir y cerrar (Ya la tienes, pero asegúrate de que sea global)
window.toggleModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.toggle('hidden');
};

// Buscador en tiempo real dentro del modal de usuarios
document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.getElementById('buscarUsuario');
    const filasUsuarios = document.querySelectorAll('#tablaUsuariosBody tr');

    if (inputBusqueda) {
        inputBusqueda.addEventListener('keyup', () => {
            const valor = inputBusqueda.value.toLowerCase();
            filasUsuarios.forEach(fila => {
                const contenido = fila.textContent.toLowerCase();
                fila.style.display = contenido.includes(valor) ? '' : 'none';
            });
        });
    }
});

// Funciones para las acciones (puedes desarrollarlas después)
function abrirEditarUsuario(id) {
    console.log("Editando usuario ID:", id);
    // Aquí podrías abrir OTRO modal para editar los datos del usuario
}

function eliminarUsuario(id) {
    if(confirm("¿Estás seguro de eliminar este usuario?")) {
        window.location.href = `CRUD/eliminarUsuario.php?id=${id}`;
    }
}