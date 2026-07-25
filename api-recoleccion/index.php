<?php

header("Content-Type: application/json; charset=UTF-8");

/*
 * Permite solicitudes desde otros orígenes.
 * Es útil durante el desarrollo cuando la landing page
 * y la API se ejecutan en ubicaciones diferentes.
 */
header("Access-Control-Allow-Origin: *");
header(
    "Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS"
);
header(
    "Access-Control-Allow-Headers: Content-Type, Authorization"
);

/*
 * Responde las solicitudes preliminares CORS.
 */
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$segmentos = array_values(
    array_filter(
        explode("/", trim($uri, "/"))
    )
);

/*
 * Recursos disponibles dentro de esta API.
 */
$recursosPermitidos = [
    "camiones",
    "rutas",
    "recolecciones"
];

$recursoEncontrado = null;

/*
 * Busca dentro de la URI cuál de los recursos fue solicitado.
 *
 * Esto permite que la API funcione aunque esté instalada
 * dentro de una carpeta, por ejemplo:
 *
 * /proyecto/api-recoleccion/camiones
 */
foreach ($segmentos as $segmento) {
    $segmentoNormalizado = strtolower(trim($segmento));

    if (in_array(
        $segmentoNormalizado,
        $recursosPermitidos,
        true
    )) {
        $recursoEncontrado = $segmentoNormalizado;
        break;
    }
}

/*
 * Si no se indicó ningún recurso, devuelve información básica
 * de la API.
 */
if ($recursoEncontrado === null) {
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        responder([
            "error" => true,
            "codigo" => 404,
            "mensaje" => "Ruta no encontrada."
        ]);
    }

    responder([
        "error" => false,
        "codigo" => 200,
        "mensaje" => "API de Recolección funcionando correctamente.",
        "recursos" => [
            [
                "nombre" => "Camiones",
                "endpoint" => "/camiones"
            ],
            [
                "nombre" => "Rutas",
                "endpoint" => "/rutas"
            ],
            [
                "nombre" => "Recolecciones",
                "endpoint" => "/recolecciones"
            ]
        ]
    ]);
}

/*
 * Carga el archivo de rutas correspondiente.
 */
switch ($recursoEncontrado) {
    case "camiones":
        require __DIR__ . "/routes/camiones.php";
        break;

    case "rutas":
        require __DIR__ . "/routes/rutas.php";
        break;

    case "recolecciones":
        require __DIR__ . "/routes/recolecciones.php";
        break;

    default:
        responder([
            "error" => true,
            "codigo" => 404,
            "mensaje" => "Ruta no encontrada."
        ]);
}

/**
 * Envía una respuesta JSON y finaliza la ejecución.
 */
function responder(array $resultado): void
{
    $codigo = $resultado["codigo"] ?? 500;

    http_response_code($codigo);

    echo json_encode(
        $resultado,
        JSON_UNESCAPED_UNICODE |
            JSON_PRETTY_PRINT
    );

    exit;
}
