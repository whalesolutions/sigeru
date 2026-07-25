const formulario = document.getElementById("registroUForm");
const mensajeRegistro = document.getElementById("mensajeRegistro");
const btnGuardarEmpleado = document.getElementById("btnGuardarEmpleado");

const inputContrasena = document.getElementById("contrasena");
const inputConfirmarContrasena =
    document.getElementById("confirmarContrasena");

const btnMostrarContrasena =
    document.getElementById("btnMostrarContrasena");

const iconoContrasena =
    document.getElementById("iconoContrasena");


/*
|--------------------------------------------------------------------------
| Mostrar u ocultar contraseña
|--------------------------------------------------------------------------
*/

btnMostrarContrasena.addEventListener("click", function () {

    const contraseñaOculta =
        inputContrasena.type === "password";

    if (contraseñaOculta) {

        inputContrasena.type = "text";
        inputConfirmarContrasena.type = "text";

        iconoContrasena.classList.remove("bi-eye");
        iconoContrasena.classList.add("bi-eye-slash");

        btnMostrarContrasena.setAttribute(
            "aria-label",
            "Ocultar contraseña"
        );

    } else {

        inputContrasena.type = "password";
        inputConfirmarContrasena.type = "password";

        iconoContrasena.classList.remove("bi-eye-slash");
        iconoContrasena.classList.add("bi-eye");

        btnMostrarContrasena.setAttribute(
            "aria-label",
            "Mostrar contraseña"
        );
    }

});

/*
|--------------------------------------------------------------------------
| Envío del formulario
|--------------------------------------------------------------------------
*/

formulario.addEventListener("submit", async function (event) {

    event.preventDefault();

    ocultarMensaje();

    /*
    |--------------------------------------------------------------------------
    | Validación de Bootstrap
    |--------------------------------------------------------------------------
    */

    if (!formulario.checkValidity()) {

        formulario.classList.add("was-validated");

        mostrarMensaje(
            "Revisá los campos obligatorios del formulario.",
            "danger"
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener los datos
    |--------------------------------------------------------------------------
    */

    const nombre =
        document.getElementById("nombre").value.trim();
    const apellido =
        document.getElementById("apellido").value.trim();
    const cedula =
        document.getElementById("cedula").value.trim();
    const telefono =
        document.getElementById("telefono").value.trim();
    const correo =
        document.getElementById("correo").value.trim();
    const contrasena =
        inputContrasena.value;
    const confirmarContrasena =
        inputConfirmarContrasena.value;
    const idRol =
        document.getElementById("idRol").value;


    /*
    |--------------------------------------------------------------------------
    | Validaciones propias
    |--------------------------------------------------------------------------
    */

    if (!validarSoloNumeros(cedula)) {

        mostrarMensaje(
            "La cédula debe contener solamente números.",
            "danger"
        );

        return;
    }

    if (cedula.length < 7 || cedula.length > 8) {

        mostrarMensaje(
            "La cédula debe tener entre 7 y 8 números.",
            "danger"
        );

        return;
    }

    if (!validarSoloNumeros(telefono)) {

        mostrarMensaje(
            "El teléfono debe contener solamente números.",
            "danger"
        );

        return;
    }

    if (contrasena.length < 6) {

        mostrarMensaje(
            "La contraseña debe tener al menos 6 caracteres.",
            "danger"
        );

        return;
    }

    if (contrasena !== confirmarContrasena) {

        mostrarMensaje(
            "Las contraseñas no coinciden.",
            "danger"
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Datos enviados a UsuarioController.php
    |--------------------------------------------------------------------------
    */

    const empleado = {
        nombre: nombre,
        apellido: apellido,
        documento: cedula,
        telefono: telefono,
        correo: correo,
        contrasena: contrasena,
        rol: idRol
    };


    /*
    |--------------------------------------------------------------------------
    | Petición a la API
    |--------------------------------------------------------------------------
    */

    try {

        cambiarEstadoBoton(true);

        mostrarMensaje(
            "Guardando empleado",
            "info"
        );

        const respuesta = await fetch(
            "/sigeru/api-usuarios/index.php/usuarios",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(empleado)
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Leer la respuesta del servidor
        |--------------------------------------------------------------------------
        */

        const textoRespuesta = await respuesta.text();

        let resultado;

        try {

            resultado = textoRespuesta
                ? JSON.parse(textoRespuesta)
                : {};

        } catch (error) {

            console.error(
                "La API no devolvió un JSON válido:",
                textoRespuesta
            );

            throw new Error(
                "La respuesta del servidor no tiene un formato válido."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Error enviado por la API
        |--------------------------------------------------------------------------
        */

        if (!respuesta.ok) {

            mostrarMensaje(
                resultado.mensaje ||
                resultado.message ||
                resultado.error ||
                "No se pudo guardar el empleado.",
                "danger"
            );
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Registro correcto
        |--------------------------------------------------------------------------
        */

        mostrarMensaje(
            resultado.mensaje ||
            "Empleado registrado correctamente.",
            "success"
        );

        formulario.reset();
        formulario.classList.remove("was-validated");

        inputContrasena.type = "password";
        inputConfirmarContrasena.type = "password";

        iconoContrasena.classList.remove("bi-eye-slash");
        iconoContrasena.classList.add("bi-eye");

    } catch (error) {

        console.error(
            "Error al registrar el empleado:",
            error
        );

        mostrarMensaje(
            error.message ||
            "No se pudo conectar con la API de usuarios.",
            "danger"
        );

    } finally {

        cambiarEstadoBoton(false);

    }

});
/*
|--------------------------------------------------------------------------
| Validar que un dato contenga solamente números
|--------------------------------------------------------------------------
*/
function validarSoloNumeros(valor) {

    return /^[0-9]+$/.test(valor);

}
/*
|--------------------------------------------------------------------------
| Mostrar mensajes con Bootstrap
|--------------------------------------------------------------------------
*/
function mostrarMensaje(texto, tipo) {

    mensajeRegistro.textContent = texto;

    mensajeRegistro.className =
        `alert alert-${tipo} mt-4`;

}

/*
|--------------------------------------------------------------------------
| Ocultar mensajes
|--------------------------------------------------------------------------
*/
function ocultarMensaje() {

    mensajeRegistro.textContent = "";

    mensajeRegistro.className =
        "alert d-none mt-4";

}

/*
|--------------------------------------------------------------------------
| Bloquear el botón mientras se guarda
|--------------------------------------------------------------------------
*/
function cambiarEstadoBoton(guardando) {

    if (guardando) {

        btnGuardarEmpleado.disabled = true;

        btnGuardarEmpleado.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                aria-hidden="true">
            </span>
            Guardando...
        `;

    } else {

        btnGuardarEmpleado.disabled = false;

        btnGuardarEmpleado.innerHTML = `
            <i class="bi bi-person-plus me-2"></i>
            Guardar empleado
        `;

    }

}