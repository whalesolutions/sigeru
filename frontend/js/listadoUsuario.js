const tablaUsuarios = document.getElementById("tablaUsuarios");
const totalUsuarios = document.getElementById("totalUsuarios");
const buscarUsuario = document.getElementById("buscarUsuario");
const mensajeUsuarios = document.getElementById("mensajeUsuarios");

let usuarios = [];

async function cargarUsuarios() {
    try {
        const respuesta = await fetch(
            "../api-usuarios/index.php/usuarios"
        );
        if (!respuesta.ok) {
            throw new Error("No se pudo obtener la lista de usuarios");
        }
        usuarios = await respuesta.json();
        mostrarUsuarios(usuarios);
    } catch (error) {
        console.error(error);
        tablaUsuarios.innerHTML = "";
        mensajeUsuarios.textContent =
            "No se pudo cargar la lista de usuarios.";
        mensajeUsuarios.classList.remove("d-none");
    }
}

function mostrarUsuarios(listaUsuarios) {
    tablaUsuarios.innerHTML = "";
    totalUsuarios.textContent = listaUsuarios.length;
    if (listaUsuarios.length === 0) {
        mensajeUsuarios.textContent =
            "No se encontraron usuarios.";
        mensajeUsuarios.classList.remove("d-none");
        return;
    }
    mensajeUsuarios.classList.add("d-none");

    listaUsuarios.forEach((usuario) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${usuario.documento}</td>
            <td>
                ${usuario.nombre} ${usuario.apellido}
            </td>
            <td>
                <span class="badge bg-primary">
                    ${usuario.rol}
                </span>
            </td>
            <td>
                ${crearEstado(usuario.estado)}
            </td>
            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-success"
                    onclick="verUsuario(${usuario.id})"
                >
                    <i class="bi bi-eye-fill me-1"></i>
                    Ver
                </button>
            </td>
        `;
        tablaUsuarios.appendChild(fila);
    });
}

function crearEstado(estado) {
    let claseEstado = "bg-secondary";
    if (estado === "Activo") {
        claseEstado = "bg-success";
    }
    if (estado === "Inactivo") {
        claseEstado = "bg-danger";
    }
    if (estado === "Pendiente") {
        claseEstado = "bg-warning text-dark";
    }
    if (estado === "Rechazado") {
        claseEstado = "bg-dark";
    }
    return `
        <span class="badge ${claseEstado}">
            ${estado}
        </span>
    `;
}

function verUsuario(id) {
    const usuario = usuarios.find(
        (usuarioActual) => usuarioActual.id === id
    );
    if (!usuario) {
        return;
    }
    document.getElementById("modalId").textContent =
        usuario.id;
    document.getElementById("modalDocumento").textContent =
        usuario.documento;
    document.getElementById("modalNombre").textContent =
        usuario.nombre;
    document.getElementById("modalApellido").textContent =
        usuario.apellido;
    document.getElementById("modalTelefono").textContent =
        usuario.telefono;
    document.getElementById("modalCorreo").textContent =
        usuario.correo;
    document.getElementById("modalRol").textContent =
        usuario.rol;
    const modalEstado = document.getElementById("modalEstado");

    modalEstado.textContent = usuario.estado;
    modalEstado.className = "badge";

    if (usuario.estado === "Activo") {
        modalEstado.classList.add("bg-success");
    } else if (usuario.estado === "Inactivo") {
        modalEstado.classList.add("bg-danger");
    } else if (usuario.estado === "Pendiente") {
        modalEstado.classList.add(
            "bg-warning",
            "text-dark"
        );
    } else {
        modalEstado.classList.add("bg-dark");
    }
    const modal = new bootstrap.Modal(
        document.getElementById("modalUsuario")
    );
    modal.show();
}

buscarUsuario.addEventListener("input", () => {
    const texto = buscarUsuario.value
        .trim()
        .toLowerCase();
    const usuariosFiltrados = usuarios.filter((usuario) => {
        const nombreCompleto =
            `${usuario.nombre} ${usuario.apellido}`.toLowerCase();
        return (
            nombreCompleto.includes(texto)
            || usuario.documento.includes(texto)
            || usuario.correo.toLowerCase().includes(texto)
        );
    });
    mostrarUsuarios(usuariosFiltrados);
});

cargarUsuarios();