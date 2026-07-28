
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

var map = L.map('map').setView([-34.9011, -56.1645], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

const iconoContenedor = L.icon({
    iconUrl: "assets/images/dumpster.png",
    iconSize: [38, 38],
    iconAnchor: [19, 38],
    popupAnchor: [0, -35]
});
const iconoCiudad = L.icon({
    iconUrl: "assets/images/city.png",
    iconSize: [40, 40],
    iconAnchor: [20, 40],
    popupAnchor: [0, -35]
});

L.marker(
    [-34.9011, -56.1645],
    {
        icon: iconoCiudad
    }
)
.addTo(map)
.bindPopup("<strong>Montevideo</strong>");;

L.marker(
    [-34.905, -54.958],
    {
        icon: iconoCiudad
    }
)
.addTo(map)
.bindPopup("<strong>Maldonado</strong>");

L.marker(
    [-31.383, -57.968],
    {
        icon: iconoCiudad
    }
)
.addTo(map)
.bindPopup("<strong>Salto</strong>");

const contenedores = [

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

    }
    ,
    {
        nombre: "C021 Parque Batlle",
        lat: -34.8928,
        lng: -56.1512
    },

    {
        nombre: "C022 Parque Batlle",
        lat: -34.8951,
        lng: -56.1568
    },

    {
        nombre: "C023 Villa Dolores",
        lat: -34.9036,
        lng: -56.1359
    },
    {
        nombre: "C024 Pocitos",
        lat: -34.9095,
        lng: -56.1448
    },
    {
        nombre: "C026 Punta Carretas",
        lat: -34.9201,
        lng: -56.1578
    },
    {
        nombre: "C027 Parque Rodó",
        lat: -34.9164,
        lng: -56.1687
    },
    {
        nombre: "C028 Centro",
        lat: -34.9046,
        lng: -56.1872
    },
    {
        nombre: "C029 Centro",
        lat: -34.9002,
        lng: -56.1854
    },
    {
        nombre: "C030 Aguada",
        lat: -34.8960,
        lng: -56.1983
    },
    {
        nombre: "C031 Bella Vista",
        lat: -34.8885,
        lng: -56.1952
    },
    {
        nombre: "C032 Prado",
        lat: -34.8561,
        lng: -56.1988
    },
    {
        nombre: "C033 Sayago",
        lat: -34.8457,
        lng: -56.2151
    },
    {
        nombre: "C034 Colón",
        lat: -34.8112,
        lng: -56.2146
    },
    {
        nombre: "C035 Peñarol",
        lat: -34.8569,
        lng: -56.2474
    },
    {
        nombre: "C036 Cerro",
        lat: -34.8868,
        lng: -56.2574
    },
    {
        nombre: "C037 Casabó",
        lat: -34.8945,
        lng: -56.2765
    },
    {
        nombre: "C038 Malvín",
        lat: -34.8914,
        lng: -56.1016
    },
    {
        nombre: "C039 Carrasco",
        lat: -34.8899,
        lng: -56.0583
    },
    {
        nombre: "C040 Buceo",
        lat: -34.8990,
        lng: -56.1253
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

contenedores.forEach(function (contenedor) {
    L.marker(
        [contenedor.lat, contenedor.lng],
        {
            icon: iconoContenedor
        }
    )
        .addTo(map)
        .bindPopup(`
            <strong>${contenedor.nombre}</strong><br>
            Contenedor de residuos
        `);
});