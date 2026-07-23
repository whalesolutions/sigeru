<?php

require_once __DIR__ . "/../controllers/contenedorController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new ContenedorController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-contenedores/")
        || str_ends_with($ruta, "/api-contenedores/index.php")
        || str_ends_with($ruta, "/contenedores")
    )
) {
    http_response_code(200);
    echo json_encode($controller->listar());
    exit;
}

if (
    $metodo === "GET"
    && preg_match("#/contenedores/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];
    $contenedor = $controller->buscar($id);

    if ($contenedor === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Contenedor no encontrado"
        ]);

        exit;
    }

    http_response_code(200);
    echo json_encode($contenedor);
    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/contenedores$#", $ruta)
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
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["estado"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["codigo"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["estado"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos codigo, direccion, longitud, latitud, estado y capacidadMaxima son obligatorios"
        ]);

        exit;
    }

    if (
        !is_numeric($datos["longitud"])
        || !is_numeric($datos["latitud"])
        || !is_numeric($datos["capacidadMaxima"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Longitud, latitud y capacidadMaxima deben ser valores numéricos"
        ]);

        exit;
    }

    $contenedor = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $contenedor,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/contenedores/(\d+)$#", $ruta, $coincidencias)
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
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["estado"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["codigo"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["estado"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos codigo, direccion, longitud, latitud, estado y capacidadMaxima son obligatorios"
        ]);

        exit;
    }

    if (
        !is_numeric($datos["longitud"])
        || !is_numeric($datos["latitud"])
        || !is_numeric($datos["capacidadMaxima"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Longitud, latitud y capacidadMaxima deben ser valores numéricos"
        ]);

        exit;
    }

    $contenedor = $controller->actualizar($id, $datos);

    if ($contenedor === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Contenedor no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $contenedor,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/contenedores/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminado = $controller->eliminar($id);

    if (!$eliminado) {
        http_response_code(404);

        echo json_encode([
            "error" => "Contenedor no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "mensaje" => "Contenedor eliminado correctamente"
    ]);

    exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);
