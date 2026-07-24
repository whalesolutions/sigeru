<?php

require_once __DIR__ . "/../controllers/contenedorController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new ContenedorController();

/**
 * Obtiene y decodifica el cuerpo JSON de la petición.
 */
function obtenerDatosJson(): ?array
{
    $contenido = file_get_contents("php://input");

    if ($contenido === false || trim($contenido) === "") {
        return null;
    }

    $datos = json_decode($contenido, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
        return null;
    }

    return $datos;
}

/**
 * Envía una respuesta JSON utilizando el código HTTP
 * devuelto por el controlador.
 */
function responderJson(array $resultado): void
{
    $codigo = $resultado["codigo"] ?? 200;

    http_response_code($codigo);

    echo json_encode(
        $resultado,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| GET - Listar todos los contenedores
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-contenedores/")
        || str_ends_with($ruta, "/api-contenedores/index.php")
        || str_ends_with($ruta, "/contenedores")
        || str_ends_with($ruta, "/contenedores/")
    )
) {
    responderJson($controller->listar());
}

/*
|--------------------------------------------------------------------------
| GET - Buscar un contenedor por ID
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && preg_match(
        "#/contenedores/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    responderJson($controller->buscar($id));
}

/*
|--------------------------------------------------------------------------
| POST - Crear un contenedor
|--------------------------------------------------------------------------
*/
if (
    $metodo === "POST"
    && preg_match("#/contenedores/?$#", $ruta)
) {
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson([
            "error" => true,
            "codigo" => 400,
            "mensaje" =>
                "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    responderJson($controller->crear($datos));
}

/*
|--------------------------------------------------------------------------
| PUT - Actualizar un contenedor
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PUT"
    && preg_match(
        "#/contenedores/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];
    $datos = obtenerDatosJson();

    if ($datos === null) {
        responderJson([
            "error" => true,
            "codigo" => 400,
            "mensaje" =>
                "El cuerpo de la petición debe contener JSON válido"
        ]);
    }

    responderJson($controller->actualizar($id, $datos));
}

/*
|--------------------------------------------------------------------------
| DELETE - Eliminar un contenedor
|--------------------------------------------------------------------------
*/
if (
    $metodo === "DELETE"
    && preg_match(
        "#/contenedores/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    responderJson($controller->eliminar($id));
}

/*
|--------------------------------------------------------------------------
| Ruta no encontrada
|--------------------------------------------------------------------------
*/
responderJson([
    "error" => true,
    "codigo" => 404,
    "mensaje" => "Ruta no encontrada"
]);