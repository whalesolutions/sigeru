const sidebar = document.getElementById("sidebar");
const btnMenu = document.getElementById("btnMenu");
const sidebarOverlay = document.getElementById("sidebarOverlay");
const btnCerrarSesion = document.getElementById("btnCerrarSesion");

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
    btnCerrarSesion.addEventListener("click", () => {
        localStorage.removeItem("usuario");
        localStorage.removeItem("token");
    });
}