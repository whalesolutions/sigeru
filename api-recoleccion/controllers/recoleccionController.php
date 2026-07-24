<?php

require_once __DIR__ . "/../models/recoleccion.php";

class RecoleccionController
{
    private Recoleccion $modelo;

    public function __construct()
    {
        $this->modelo = new Recoleccion();
    }

    /**
     * Devuelve todas las recolecciones.
     */
    public function listar(): array
    {
        $recolecciones = $this->modelo->obtenerTodos();

        return [
            "error" => false,
            "codigo" => 200,
            "cantidad" => count($recolecciones),
            "recolecciones" => $recolecciones
        ];
    }

    /**
     * Busca una recolección por su ID.
     */
    public function buscar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la recolección no es válido."
            ];
        }

        $recoleccion = $this->modelo->obtenerPorId($id);

        if ($recoleccion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró la recolección solicitada."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "recoleccion" => $recoleccion
        ];
    }

    /**
     * Crea una nueva recolección.
     */
    public function crear(array $datos): array
    {
        $datos = $this->aplicarValoresIniciales($datos);

        $errorValidacion = $this->validarDatos($datos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        if (
            $this->modelo->existeAsignacion(
                (int) $datos["rutaId"],
                (int) $datos["camionId"],
                (string) $datos["fecha"]
            )
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Ya existe una recolección con esa ruta, camión y fecha."
            ];
        }

        $nuevaRecoleccion = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Recolección creada correctamente.",
            "recoleccion" => $nuevaRecoleccion
        ];
    }

    /**
     * Actualiza una recolección existente.
     */
    public function actualizar(int $id, array $datos): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la recolección no es válido."
            ];
        }

        $recoleccionActual = $this->modelo->obtenerPorId($id);

        if ($recoleccionActual === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" =>
                "No se encontró la recolección que desea actualizar."
            ];
        }

        if ($recoleccionActual["estado"] === "Finalizada") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "No se puede modificar una recolección finalizada."
            ];
        }

        if ($recoleccionActual["estado"] === "Cancelada") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "No se puede modificar una recolección cancelada."
            ];
        }

        /*
         * Permite actualizaciones parciales.
         * Los datos no enviados conservan su valor actual.
         */
        $datosCompletos = array_merge($recoleccionActual, $datos);

        $errorValidacion = $this->validarDatos($datosCompletos);

        if ($errorValidacion !== null) {
            return $errorValidacion;
        }

        if (
            $this->modelo->existeAsignacion(
                (int) $datosCompletos["rutaId"],
                (int) $datosCompletos["camionId"],
                (string) $datosCompletos["fecha"],
                $id
            )
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Ya existe otra recolección con esa ruta, camión y fecha."
            ];
        }

        $recoleccionActualizada = $this->modelo->actualizar(
            $id,
            $datosCompletos
        );

        if ($recoleccionActualizada === null) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "No se pudo actualizar la recolección."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Recolección actualizada correctamente.",
            "recoleccion" => $recoleccionActualizada
        ];
    }

    /**
     * Inicia una recolección pendiente.
     */
    public function iniciar(int $id): array
    {
        $recoleccion = $this->modelo->obtenerPorId($id);

        if ($recoleccion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró la recolección solicitada."
            ];
        }

        if ($recoleccion["estado"] !== "Pendiente") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Solo se pueden iniciar recolecciones pendientes."
            ];
        }

        $recoleccionActualizada = $this->modelo->cambiarEstado(
            $id,
            "En curso"
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Recolección iniciada correctamente.",
            "recoleccion" => $recoleccionActualizada
        ];
    }

    /**
     * Finaliza una recolección en curso.
     */
    public function finalizar(int $id, array $datos): array
    {
        $recoleccion = $this->modelo->obtenerPorId($id);

        if ($recoleccion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró la recolección solicitada."
            ];
        }

        if ($recoleccion["estado"] !== "En curso") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "Solo se pueden finalizar recolecciones en curso."
            ];
        }

        if (
            !array_key_exists("pesoRecolectado", $datos) ||
            !is_numeric($datos["pesoRecolectado"]) ||
            (float) $datos["pesoRecolectado"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar un peso recolectado válido."
            ];
        }

        $horaFin = $datos["horaFin"] ?? date("H:i");

        if (!$this->horaValida((string) $horaFin)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de finalización debe tener formato HH:MM."
            ];
        }

        if (
            strtotime((string) $horaFin) <=
            strtotime((string) $recoleccion["horaInicio"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de finalización debe ser posterior " .
                    "a la hora de inicio."
            ];
        }

        $datosFinalizacion = [
            "horaFin" => trim((string) $horaFin),
            "pesoRecolectado" =>
            (float) $datos["pesoRecolectado"],
            "observaciones" => trim(
                (string) (
                    $datos["observaciones"] ??
                    $recoleccion["observaciones"]
                )
            ),
            "estado" => "Finalizada"
        ];

        $recoleccionActualizada = $this->modelo->finalizar(
            $id,
            $datosFinalizacion
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Recolección finalizada correctamente.",
            "recoleccion" => $recoleccionActualizada
        ];
    }

    /**
     * Cancela una recolección pendiente o en curso.
     */
    public function cancelar(int $id, array $datos = []): array
    {
        $recoleccion = $this->modelo->obtenerPorId($id);

        if ($recoleccion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "No se encontró la recolección solicitada."
            ];
        }

        if ($recoleccion["estado"] === "Finalizada") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "No se puede cancelar una recolección finalizada."
            ];
        }

        if ($recoleccion["estado"] === "Cancelada") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "La recolección ya está cancelada."
            ];
        }

        $motivo = trim((string) ($datos["motivo"] ?? ""));

        if ($motivo === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar el motivo de la cancelación."
            ];
        }

        $recoleccionActualizada = $this->modelo->cancelar(
            $id,
            $motivo
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Recolección cancelada correctamente.",
            "recoleccion" => $recoleccionActualizada
        ];
    }

    /**
     * Elimina una recolección.
     */
    public function eliminar(int $id): array
    {
        if ($id <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El ID de la recolección no es válido."
            ];
        }

        $recoleccion = $this->modelo->obtenerPorId($id);

        if ($recoleccion === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" =>
                "No se encontró la recolección que desea eliminar."
            ];
        }

        if ($recoleccion["estado"] === "En curso") {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" =>
                "No se puede eliminar una recolección en curso."
            ];
        }

        $eliminada = $this->modelo->eliminar($id);

        if (!$eliminada) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "No se pudo eliminar la recolección."
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Recolección eliminada correctamente."
        ];
    }

    /**
     * Aplica valores iniciales a los campos opcionales.
     */
    private function aplicarValoresIniciales(array $datos): array
    {
        $datos["horaFin"] = $datos["horaFin"] ?? null;
        $datos["pesoRecolectado"] =
            $datos["pesoRecolectado"] ?? 0;
        $datos["observaciones"] =
            $datos["observaciones"] ?? "";
        $datos["estado"] =
            $datos["estado"] ?? "Pendiente";

        return $datos;
    }

    /**
     * Valida los datos de una recolección.
     */
    private function validarDatos(array $datos): ?array
    {
        $camposObligatorios = [
            "rutaId",
            "camionId",
            "cuadrillaId",
            "contenedoresIds",
            "fecha",
            "horaInicio",
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

        foreach (["rutaId", "camionId", "cuadrillaId"] as $campo) {
            if (
                filter_var(
                    $datos[$campo],
                    FILTER_VALIDATE_INT
                ) === false ||
                (int) $datos[$campo] <= 0
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" =>
                    "El campo '$campo' debe ser un entero positivo."
                ];
            }
        }

        if (
            !is_array($datos["contenedoresIds"]) ||
            count($datos["contenedoresIds"]) === 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Debe indicar al menos un contenedor."
            ];
        }

        foreach ($datos["contenedoresIds"] as $contenedorId) {
            if (
                filter_var(
                    $contenedorId,
                    FILTER_VALIDATE_INT
                ) === false ||
                (int) $contenedorId <= 0
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" =>
                    "Todos los IDs de contenedores deben ser válidos."
                ];
            }
        }

        if (!$this->fechaValida((string) $datos["fecha"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La fecha debe tener formato YYYY-MM-DD."
            ];
        }

        if (!$this->horaValida((string) $datos["horaInicio"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "La hora de inicio debe tener formato HH:MM."
            ];
        }

        if (
            $datos["horaFin"] !== null &&
            trim((string) $datos["horaFin"]) !== ""
        ) {
            if (!$this->horaValida((string) $datos["horaFin"])) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" =>
                    "La hora de finalización debe tener formato HH:MM."
                ];
            }

            if (
                strtotime((string) $datos["horaFin"]) <=
                strtotime((string) $datos["horaInicio"])
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" =>
                    "La hora de finalización debe ser posterior " .
                        "a la hora de inicio."
                ];
            }
        }

        if (
            !is_numeric($datos["pesoRecolectado"]) ||
            (float) $datos["pesoRecolectado"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "El peso recolectado no puede ser negativo."
            ];
        }

        $estadosPermitidos = [
            "Pendiente",
            "En curso",
            "Finalizada",
            "Cancelada"
        ];

        if (!in_array($datos["estado"], $estadosPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "El estado debe ser: Pendiente, En curso, " .
                    "Finalizada o Cancelada."
            ];
        }

        if (
            mb_strlen(
                trim((string) $datos["observaciones"])
            ) > 500
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" =>
                "Las observaciones no pueden superar 500 caracteres."
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
