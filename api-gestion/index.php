<?php

header("Content-Type: application/json; charset=UTF-8");

$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

/*
|--------------------------------------------------------------------------
| API Gestión
|--------------------------------------------------------------------------
*/

$routes = [
    "/centroAcopios" => "/routes/centroAcopios.php",
    "/contenedores"  => "/routes/contenedores.php",
    "/repuestos"     => "/routes/repuestos.php",
    "/vertederos"    => "/routes/vertederos.php"
];

foreach ($routes as $endpoint => $archivo) {

    if (str_contains($ruta, $endpoint)) {
        require_once __DIR__ . $archivo;
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Ruta inexistente
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode(
    [
        "error" => true,
        "codigo" => 404,
        "mensaje" => "Recurso no encontrado"
    ],
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);