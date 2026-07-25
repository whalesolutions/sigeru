<?php

require_once __DIR__ . "/../models/ruta.php";

class RutaController
{
    private Ruta $modelo;

    public function __construct()
    {
        $this->modelo = new Ruta();
    }

    /**
     * Devuelve todas las rutas.
     */
    public function listar(): array
    {
        $rutas = $this->modelo->obtenerTodos();

        return [
            "error" => false,
            "codigo" => 200,
            "cantidad" => count($rutas),
            "rutas" => $rutas
        ];
    }

    /**
     * Busca una ruta por su ID.
     */
    public function buscar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la ruta no es válido."
            ];
        }

        $ruta = $this->modelo->obtenerPorId($id);

        if ($ruta === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró la ruta solicitada."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "ruta" => $ruta
        ];
    }

    /**
     * Crea una nueva ruta.
     */
    public function crear(array $datos): array
    {
        $errorValidacion = $this->validarDatos($datos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        $nombre = trim((string) $datos["nombre"]);
        $fecha = trim((string) $datos["fecha"]);

        if ($this->modelo->existeRuta($nombre, $fecha)) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Ya existe una ruta con ese nombre para la fecha indicada."
            ];
        }

        $nuevaRuta = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Ruta creada correctamente.",
            "ruta" => $nuevaRuta
        ];
    }

    /**
     * Actualiza una ruta existente.
     */
    public function actualizar(int $id, array $datos): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la ruta no es válido."
            ];
        }

        $rutaActual = $this->modelo->obtenerPorId($id);

        if ($rutaActual === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" =>
                "No se encontró la ruta que desea actualizar."
            ];
        }

        /*
         * Permite actualizaciones parciales.
         * Los campos no enviados conservan su valor actual.
         */
        $datosCompletos = array_merge($rutaActual, $datos);

        $errorValidacion = $this->validarDatos($datosCompletos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        $nombre = trim((string) $datosCompletos["nombre"]);
        $fecha = trim((string) $datosCompletos["fecha"]);

        if ($this->modelo->existeRuta($nombre, $fecha, $id)) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Ya existe otra ruta con ese nombre para la fecha indicada."
            ];
        }

        $rutaActualizada = $this->modelo->actualizar(
            $id,
            $datosCompletos
        );

        if ($rutaActualizada === null) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "No se pudo actualizar la ruta."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Ruta actualizada correctamente.",
            "ruta" => $rutaActualizada
        ];
    }

    /**
     * Elimina una ruta por su ID.
     */
    public function eliminar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la ruta no es válido."
            ];
        }

        $ruta = $this->modelo->obtenerPorId($id);

        if ($ruta === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" =>
                "No se encontró la ruta que desea eliminar."
            ];
        }

        $eliminada = $this->modelo->eliminar($id);

        if (!$eliminada) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "No se pudo eliminar la ruta."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Ruta eliminada correctamente."
        ];
    }

    /**
     * Valida los datos necesarios para crear o actualizar una ruta.
     */
    private function validarDatos(array $datos): ?array
    {
        $camposObligatorios = [
            "nombre",
            "zona",
            "fecha",
            "horaInicio",
            "horaFin",
            "estado"
        ];

        foreach ($camposObligatorios as $campo) {
            if (
                !array_key_exists($campo, $datos) ||
                $datos[$campo] === null ||
                (
                    is_string($datos[$campo]) &&
                    trim($datos[$campo]) === ""
                )
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El campo '$campo' es obligatorio."
                ];
            }
        }

        if (mb_strlen(trim((string) $datos["nombre"])) < 3) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "El nombre de la ruta debe tener al menos 3 caracteres."
            ];
        }

        if (mb_strlen(trim((string) $datos["zona"])) < 2) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La zona debe tener al menos 2 caracteres."
            ];
        }

        if (!$this->fechaValida((string) $datos["fecha"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La fecha debe tener el formato YYYY-MM-DD."
            ];
        }

        if (!$this->horaValida((string) $datos["horaInicio"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de inicio debe tener el formato HH:MM."
            ];
        }

        if (!$this->horaValida((string) $datos["horaFin"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de finalización debe tener el formato HH:MM."
            ];
        }

        if (
            strtotime($datos["horaFin"]) <=
            strtotime($datos["horaInicio"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de finalización debe ser posterior " .
                    "a la hora de inicio."
            ];
        }

        $estadosPermitidos = [
            "Planificada",
            "En curso",
            "Finalizada",
            "Cancelada"
        ];

        if (!in_array($datos["estado"], $estadosPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "El estado debe ser: Planificada, En curso, " .
                    "Finalizada o Cancelada."
            ];
        }

        return null;
    }

    /**
     * Valida una fecha con formato YYYY-MM-DD.
     */
    private function fechaValida(string $fecha): bool
    {
        $fecha = trim($fecha);

        $objetoFecha = DateTime::createFromFormat(
            "Y-m-d",
            $fecha
        );

        return $objetoFecha !== false &&
            $objetoFecha->format("Y-m-d") === $fecha;
    }

    /**
     * Valida una hora con formato HH:MM.
     */
    private function horaValida(string $hora): bool
    {
        $hora = trim($hora);

        $objetoHora = DateTime::createFromFormat(
            "H:i",
            $hora
        );

        return $objetoHora !== false &&
            $objetoHora->format("H:i") === $hora;
    }
}
