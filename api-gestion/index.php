<?php

header("Content-Type: application/json; charset=UTF-8");

$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (str_contains($ruta, "/centroAcopios")) {
    require_once __DIR__ . "/routes/centroAcopios.php";
    exit;
}

if (str_contains($ruta, "/contenedores")) {
    require_once __DIR__ . "/routes/contenedores.php";
    exit;
}

if (str_contains($ruta, "/vertederos")) {
    require_once __DIR__ . "/routes/vertederos.php";
    exit;
}

if (str_contains($ruta, "/repuestos")) {
    require_once __DIR__ . "/routes/repuestos.php";
    exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Recurso no encontrado"
]);
