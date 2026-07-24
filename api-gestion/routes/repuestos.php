<?php

require_once __DIR__ . "/../controllers/repuestoController.php";

header("Content-Type: application/json; charset=UTF-8");

$metodo = $_SERVER["REQUEST_METHOD"];
$ruta = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$controller = new RepuestoController();

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
 * Envía una respuesta JSON usando el código HTTP
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
| GET - Listar repuestos con stock bajo
|--------------------------------------------------------------------------
|
| Esta ruta debe declararse antes que /repuestos/{id},
| para evitar que se intente interpretar "stock-bajo" como un ID.
|
*/
if (
    $metodo === "GET"
    && preg_match("#/repuestos/stock-bajo/?$#", $ruta)
) {
    responderJson($controller->listarStockBajo());
}

/*
|--------------------------------------------------------------------------
| GET - Listar todos los repuestos
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && (
        str_ends_with($ruta, "/api-repuestos/")
        || str_ends_with($ruta, "/api-repuestos/index.php")
        || str_ends_with($ruta, "/repuestos")
        || str_ends_with($ruta, "/repuestos/")
    )
) {
    responderJson($controller->listar());
}

/*
|--------------------------------------------------------------------------
| GET - Buscar un repuesto por ID
|--------------------------------------------------------------------------
*/
if (
    $metodo === "GET"
    && preg_match(
        "#/repuestos/(\d+)/?$#",
        $ruta,
        $coincidencias
    )
) {
    $id = (int) $coincidencias[1];

    responderJson($controller->buscar($id));
}

/*
|--------------------------------------------------------------------------
| POST - Crear un repuesto
|--------------------------------------------------------------------------
*/
if (
    $metodo === "POST"
    && preg_match("#/repuestos/?$#", $ruta)
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
| PUT - Actualizar un repuesto
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PUT"
    && preg_match(
        "#/repuestos/(\d+)/?$#",
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
| PATCH - Ingresar stock
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PATCH"
    && preg_match(
        "#/repuestos/(\d+)/ingresar-stock/?$#",
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

    responderJson($controller->ingresarStock($id, $datos));
}

/*
|--------------------------------------------------------------------------
| PATCH - Retirar stock
|--------------------------------------------------------------------------
*/
if (
    $metodo === "PATCH"
    && preg_match(
        "#/repuestos/(\d+)/retirar-stock/?$#",
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

    responderJson($controller->retirarStock($id, $datos));
}

/*
|--------------------------------------------------------------------------
| DELETE - Eliminar un repuesto
|--------------------------------------------------------------------------
*/
if (
    $metodo === "DELETE"
    && preg_match(
        "#/repuestos/(\d+)/?$#",
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