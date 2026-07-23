<?php

require_once __DIR__ . "/../controllers/usuarioController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new UsuarioController();

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-usuarios/")
        || str_ends_with($ruta, "/api-usuarios/index.php")
        || str_ends_with($ruta, "/usuarios")
    )
) {
    http_response_code(200);
    echo json_encode($controller->listar());
    exit;
}

if ($metodo === "GET" && preg_match("#/usuarios/(\d+)$#", $ruta, $coincidencias)) {
    $id = (int) $coincidencias[1];
    $usuario = $controller->buscar($id);

    if ($usuario === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Usuario no encontrado"
        ]);

        exit;
    }

    http_response_code(200);
    echo json_encode($usuario);
    exit;
}

if (
    $metodo === "POST"
    && preg_match("#/usuarios$#", $ruta)
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
           empty($datos["nombre"])
        || empty($datos["apellido"])
        || empty($datos["documento"])
        || empty($datos["correo"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, apellido, documento y correo son obligatorios"
        ]);

        exit;
    }

    $usuario = $controller->crear($datos);

    http_response_code(201);

    echo json_encode(
        $usuario,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "PUT"
    && preg_match("#/usuarios/(\d+)$#", $ruta, $coincidencias)
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
           empty($datos["nombre"])
        || empty($datos["apellido"])
        || empty($datos["documento"])
        || empty($datos["correo"])
        || empty($datos["estado"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Los campos nombre, apellido, documento, correo y estado son obligatorios"
        ]);

        exit;
    }

    $usuario = $controller->actualizar($id, $datos);

    if ($usuario === null) {
        http_response_code(404);

        echo json_encode([
            "error" => "Usuario no encontrado"
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode(
        $usuario,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

if (
    $metodo === "DELETE"
    && preg_match("#/usuarios/(\d+)$#", $ruta, $coincidencias)
) {
    $id = (int) $coincidencias[1];

    $eliminada = $controller->eliminar($id);
    if (!$eliminada) {
    http_response_code(404);

    echo json_encode([
        "error" => "Usuario no encontrado"
    ]);

    exit;
    }
 
    http_response_code(200);

echo json_encode([
    "mensaje" => "Usuario eliminado correctamente"
]);

exit;
}

http_response_code(404);

echo json_encode([
    "error" => "Ruta no encontrada"
]);