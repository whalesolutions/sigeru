<?php

require_once __DIR__ . "/../controllers/vertederoController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new VertederoController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/vertederos")
    )
) {
    http_response_code(200);

    echo json_encode(
        $controller->listar(),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "GET"
    && preg_match("#/vertederos/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];
    $vertedero = $controller->buscar($id);

    if ($vertedero === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Vertedero no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $vertedero,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/vertederos$#", $ruta)
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
            $datos["nombre"],
            $datos["telefono"],
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["correo"],
            $datos["estado"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["nombre"]) === ""
        || trim((string) $datos["telefono"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["correo"]) === ""
        || trim((string) $datos["estado"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, telefono, direccion, longitud, latitud, correo, estado y capacidadMaxima son obligatorios"
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

    $vertedero = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $vertedero,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/vertederos/(\d+)$#", $ruta, $coincidencias)
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
            $datos["nombre"],
            $datos["telefono"],
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["correo"],
            $datos["estado"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["nombre"]) === ""
        || trim((string) $datos["telefono"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["correo"]) === ""
        || trim((string) $datos["estado"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, telefono, direccion, longitud, latitud, correo, estado y capacidadMaxima son obligatorios"
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

    $vertedero = $controller->actualizar($id, $datos);

    if ($vertedero === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Vertedero no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $vertedero,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/vertederos/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminado = $controller->eliminar($id);

    if (!$eliminado) {
        http_response_code(404);

        echo json_encode([
            "error" => "Vertedero no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "mensaje" => "Vertedero eliminado correctamente"
    ]);

    exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);
