<?php

require_once __DIR__ . "/../controllers/usuarioController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new UsuarioController();

// Funciones auxiliares


// Lee y convierte el cuerpo JSON de la petición.
function obtenerDatosJson(): ?array
{
    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    return is_array($datos) ? $datos : null;
}

// Envía una respuesta JSON con su código HTTP.
function responderJson(int $codigo, array $contenido): void
{
    http_response_code($codigo);

    echo json_encode(
        $contenido,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

// GET - Listar todos los usuarios

// GET /api-usuarios/
// GET /api-usuarios/index.php
// GET /api-usuarios/index.php/usuarios

if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-usuarios/")
        || str_ends_with($ruta, "/api-usuarios")
        || str_ends_with($ruta, "/api-usuarios/index.php")
        || str_ends_with($ruta, "/api-usuarios/index.php/usuarios")
        || str_ends_with($ruta, "/usuarios")
    )
) {
    responderJson(200, $controller->listar());
}

// GET - Buscar usuario por ID

// GET /api-usuarios/index.php/usuarios/1


if (
    $metodo === "GET"
    && preg_match(
        "#/usuarios/(\d+)$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $usuario = $controller->buscar($id);

    if ($usuario === null) {
        responderJson(404, [
            "error" => true,
            "mensaje" => "Usuario no encontrado"
        ]);
    }

    responderJson(200, $usuario);
}

// POST - Registrar un usuario

// POST /api-usuarios/index.php/usuarios

// El usuario se crea automáticamente con estado Pendiente.


if (
    $metodo === "POST"
    && preg_match("#/usuarios$#", $ruta)
) {
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->crear($datos);

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}

// POST - Iniciar sesión

// POST /api-usuarios/index.php/usuarios/login


if (
    $metodo === "POST"
    && preg_match("#/usuarios/login$#", $ruta)
) {
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->login($datos);

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}

// PUT - Actualizar perfil

// PUT /api-usuarios/index.php/usuarios/1/perfil


if (
    $metodo === "PUT"
    && preg_match(
        "#/usuarios/(\d+)/perfil$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->actualizarPerfil(
        $id,
        $datos
    );

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}

// PATCH - Cambiar estado

// PATCH /api-usuarios/index.php/usuarios/1/estado


if (
    $metodo === "PATCH"
    && preg_match(
        "#/usuarios/(\d+)/estado$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->cambiarEstado(
        $id,
        $datos
    );

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}

// PATCH - Cambiar rol

// PATCH /api-usuarios/index.php/usuarios/1/rol

if (
    $metodo === "PATCH"
    && preg_match(
        "#/usuarios/(\d+)/rol$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->cambiarRol(
        $id,
        $datos
    );

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}


// PATCH - Cambiar contraseña

// PATCH /api-usuarios/index.php/usuarios/1/contrasena

if (
    $metodo === "PATCH"
    && preg_match(
        "#/usuarios/(\d+)/contrasena$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson(400, [
            "error" => true,
            "mensaje" => "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    $resultado = $controller->cambiarContrasena(
        $id,
        $datos
    );

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}

// DELETE - Eliminar usuario

// DELETE /api-usuarios/index.php/usuarios/1


if (
    $metodo === "DELETE"
    && preg_match(
        "#/usuarios/(\d+)$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    $resultado = $controller->eliminar($id);

    responderJson(
        $resultado["codigo"],
        $resultado
    );
}


// Ruta inexistente


responderJson(404, [
    "error" => true,
    "mensaje" => "Ruta no encontrada"
]);