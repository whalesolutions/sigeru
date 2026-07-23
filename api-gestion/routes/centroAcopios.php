<?php

require_once __DIR__ . "/../controllers/centroAcopioController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new CentroAcopioController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-centroAcopios/")
        || str_ends_with($ruta, "/api-centroAcopios/index.php")
        || str_ends_with($ruta, "/centroAcopios")
    )
) {
    http_response_code(200);
    echo json_encode($controller->listar());
    exit;
}

if ($metodo === "GET" && preg_match("#/centroAcopios/(\d+)$#", $ruta, $coincidencias)) {
    $id = (int) $coincidencias[1];
    $centroAcopio = $controller->buscar($id);

    if ($centroAcopio === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Centro de Acopio no encontrado"
        ]);

        exit;
    }

    http_response_code(200);
    echo json_encode($centroAcopio);
    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/centroAcopios$#", $ruta)
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
        // isset() comprueba que todos los campos estén presentes
        !isset(
            $datos["nombre"],
            $datos["telefono"],
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["correo"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["nombre"]) === ""
        || trim((string) $datos["telefono"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["correo"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, telefono, direccion, longitud, latitud, correo y capacidadMaxima son obligatorios"
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

    $centroAcopio = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $centroAcopio,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/centroAcopios/(\d+)$#", $ruta, $coincidencias)
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
        // isset() comprueba que todos los campos estén presentes
        !isset(
            $datos["nombre"],
            $datos["telefono"],
            $datos["direccion"],
            $datos["longitud"],
            $datos["latitud"],
            $datos["correo"],
            $datos["capacidadMaxima"]
        )
        || trim((string) $datos["nombre"]) === ""
        || trim((string) $datos["telefono"]) === ""
        || trim((string) $datos["direccion"]) === ""
        || trim((string) $datos["correo"]) === ""
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, telefono, direccion, longitud, latitud, correo y capacidadMaxima son obligatorios"
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

    $centroAcopio = $controller->actualizar($id, $datos);

    if ($centroAcopio === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Centro de Acopio no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $centroAcopio,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/centroAcopios/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminada = $controller->eliminar($id);
    if (!$eliminada) {
        http_response_code(404);

        echo json_encode([
            "error" => "Centro de Acopio no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "mensaje" => "Centro de Acopio eliminado correctamente"
    ]);

    exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);
