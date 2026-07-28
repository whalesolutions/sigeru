const tablaCamiones = document.getElementById("tablaCamiones");
const buscador = document.getElementById("buscador");
const mensajeCamiones = document.getElementById("mensajeCamiones");
const detalleMatricula = document.getElementById("detalleMatricula");
const detalleMarca = document.getElementById("detalleMarca");
const detalleModelo = document.getElementById("detalleModelo");
const detalleCapacidad = document.getElementById("detalleCapacidad");
const detalleKilometraje = document.getElementById("detalleKilometraje");
const detalleEstado = document.getElementById("detalleEstado");
const elementoModalCamion = document.getElementById("modalCamion");
const instanciaModalCamion = bootstrap.Modal.getOrCreateInstance(elementoModalCamion);

let camiones = [];

async function obtenerCamiones() {

    tablaCamiones.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                Cargando camiones...
            </td>
        </tr>
    `;

    try {

        const respuesta = await fetch(
            "../api-recoleccion/index.php/camiones"
        );

        const resultado = await respuesta.json();

        if (respuesta.ok) {

            mensajeCamiones.classList.add("d-none");
            mensajeCamiones.textContent = "";

            camiones = resultado.camiones;

            mostrarCamiones(camiones);

        } else {

            mostrarMensaje(resultado.mensaje);

        }

    } catch (error) {

        console.error(error);

        mostrarMensaje("Error al conectar con la API.");

    }

}

function obtenerBadgeEstado(estado) {

    if (estado === "Disponible") {
        return `
            <span class="badge text-bg-success">
                ${estado}
            </span>
        `;
    }

    if (estado === "En mantenimiento") {
        return `
            <span class="badge text-bg-warning">
                ${estado}
            </span>
        `;
    }

    return `
        <span class="badge text-bg-secondary">
            ${estado}
        </span>
    `;
}

function mostrarCamiones(listaCamiones) {

    tablaCamiones.innerHTML = "";

    if (listaCamiones.length === 0) {

        tablaCamiones.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    No se encontraron camiones.
                </td>
            </tr>
        `;

        return;
    }

    listaCamiones.forEach(function (camion) {

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${camion.matricula}</td>
            <td>${camion.marca}</td>
             <td>${camion.modelo}</td>
            <td>${obtenerBadgeEstado(camion.estado)}</td>

            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-primary btn-sm btn-ver-camion">
                    Ver
                </button>
            </td>
        `;

        const botonVer =
            fila.querySelector(".btn-ver-camion");

        botonVer.addEventListener("click", function () {
            verCamion(camion.id);
        });

        tablaCamiones.appendChild(fila);
    });
}


buscador.addEventListener("input", function () {

    const texto = buscador.value.toLowerCase();

    const filtrados = camiones.filter(function (camion) {

        return (

            camion.matricula.toLowerCase().includes(texto) ||

            camion.marca.toLowerCase().includes(texto) ||

            camion.modelo.toLowerCase().includes(texto) ||

            camion.estado.toLowerCase().includes(texto)

        );

    });

    mostrarCamiones(filtrados);

});


function verCamion(id) {

    const camion = camiones.find(function (camionActual) {
        return Number(camionActual.id) === Number(id);
    });

    if (!camion) {
        alert("No se encontró el camión.");
        return;
    }

    detalleMatricula.textContent = camion.matricula;
    detalleMarca.textContent = camion.marca;
    detalleModelo.textContent = camion.modelo;
    detalleCapacidad.textContent = camion.capacidadCarga + " kg";
    detalleKilometraje.textContent = camion.kilometraje + " km";
    detalleEstado.textContent = camion.estado;

    instanciaModalCamion.show();
}


function mostrarMensaje(mensaje) {

    mensajeCamiones.textContent = mensaje;

    mensajeCamiones.classList.remove("d-none");

}

obtenerCamiones();

