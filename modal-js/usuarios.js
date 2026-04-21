function guardarUsuario(event) {
    event.preventDefault();

    const form = document.getElementById('formUsuarios');
    const formData = new FormData(form);

    // Validaciones básicas
    const rol = formData.get('rol');
    const nombre = formData.get('nombre');
    const apellidos = formData.get('apellidos');
    const iden = formData.get('iden');
    const tel = formData.get('tel');
    const email = formData.get('email');
    const password = formData.get('password');

    if (!rol || !nombre || !apellidos || !iden || !tel || !email || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, rellene todos los campos obligatorios.',
            confirmButtonColor: '#a82035'
        });
        return;
    }

    // Feedback visual
    const btn = document.getElementById('Registrar');
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = 'Registrando...';

    fetch('CRUD/registrarUsuario.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = originalText;

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                confirmButtonColor: '#a82035'
            }).then(() => {
                // Limpiar formulario y cerrar el panel de registro (regresar al login)
                form.reset();
                const flipToggle = document.getElementById('flip-toggle');
                if (flipToggle) flipToggle.checked = false;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#a82035'
            });
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerText = originalText;
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo contactar con el servidor.',
            confirmButtonColor: '#a82035'
        });
    });
}
