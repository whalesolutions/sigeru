const formulario = document.getElementById("altaCamionForm");
const mensajeCamion = document.getElementById("mensajeCamion");
const btnGuardarCamion = document.getElementById("btnGuardarCamion");
const btnLimpiarCamion = document.getElementById("btnLimpiarCamion");
const inputMatricula = document.getElementById("matricula");
const inputMarca = document.getElementById("marca");
const inputModelo = document.getElementById("modelo");
const inputCapacidadCarga = document.getElementById("capacidadCarga");
const inputKilometraje = document.getElementById("kilometraje");
const selectEstado = document.getElementById("estado");

formulario.addEventListener("submit", async function (evento) {
    evento.preventDefault();

    ocultarMensaje();
    normalizarCampos();

    if (!formulario.checkValidity()) {
        formulario.classList.add("was-validated");
        return;
    }

    const matricula = inputMatricula.value.trim().toUpperCase();
    const marca = inputMarca.value.trim();
    const modelo = inputModelo.value.trim();
    const capacidadCarga = Number(inputCapacidadCarga.value);
    const kilometraje = Number(inputKilometraje.value);
    const estado = selectEstado.value;

    if (!matriculaValida(matricula)) {
        mostrarMensaje(
            "La matrícula debe contener entre 5 y 10 caracteres y solo puede incluir letras, números y guiones.",
            "danger"
        );

        inputMatricula.focus();
        return;
    }

    const camion = {
        matricula,
        marca,
        modelo,
        capacidadCarga,
        kilometraje,
        estado
    };

    /* Se hace el consumo de la API para crear el Camion*/
    try {
        bloquearFormulario(true);

        const respuesta = await fetch(
            "../api-recoleccion/index.php/camiones",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(camion)
            }
        );

        const resultado = await respuesta.json();

        if (!respuesta.ok || resultado.error) {
            mostrarMensaje(
                resultado.mensaje ||
                "No se pudo registrar el camión.",
                "danger"
            );

            return;
        }

        mostrarMensaje(
            resultado.mensaje ||
            "Camión registrado correctamente.",
            "success"
        );

        formulario.reset();
        formulario.classList.remove("was-validated");
        inputMatricula.focus();

        console.log("Camión creado:", resultado.camion);
    } catch (error) {
        console.error("Error al registrar el camión:", error);

        mostrarMensaje(
            "No se pudo conectar con la API de camiones.",
            "danger"
        );
    } finally {
        bloquearFormulario(false);
    }
});

btnLimpiarCamion.addEventListener("click", function () {
    formulario.classList.remove("was-validated");
    ocultarMensaje();
});

/* Funcion para poder eliminar los espacios al principio y al final si por error se insertan y pasar los datos a Mayusuculas */
function normalizarCampos() {
    inputMatricula.value = inputMatricula.value
        .trim()
        .toUpperCase();

    inputMarca.value = inputMarca.value.trim();
    inputModelo.value = inputModelo.value.trim();
}

/* Funcion para validar el formato de la matricula y el largo */
function matriculaValida(matricula) {
    const expresionMatricula = /^[A-Z0-9-]{5,10}$/;

    return expresionMatricula.test(matricula);
}

function mostrarMensaje(mensaje, tipo) {
    mensajeCamion.textContent = mensaje;
    mensajeCamion.className = `alert alert-${tipo} mt-4`;
}

function ocultarMensaje() {
    mensajeCamion.textContent = "";
    mensajeCamion.className = "alert d-none mt-4";
}

/* Funcion para bloquear los botones de html mientras se realiza el registro al hacer click en guardar*/
function bloquearFormulario(bloquear) {
    btnGuardarCamion.disabled = bloquear;

    if (bloquear) {
        btnGuardarCamion.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true">
            </span>
            Guardando...
        `;
    } else {
        btnGuardarCamion.innerHTML = `
            <i class="bi bi-truck me-2"></i>
            Guardar camión
        `;
    }
}