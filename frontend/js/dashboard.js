const sidebar = document.getElementById("sidebar");
const btnMenu = document.getElementById("btnMenu");
const sidebarOverlay = document.getElementById("sidebarOverlay");
const btnCerrarSesion = document.getElementById("btnCerrarSesion");
const nombreUsuario = document.getElementById("nombreUsuario");
const rolUsuario = document.getElementById("rolUsuario");

function abrirMenu() {
    sidebar.classList.add("mostrar");
    sidebarOverlay.classList.add("mostrar");
}

function cerrarMenu() {
    sidebar.classList.remove("mostrar");
    sidebarOverlay.classList.remove("mostrar");
}

if (btnMenu) {
    btnMenu.addEventListener("click", abrirMenu);
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", cerrarMenu);
}

if (btnCerrarSesion) {

    btnCerrarSesion.addEventListener("click", (evento) => {
        evento.preventDefault();
        
        localStorage.removeItem("usuario");
        localStorage.removeItem("token");
        window.location.href = "login.html";
    });
}

/* Pra el dashboard muestra el usuario logeado */
const usuarioGuardado = localStorage.getItem("usuario");

if (!usuarioGuardado) {
    window.location.href = "login.html";
} else {
    const usuario = JSON.parse(usuarioGuardado);

    nombreUsuario.textContent = usuario.nombre;
    rolUsuario.textContent = usuario.rol;
}