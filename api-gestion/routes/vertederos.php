<?php

require_once __DIR__ . "/../controllers/vertederoController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new VertederoController();

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
 * Devuelve una respuesta JSON utilizando el código HTTP
 * generado por el controlador.
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
| GET - Listar todos los vertederos
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-vertederos/")
        || str_ends_with($ruta, "/api-vertederos/index.php")
        || str_ends_with($ruta, "/vertederos")
        || str_ends_with($ruta, "/vertederos/")
    )
) {
    responderJson($controller->listar());
}

/*
|--------------------------------------------------------------------------
| GET - Buscar un vertedero por ID
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && preg_match(
        "#/vertederos/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    responderJson($controller->buscar($id));
}

/*
|--------------------------------------------------------------------------
| POST - Crear un vertedero
|--------------------------------------------------------------------------
*/
if (
    $metodo === "POST"
    && preg_match("#/vertederos/?$#", $ruta)
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
| PUT - Actualizar un vertedero
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PUT"
    && preg_match(
        "#/vertederos/(\d+)/?$#",
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
| DELETE - Eliminar un vertedero
|--------------------------------------------------------------------------
*/
if (
    $metodo === "DELETE"
    && preg_match(
        "#/vertederos/(\d+)/?$#",
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