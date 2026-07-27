const tablaContenedores = document.getElementById("tablaContenedores");
const buscador = document.getElementById("buscadorContenedores");
const mensajeContenedores = document.getElementById("mensajeContenedores");
const detalleCodigo = document.getElementById("detalleCodigo");
const detalleDireccion = document.getElementById("detalleDireccion");
const detalleCapacidadContenedor = document.getElementById("detalleCapacidadContenedor");
const detalleFechaInstalacion = document.getElementById("detalleFechaInstalacion");
const detalleLatitud = document.getElementById("detalleLatitud");
const detalleLongitud = document.getElementById("detalleLongitud");
const detalleEstadoContenedor = document.getElementById("detalleEstadoContenedor");
const elementoModalContenedor = document.getElementById("modalContenedor");
const instanciaModalContenedor = bootstrap.Modal.getOrCreateInstance(elementoModalContenedor);

let contenedores = [];


/*Obtiene los contenedores desde la API.*/
async function obtenerContenedores() {
    tablaContenedores.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                Cargando contenedores...
            </td>
        </tr>
    `;

    try {
        const respuesta = await fetch(
            "../api-gestion/index.php/contenedores"
        );

        const resultado = await respuesta.json();
        if (respuesta.ok) {
            mensajeContenedores.classList.add("d-none");
            mensajeContenedores.textContent = "";
            contenedores = resultado.contenedores;
            mostrarContenedores(contenedores);
        } else {
            mostrarMensaje(resultado.mensaje);
        }

    } catch (error) {
        console.error(error);
        mostrarMensaje("Error al conectar con la API.");
    }
}


/*Devuelve el badge correspondiente al estado.*/
function obtenerBadgeEstado(estado) {

    if (estado === "Activo") {
        return `
            <span class="badge text-bg-success">
                ${estado}
            </span>
        `;
    }
    if (estado === "Lleno") {
        return `
            <span class="badge text-bg-danger">
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


/*Muestra los contenedores en la tabla. */
function mostrarContenedores(listaContenedores) {
    tablaContenedores.innerHTML = "";
    if (listaContenedores.length === 0) {
        tablaContenedores.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    No se encontraron contenedores.
                </td>
            </tr>
        `;
        return;
    }

    listaContenedores.forEach(function (contenedor) {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${contenedor.codigo}</td>
            <td>${contenedor.direccion}</td>
            <td>
                ${contenedor.capacidadActual} /
                ${contenedor.capacidadMaxima} L
            </td>
            <td>
                ${obtenerBadgeEstado(contenedor.estado)}
            </td>
            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-primary btn-sm btn-ver-contenedor">
                    Ver
                </button>
            </td>
        `;
        const botonVer =
            fila.querySelector(".btn-ver-contenedor");
        botonVer.addEventListener("click", function () {
            verContenedor(contenedor.id);
        });
        tablaContenedores.appendChild(fila);
    });
}

/*Filtra los contenedores según el texto ingresado.*/
buscador.addEventListener("input", function () {
    const texto = buscador.value
        .trim()
        .toLowerCase();
    const filtrados = contenedores.filter(
        function (contenedor) {
            return (
                contenedor.codigo
                    .toLowerCase()
                    .includes(texto) ||
                contenedor.direccion
                    .toLowerCase()
                    .includes(texto) ||
                contenedor.estado
                    .toLowerCase()
                    .includes(texto)
            );
        }
    );
    mostrarContenedores(filtrados);
});

/*Muestra la información completa en el modal.*/
function verContenedor(id) {

    const contenedor = contenedores.find(
        function (contenedorActual) {
            return Number(contenedorActual.id) === Number(id);
        }
    );

    if (!contenedor) {
        alert("No se encontró el contenedor.");
        return;
    }
    detalleCodigo.textContent = contenedor.codigo;
    detalleDireccion.textContent = contenedor.direccion;
    detalleCapacidadContenedor.textContent = contenedor.capacidadActual + " / " + contenedor.capacidadMaxima + " litros";
    detalleLatitud.textContent = contenedor.latitud;
    detalleLongitud.textContent = contenedor.longitud;
    detalleEstadoContenedor.textContent = contenedor.estado;
    if (detalleFechaInstalacion) {
        detalleFechaInstalacion.textContent =
            contenedor.fechaInstalacion ?? "No registrada";
    }
    instanciaModalContenedor.show();

}
/*Muestra mensajes de error o información. */
function mostrarMensaje(mensaje) {
    mensajeContenedores.textContent = mensaje;
    mensajeContenedores.classList.remove("d-none");
}
obtenerContenedores();