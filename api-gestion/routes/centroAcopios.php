<?php

require_once __DIR__ . "/../controllers/centroAcopioController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new CentroAcopioController();

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
 * proporcionado por el controlador.
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
| GET - Listar todos los centros de acopio
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-centroAcopios/")
        || str_ends_with($ruta, "/api-centroAcopios/index.php")
        || str_ends_with($ruta, "/centroAcopios")
        || str_ends_with($ruta, "/centroAcopios/")
    )
) {
    responderJson($controller->listar());
}

/*
|--------------------------------------------------------------------------
| GET - Buscar un centro de acopio por ID
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && preg_match(
        "#/centroAcopios/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    responderJson($controller->buscar($id));
}

/*
|--------------------------------------------------------------------------
| POST - Crear un centro de acopio
|--------------------------------------------------------------------------
*/
if (
    $metodo === "POST"
    && preg_match("#/centroAcopios/?$#", $ruta)
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
| PUT - Actualizar un centro de acopio
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PUT"
    && preg_match(
        "#/centroAcopios/(\d+)/?$#",
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
| DELETE - Eliminar un centro de acopio
|--------------------------------------------------------------------------
*/
if (
    $metodo === "DELETE"
    && preg_match(
        "#/centroAcopios/(\d+)/?$#",
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