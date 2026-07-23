<?php

require_once __DIR__ . "/../controllers/repuestoController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new RepuestoController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/repuestos")
    )
) {
    http_response_code(200);
    echo json_encode($controller->listar());
    exit;
}

if (
    $metodo === "GET"
    && preg_match("#/repuestos/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];
    $repuesto = $controller->buscar($id);

    if ($repuesto === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Repuesto no encontrado"
        ]);

        exit;
    }

    http_response_code(200);
    echo json_encode($repuesto);
    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/repuestos$#", $ruta)
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
        !isset(
            $datos["codigo"],
            $datos["modelo"],
            $datos["stock"],
            $datos["precio"],
            $datos["descripcion"]
        )
        || trim((string) $datos["codigo"]) === ""
        || trim((string) $datos["modelo"]) === ""
        || trim((string) $datos["descripcion"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos codigo, modelo, stock, precio y descripcion son obligatorios"
        ]);

        exit;
    }

    if (
        !is_numeric($datos["stock"])
        || !is_numeric($datos["precio"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Stock y precio deben ser valores numéricos"
        ]);

        exit;
    }

    if ($datos["precio"] < 0) {
        http_response_code(400);

        echo json_encode([
            "error" => "El precio no puede ser negativo"
        ]);

        exit;
    }

    $repuesto = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $repuesto,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/repuestos/(\d+)$#", $ruta, $coincidencias)
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
        !isset(
            $datos["codigo"],
            $datos["modelo"],
            $datos["stock"],
            $datos["precio"],
            $datos["descripcion"]
        )
        || trim((string) $datos["codigo"]) === ""
        || trim((string) $datos["modelo"]) === ""
        || trim((string) $datos["descripcion"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos codigo, modelo, stock, precio y descripcion son obligatorios"
        ]);

        exit;
    }

    if (
        !is_numeric($datos["stock"])
        || !is_numeric($datos["precio"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Stock y precio deben ser valores numéricos"
        ]);

        exit;
    }

    if ($datos["precio"] < 0) {
        http_response_code(400);

        echo json_encode([
            "error" => "El precio no puede ser negativo"
        ]);

        exit;
    }

    $repuesto = $controller->actualizar($id, $datos);

    if ($repuesto === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Repuesto no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $repuesto,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/repuestos/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminado = $controller->eliminar($id);

    if (!$eliminado) {
        http_response_code(404);

        echo json_encode([
            "error" => "Repuesto no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "mensaje" => "Repuesto eliminado correctamente"
    ]);

    exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);
