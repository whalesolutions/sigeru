<?php

require_once __DIR__ . "/../controllers/recoleccionController.php";

$controller = new RecoleccionController();

$metodo = $_SERVER["REQUEST_METHOD"];

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$segmentos = array_values(
    array_filter(
        explode("/", trim($uri, "/"))
    )
);

/*
 * Busca la posición del segmento "recolecciones".
 * Esto permite que la ruta funcione aunque la API esté
 * dentro de una carpeta adicional.
 */
$posicionRecolecciones = array_search(
    "recolecciones",
    $segmentos,
    true
);

if ($posicionRecolecciones === false) {
    responder([
        "error" => true,
        "codigo" => 404,
        "mensaje" => "Ruta no encontrada."
    ]);
}

$id = null;
$accion = null;

if (isset($segmentos[$posicionRecolecciones + 1])) {
    $segmentoId = $segmentos[$posicionRecolecciones + 1];

    if (!ctype_digit($segmentoId)) {
        responder([
            "error" => true,
            "codigo" => 400,
            "mensaje" =>
            "El ID de la recolección no es válido."
        ]);
    }

    $id = (int) $segmentoId;
}

if (isset($segmentos[$posicionRecolecciones + 2])) {
    $accion = strtolower(
        trim($segmentos[$posicionRecolecciones + 2])
    );
}

/*
 * No se permiten segmentos adicionales.
 *
 * Ejemplo inválido:
 * /recolecciones/1/iniciar/extra
 */
if (isset($segmentos[$posicionRecolecciones + 3])) {
    responder([
        "error" => true,
        "codigo" => 404,
        "mensaje" => "Ruta no encontrada."
    ]);
}

$datos = obtenerCuerpoJson();

switch ($metodo) {
    case "GET":
        if ($accion !== null) {
            responder([
                "error" => true,
                "codigo" => 405,
                "mensaje" =>
                "La acción indicada no admite solicitudes GET."
            ]);
        }

        if ($id === null) {
            $resultado = $controller->listar();
        } else {
            $resultado = $controller->buscar($id);
        }

        responder($resultado);
        break;

    case "POST":
        if ($id !== null || $accion !== null) {
            responder([
                "error" => true,
                "codigo" => 405,
                "mensaje" =>
                "No se puede crear una recolección " .
                    "indicando un ID o una acción."
            ]);
        }

        $resultado = $controller->crear($datos);

        responder($resultado);
        break;

    case "PUT":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el ID de la recolección " .
                    "que desea actualizar."
            ]);
        }

        if ($accion !== null) {
            responder([
                "error" => true,
                "codigo" => 405,
                "mensaje" =>
                "Las acciones especiales deben realizarse " .
                    "mediante PATCH."
            ]);
        }

        $resultado = $controller->actualizar($id, $datos);

        responder($resultado);
        break;

    case "PATCH":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el ID de la recolección."
            ]);
        }

        if ($accion === null) {
            $resultado = $controller->actualizar(
                $id,
                $datos
            );

            responder($resultado);
        }

        switch ($accion) {
            case "iniciar":
                $resultado = $controller->iniciar($id);
                break;

            case "finalizar":
                $resultado = $controller->finalizar(
                    $id,
                    $datos
                );
                break;

            case "cancelar":
                $resultado = $controller->cancelar(
                    $id,
                    $datos
                );
                break;

            default:
                responder([
                    "error" => true,
                    "codigo" => 404,
                    "mensaje" =>
                    "La acción indicada no existe."
                ]);
        }

        responder($resultado);
        break;

    case "DELETE":
        if ($id === null) {
            responder([
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el ID de la recolección " .
                    "que desea eliminar."
            ]);
        }

        if ($accion !== null) {
            responder([
                "error" => true,
                "codigo" => 405,
                "mensaje" =>
                "No se puede eliminar una acción específica."
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
            "mensaje" =>
            "El cuerpo JSON debe ser un objeto."
        ]);
    }

    return $datos;
}

/**
 * Envía la respuesta en formato JSON.
 */
function responder(array $resultado): void
{
    $codigo = $resultado["codigo"] ?? 500;

    http_response_code($codigo);

    header(
        "Content-Type: application/json; charset=UTF-8"
    );

    echo json_encode(
        $resultado,
        JSON_UNESCAPED_UNICODE |
            JSON_PRETTY_PRINT
    );

    exit;
}
