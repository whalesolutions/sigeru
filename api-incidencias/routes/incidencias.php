<?php

require_once __DIR__ . "/../controllers/incidenciaController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new IncidenciaController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-incidencias/")
        || str_ends_with($ruta, "/api-incidencias/index.php")
        || str_ends_with($ruta, "/incidencias")
    )
) {
    http_response_code(200);
    echo json_encode($controller->listar());
    exit;
}

if ($metodo === "GET" && preg_match("#/incidencias/(\d+)$#", $ruta, $coincidencias)) {
    $id = (int) $coincidencias[1];
    $incidencia = $controller->buscar($id);

    if ($incidencia === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Incidencia no encontrada"
        ]);

        exit;
    }

    http_response_code(200);
    echo json_encode($incidencia);
    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/incidencias$#", $ruta)
) {
    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    if (!is_array($datos)) {
        http_response_code(400);

        echo json_encode([
            "error" => "El cuerpo de la petición debe contener JSON válido"
        ]);

        exit;
    }

    if (
        empty($datos["titulo"])
        || empty($datos["descripcion"])
        || empty($datos["tipo"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos titulo, descripcion y tipo son obligatorios"
        ]);

        exit;
    }

    $incidencia = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $incidencia,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/incidencias/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    if (!is_array($datos)) {
        http_response_code(400);

        echo json_encode([
            "error" => "El cuerpo de la petición debe contener JSON válido"
        ]);

        exit;
    }

    if (
        empty($datos["titulo"])
        || empty($datos["descripcion"])
        || empty($datos["tipo"])
        || empty($datos["estado"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos titulo, descripcion, tipo y estado son obligatorios"
        ]);

        exit;
    }

    $incidencia = $controller->actualizar($id, $datos);

    if ($incidencia === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Incidencia no encontrada"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $incidencia,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/incidencias/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminada = $controller->eliminar($id);
    if (!$eliminada) {
    http_response_code(404);

    echo json_encode([
        "error" => "Incidencia no encontrada"
    ]);

    exit;
    }
 
    http_response_code(200);

echo json_encode([
    "mensaje" => "Incidencia eliminada correctamente"
]);

exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);