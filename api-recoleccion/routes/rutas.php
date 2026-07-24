<?php

require_once __DIR__ . "/../controllers/rutaController.php";

$controller = new RutaController();

$metodo = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$segmentos = array_values(
    array_filter(
        explode("/", trim($uri, "/"))
    )
);

/*
 * Busca la posición del segmento "rutas".
 * Esto permite que funcione aunque la API esté dentro
 * de una carpeta adicional.
 */
$posicionRutas = array_search("rutas", $segmentos, true);

if ($posicionRutas === false) {
    responder([
        "error" => true,
        "codigo" => 404,
        "mensaje" => "Ruta no encontrada."
    ]);
}

$id = null;

if (isset($segmentos[$posicionRutas + 1])) {
    $segmentoId = $segmentos[$posicionRutas + 1];

    if (!ctype_digit($segmentoId)) {
        responder([
            "error" => true,
            "codigo" => 400,
            "mensaje" => "El ID de la ruta no es válido."
        ]);
    }

    $id = (int) $segmentoId;
}

$datos = obtenerCuerpoJson();

switch ($metodo) {
    case "GET":
        if ($id === null) {
            $resultado = $controller->listar();
        } else {
            $resultado = $controller->buscar($id);
        }

        responder($resultado);
        break;

    case "POST":
        if ($id !== null) {
            responder([
                "error" => true,
                "codigo" => 405,
                "mensaje" =>
                "No se puede crear una ruta indicando un ID."
            ]);
        }

        $resultado = $controller->crear($datos);

        responder($resultado);
        break;

    case "PUT":
    case "PATCH":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el ID de la ruta que desea actualizar."
            ]);
        }

        $resultado = $controller->actualizar($id, $datos);

        responder($resultado);
        break;

    case "DELETE":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el ID de la ruta que desea eliminar."
            ]);
        }

        $resultado = $controller->eliminar($id);

        responder($resultado);
        break;

    default:
        responder([
            "error" => true,
            "codigo" => 405,
            "mensaje" => "Método HTTP no permitido."
        ]);
}

/**
 * Obtiene y decodifica el cuerpo JSON de la solicitud.
 */
function obtenerCuerpoJson(): array
{
    $contenido = file_get_contents("php://input");

    if ($contenido === false || trim($contenido) === "") {
        return [];
    }

    $datos = json_decode($contenido, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        responder([
            "error" => true,
            "codigo" => 400,
            "mensaje" =>
            "El cuerpo de la solicitud no contiene JSON válido."
        ]);
    }

    if (!is_array($datos)) {
        responder([
            "error" => true,
            "codigo" => 400,
            "mensaje" => "El cuerpo JSON debe ser un objeto."
        ]);
    }

    return $datos;
}

/**
 * Envía una respuesta en formato JSON.
 */
function responder(array $resultado): void
{
    $codigo = $resultado["codigo"] ?? 500;

    http_response_code($codigo);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode(
        $resultado,
        JSON_UNESCAPED_UNICODE |
            JSON_PRETTY_PRINT
    );

    exit;
}
