const formulario = document.getElementById("formAltaContenedor");
const mensajeContenedor = document.getElementById("mensajeContenedor");
const btnGuardarContenedor = document.getElementById("btnGuardarContenedor");
const btnLimpiarContenedor = document.getElementById("btnLimpiarContenedor");
const inputCodigo = document.getElementById("codigo");
const inputDireccion = document.getElementById("direccion");
const inputLatitud = document.getElementById("latitud");
const inputLongitud = document.getElementById("longitud");
const selectEstado = document.getElementById("estado");
const inputCapacidadMaxima = document.getElementById("capacidadMaxima");
const inputCapacidadActual = document.getElementById("capacidadActual");


formulario.addEventListener("submit", async function (evento) {
    evento.preventDefault();

    ocultarMensaje();
    normalizarCampos();

    if (!formulario.checkValidity()) {
        formulario.classList.add("was-validated");
        return;
    }

    const codigo = inputCodigo.value.trim().toUpperCase();
    const direccion = inputDireccion.value.trim();
    const latitud = Number(inputLatitud.value);
    const longitud = Number(inputLongitud.value);
    const estado = selectEstado.value;
    const capacidadMaxima = Number(inputCapacidadMaxima.value);
    const capacidadActual = Number(inputCapacidadActual.value);

    /* Validación de la identificacion del contenedor osea el formato*/
    if (!codigoValido(codigo)) {
        mostrarMensaje(
            "El código solo puede contener letras, números y guiones.",
            "danger"
        );

        inputCodigo.focus();
        return;
    }

     /* Validación para el futuro aunque  no deberia usarse ya que deberia crearse con capacidad actual 0 por ser nuevo a menos que se agregue uno al recorrido  que antes no estaba o se mueva*/
    if (capacidadActual > capacidadMaxima) {
        mostrarMensaje(
            "La capacidad actual no puede superar la capacidad máxima.",
            "danger"
        );

        inputCapacidadActual.focus();
        return;
    }

    const contenedor = {
        codigo,
        direccion,
        latitud,
        longitud,
        estado,
        capacidadMaxima,
        capacidadActual
    };

    /* Se hace el consumo de la API para crear el contenedor */
    try {
        bloquearFormulario(true);

        const respuesta = await fetch(
            "../api-gestion/index.php/contenedores",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(contenedor)
            }
        );

        const resultado = await respuesta.json();

        if (!respuesta.ok || resultado.error) {
            mostrarMensaje(
                resultado.mensaje ||
                "No se pudo registrar el contenedor.",
                "danger"
            );

            return;
        }

        mostrarMensaje(
            resultado.mensaje ||
            "Contenedor registrado correctamente.",
            "success"
        );

        formulario.reset();
        formulario.classList.remove("was-validated");

        inputCapacidadActual.value = 0;
        inputCodigo.focus();

        console.log(
            "Contenedor creado:",
            resultado.contenedor
        );
    } catch (error) {
        console.error(
            "Error al registrar el contenedor:",
            error
        );
        mostrarMensaje(
            "No se pudo conectar con la API de contenedores.",
            "danger"
        );
    } finally {
        bloquearFormulario(false);
    }
});

/*Un simple boton para limpiar el formulario manualmente.*/
btnLimpiarContenedor.addEventListener(
    "click",
    function () {
        formulario.classList.remove("was-validated");
        ocultarMensaje();
    }
);


/*Función para eliminar los espacios al principio y al final y pasar el código a mayúsculas.*/
function normalizarCampos() {
    inputCodigo.value = inputCodigo.value
        .trim()
        .toUpperCase();
    inputDireccion.value =
        inputDireccion.value.trim();
}


function mostrarMensaje(mensaje, tipo) {
    mensajeContenedor.textContent = mensaje;
    mensajeContenedor.className =
        `alert alert-${tipo} mt-4`;
}


function ocultarMensaje() {
    mensajeContenedor.textContent = "";
    mensajeContenedor.className =
        "alert d-none mt-4";
}


/*Función para bloquear los botones mientrasse realiza el registro y que el usuario no pueda apretarlo varias veces por error o si se demora. */
function bloquearFormulario(bloquear) {
    btnGuardarContenedor.disabled = bloquear;
    btnLimpiarContenedor.disabled = bloquear;

    if (bloquear) {
        btnGuardarContenedor.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true">
            </span>
            Guardando...
        `;
    } else {
        btnGuardarContenedor.innerHTML = `
        <i class="bi bi-trash3-fill me-2"></i>
            Guardar contenedor
        `;
    }
}

function codigoValido(codigo) {
    const expresionCodigo = /^[A-Z0-9-]{3,20}$/;
    return expresionCodigo.test(codigo);
}