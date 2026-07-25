console.log("SiGeRU loaded V2");

const usuario = JSON.parse(localStorage.getItem("usuario"));

const btnAcceder = document.getElementById("btnAcceder");
const btnUsuario = document.getElementById("btnUsuario");
const nombreUsuario = document.getElementById("nombreUsuario");

if (usuario) {
    btnAcceder.style.display = "none";
    btnUsuario.style.display = "inline-block";
    nombreUsuario.textContent = usuario.nombre;

    btnUsuario.addEventListener("click", function () {
        localStorage.removeItem("usuario");
        window.location.href = "index.html";
    });
}

var map = L.map('map').setView([-32.5228, -55.7658], 7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

L.marker([-34.9011, -56.1645]).addTo(map)
    .bindPopup("Montevideo");

L.marker([-34.905, -54.958]).addTo(map)
    .bindPopup("Maldonado");

L.marker([-31.383, -57.968]).addTo(map)
    .bindPopup("Salto");


const volquetas = [

    {
        nombre: "V001 Ciudad Vieja",
        lat: -34.9067,
        lng: -56.2065
    },

    {
        nombre: "V002 Centro",
        lat: -34.9038,
        lng: -56.1908
    },

    {
        nombre: "V003 Cordón",
        lat: -34.9025,
        lng: -56.1818
    },

    {
        nombre: "V004 Palermo",
        lat: -34.9124,
        lng: -56.1845
    },

    {
        nombre: "V005 Parque Rodó",
        lat: -34.9168,
        lng: -56.1722
    },

    {
        nombre: "V006 Tres Cruces",
        lat: -34.8940,
        lng: -56.1699
    },

    {
        nombre: "V007 La Blanqueada",
        lat: -34.8884,
        lng: -56.1617
    },

    {
        nombre: "V008 Unión",
        lat: -34.8785,
        lng: -56.1438
    },

    {
        nombre: "V009 Buceo",
        lat: -34.9013,
        lng: -56.1292
    },

    {
        nombre: "V010 Pocitos",
        lat: -34.9126,
        lng: -56.1491
    },

    {
        nombre: "V011 Punta Carretas",
        lat: -34.9232,
        lng: -56.1602
    },

    {
        nombre: "V012 Malvín",
        lat: -34.8931,
        lng: -56.1038
    },

    {
        nombre: "V013 Carrasco Norte",
        lat: -34.8855,
        lng: -56.0770
    },

    {
        nombre: "V014 Carrasco",
        lat: -34.8887,
        lng: -56.0605
    },

    {
        nombre: "V015 Prado",
        lat: -34.8568,
        lng: -56.2014
    },

    {
        nombre: "V016 Paso Molino",
        lat: -34.8622,
        lng: -56.2241
    },

    {
        nombre: "V017 Belvedere",
        lat: -34.8643,
        lng: -56.2298
    },

    {
        nombre: "V018 Cerro",
        lat: -34.8884,
        lng: -56.2555
    },

    {
        nombre: "V019 Colón",
        lat: -34.8094,
        lng: -56.2195
    },

    {
        nombre: "V020 Lezica",
        lat: -34.7900,
        lng: -56.2272

    },

    // Muestras

    {
        nombre: "Maldonado Centro",
        lat: -34.905,
        lng: -54.958
    },

    {
        nombre: "Punta del Este",
        lat: -34.962,
        lng: -54.950
    },

    {
        nombre: "Las Piedras",
        lat: -34.730,
        lng: -56.219
    },

    {
        nombre: "Pando",
        lat: -34.717,
        lng: -55.958
    },

    {
        nombre: "Salto Centro",
        lat: -31.383,
        lng: -57.968
    },

    {
        nombre: "Paysandú",
        lat: -32.321,
        lng: -58.075
    }

];


volquetas.forEach(v => {
    L.marker([v.lat, v.lng])
        .addTo(map)
        .bindPopup(`
            <strong>${v.nombre}</strong><br>
            ${v.ciudad}<br>
            ${v.departamento}
        `);
});
