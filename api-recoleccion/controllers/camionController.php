<?php

require_once __DIR__ . "/../models/camion.php";

class CamionController
{
    private Camion $modelo;

    public function __construct()
    {
        $this->modelo = new Camion();
    }

    /**
     * Devuelve todos los camiones.
     */
    public function listar(): array
    {
        $camiones = $this->modelo->obtenerTodos();

        return [
            "error" => false,
            "codigo" => 200,
            "cantidad" => count($camiones),
            "camiones" => $camiones
        ];
    }

    /**
     * Busca un camión por su ID.
     */
    public function buscar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID del camión no es válido."
            ];
        }

        $camion = $this->modelo->obtenerPorId($id);

        if ($camion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró el camión solicitado."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "camion" => $camion
        ];
    }

    /**
     * Crea un nuevo camión.
     */
    public function crear(array $datos): array
    {
        $errorValidacion = $this->validarDatos($datos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        $matricula = strtoupper(trim((string) $datos["matricula"]));

        if ($this->modelo->existeMatricula($matricula)) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un camión con esa matrícula."
            ];
        }

        $nuevoCamion = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Camión creado correctamente.",
            "camion" => $nuevoCamion
        ];
    }

    /**
     * Actualiza un camión existente.
     */
    public function actualizar(int $id, array $datos): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID del camión no es válido."
            ];
        }

        $camionActual = $this->modelo->obtenerPorId($id);

        if ($camionActual === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró el camión que desea actualizar."
            ];
        }

        /*
         * Permite actualizaciones parciales.
         * Los campos no enviados conservan su valor actual.
         */
        $datosCompletos = array_merge($camionActual, $datos);

        $errorValidacion = $this->validarDatos($datosCompletos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        $matricula = strtoupper(
            trim((string) $datosCompletos["matricula"])
        );

        if ($this->modelo->existeMatricula($matricula, $id)) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe otro camión con esa matrícula."
            ];
        }

        $camionActualizado = $this->modelo->actualizar(
            $id,
            $datosCompletos
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Camión actualizado correctamente.",
            "camion" => $camionActualizado
        ];
    }

    /**
     * Elimina un camión por su ID.
     */
    public function eliminar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID del camión no es válido."
            ];
        }

        $camion = $this->modelo->obtenerPorId($id);

        if ($camion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró el camión que desea eliminar."
            ];
        }

        $eliminado = $this->modelo->eliminar($id);

        if (!$eliminado) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "No se pudo eliminar el camión."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Camión eliminado correctamente."
        ];
    }

    /**
     * Valida los datos necesarios para crear o actualizar un camión.
     */
    private function validarDatos(array $datos): ?array
    {
        $camposObligatorios = [
            "matricula",
            "marca",
            "modelo",
            "capacidadCarga",
            "kilometraje",
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

        if (!$this->matriculaValida((string) $datos["matricula"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La matrícula ingresada no es válida."
            ];
        }

        if (mb_strlen(trim((string) $datos["marca"])) < 2) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La marca debe contener al menos 2 caracteres."
            ];
        }

        if (mb_strlen(trim((string) $datos["modelo"])) < 1) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El modelo no puede estar vacío."
            ];
        }

        if (
            !is_numeric($datos["capacidadCarga"]) ||
            (float) $datos["capacidadCarga"] <= 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad de carga debe ser mayor que cero."
            ];
        }

        if (
            !is_numeric($datos["kilometraje"]) ||
            (float) $datos["kilometraje"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El kilometraje no puede ser negativo."
            ];
        }

        $estadosPermitidos = [
            "Disponible",
            "En ruta",
            "En mantenimiento",
            "Inactivo"
        ];

        if (!in_array($datos["estado"], $estadosPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "El estado debe ser: Disponible, En ruta, " .
                    "En mantenimiento o Inactivo."
            ];
        }

        return null;
    }

    /**
     * Valida un formato general de matrícula.
     *
     * Acepta letras, números y guiones.
     */
    private function matriculaValida(string $matricula): bool
    {
        $matricula = strtoupper(trim($matricula));

        return preg_match(
            "/^[A-Z0-9-]{5,10}$/",
            $matricula
        ) === 1;
    }
}
