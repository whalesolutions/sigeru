<?php

require_once __DIR__ . "/../controllers/camionController.php";

$controller = new CamionController();

$metodo = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$segmentos = array_values(
    array_filter(
        explode("/", trim($uri, "/"))
    )
);

/*
 * Busca la posición del segmento "camiones".
 * Esto permite que funcione aunque la API esté dentro
 * de una carpeta adicional.
 */
$posicionCamiones = array_search(
    "camiones",
    $segmentos,
    true
);

if ($posicionCamiones === false) {
    responder([
        "error" => true,
        "codigo" => 404,
        "mensaje" => "Ruta no encontrada."
    ]);
}

$id = null;

if (isset($segmentos[$posicionCamiones + 1])) {
    $segmentoId = $segmentos[$posicionCamiones + 1];

    if (!ctype_digit($segmentoId)) {
        responder([
            "error" => true,
            "codigo" => 400,
            "mensaje" => "El ID del camión no es válido."
        ]);
    }

    $id = (int) $segmentoId;
}

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
                    "No se puede crear un camión indicando un ID."
            ]);
        }

        $datos = obtenerCuerpoJson();

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
                    "Debe indicar el ID del camión que desea actualizar."
            ]);
        }

        $datos = obtenerCuerpoJson();

        $resultado = $controller->actualizar(
            $id,
            $datos
        );

        responder($resultado);
        break;

    case "DELETE":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                    "Debe indicar el ID del camión que desea eliminar."
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

    if (
        $contenido === false ||
        trim($contenido) === ""
    ) {
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
            "mensaje" =>
                "El cuerpo JSON debe ser un objeto."
        ]);
    }

    return $datos;
}