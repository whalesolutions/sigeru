<?php

class Recoleccion
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $recolecciones = [
        [
            "id" => 1,
            "rutaId" => 1,
            "camionId" => 1,
            "cuadrillaId" => 2,
            "contenedoresIds" => [1, 2, 3],
            "fecha" => "2026-06-15",
            "horaInicio" => "07:00",
            "horaFin" => null,
            "pesoRecolectado" => 0.0,
            "observaciones" =>
            "Recolección programada para la zona Centro",
            "estado" => "Pendiente"
        ],
        [
            "id" => 2,
            "rutaId" => 2,
            "camionId" => 2,
            "cuadrillaId" => 1,
            "contenedoresIds" => [4, 5, 6],
            "fecha" => "2026-06-15",
            "horaInicio" => "14:00",
            "horaFin" => "18:00",
            "pesoRecolectado" => 4250.5,
            "observaciones" =>
            "Recolección completada sin incidentes",
            "estado" => "Finalizada"
        ]
    ];

    /**
     * Devuelve todas las recolecciones.
     */
    public function obtenerTodos(): array
    {
        return $this->recolecciones;
    }

    /**
     * Busca una recolección por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->recolecciones as $recoleccion) {
            if ($recoleccion["id"] === $id) {
                return $recoleccion;
            }
        }

        return null;
    }

    /**
     * Comprueba si ya existe una recolección con la misma
     * ruta, camión y fecha.
     *
     * El parámetro $idExcluir permite ignorar el propio registro
     * durante una actualización.
     */
    public function existeAsignacion(
        int $rutaId,
        int $camionId,
        string $fecha,
        ?int $idExcluir = null
    ): bool {
        $fechaBuscada = trim($fecha);

        foreach ($this->recolecciones as $recoleccion) {
            $mismaRuta =
                $recoleccion["rutaId"] === $rutaId;

            $mismoCamion =
                $recoleccion["camionId"] === $camionId;

            $mismaFecha =
                $recoleccion["fecha"] === $fechaBuscada;

            $esOtraRecoleccion =
                $idExcluir === null ||
                $recoleccion["id"] !== $idExcluir;

            if (
                $mismaRuta &&
                $mismoCamion &&
                $mismaFecha &&
                $esOtraRecoleccion
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea una nueva recolección.
     */
    public function crear(array $datos): array
    {
        $nuevaRecoleccion = [
            "id" => $this->generarNuevoId(),
            "rutaId" => (int) $datos["rutaId"],
            "camionId" => (int) $datos["camionId"],
            "cuadrillaId" => (int) $datos["cuadrillaId"],
            "contenedoresIds" => $this->normalizarIds(
                $datos["contenedoresIds"]
            ),
            "fecha" => trim((string) $datos["fecha"]),
            "horaInicio" => trim(
                (string) $datos["horaInicio"]
            ),
            "horaFin" => $this->normalizarHoraFin(
                $datos["horaFin"] ?? null
            ),
            "pesoRecolectado" =>
            (float) $datos["pesoRecolectado"],
            "observaciones" => trim(
                (string) $datos["observaciones"]
            ),
            "estado" => trim((string) $datos["estado"])
        ];

        $this->recolecciones[] = $nuevaRecoleccion;

        return $nuevaRecoleccion;
    }

    /**
     * Actualiza una recolección por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach (
            $this->recolecciones as $indice => $recoleccion
        ) {
            if ($recoleccion["id"] === $id) {
                $this->recolecciones[$indice] = [
                    "id" => $id,
                    "rutaId" => (int) $datos["rutaId"],
                    "camionId" => (int) $datos["camionId"],
                    "cuadrillaId" =>
                    (int) $datos["cuadrillaId"],
                    "contenedoresIds" => $this->normalizarIds(
                        $datos["contenedoresIds"]
                    ),
                    "fecha" => trim(
                        (string) $datos["fecha"]
                    ),
                    "horaInicio" => trim(
                        (string) $datos["horaInicio"]
                    ),
                    "horaFin" => $this->normalizarHoraFin(
                        $datos["horaFin"] ?? null
                    ),
                    "pesoRecolectado" =>
                    (float) $datos["pesoRecolectado"],
                    "observaciones" => trim(
                        (string) $datos["observaciones"]
                    ),
                    "estado" => trim(
                        (string) $datos["estado"]
                    )
                ];

                return $this->recolecciones[$indice];
            }
        }

        return null;
    }

    /**
     * Cambia el estado de una recolección.
     */
    public function cambiarEstado(
        int $id,
        string $estado
    ): ?array {
        foreach (
            $this->recolecciones as $indice => $recoleccion
        ) {
            if ($recoleccion["id"] === $id) {
                $this->recolecciones[$indice]["estado"] =
                    trim($estado);

                return $this->recolecciones[$indice];
            }
        }

        return null;
    }

    /**
     * Finaliza una recolección.
     */
    public function finalizar(
        int $id,
        array $datos
    ): ?array {
        foreach (
            $this->recolecciones as $indice => $recoleccion
        ) {
            if ($recoleccion["id"] === $id) {
                $this->recolecciones[$indice]["horaFin"] =
                    trim((string) $datos["horaFin"]);

                $this->recolecciones[$indice]["pesoRecolectado"] =
                    (float) $datos["pesoRecolectado"];

                $this->recolecciones[$indice]["observaciones"] =
                    trim((string) $datos["observaciones"]);

                $this->recolecciones[$indice]["estado"] =
                    "Finalizada";

                return $this->recolecciones[$indice];
            }
        }

        return null;
    }

    /**
     * Cancela una recolección y agrega el motivo
     * a las observaciones.
     */
    public function cancelar(
        int $id,
        string $motivo
    ): ?array {
        foreach (
            $this->recolecciones as $indice => $recoleccion
        ) {
            if ($recoleccion["id"] === $id) {
                $observacionesActuales = trim(
                    (string) $recoleccion["observaciones"]
                );

                $textoCancelacion =
                    "Motivo de cancelación: " . trim($motivo);

                if ($observacionesActuales !== "") {
                    $nuevasObservaciones =
                        $observacionesActuales .
                        " | " .
                        $textoCancelacion;
                } else {
                    $nuevasObservaciones = $textoCancelacion;
                }

                $this->recolecciones[$indice]["observaciones"] =
                    $nuevasObservaciones;

                $this->recolecciones[$indice]["estado"] =
                    "Cancelada";

                return $this->recolecciones[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina una recolección por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach (
            $this->recolecciones as $indice => $recoleccion
        ) {
            if ($recoleccion["id"] === $id) {
                unset($this->recolecciones[$indice]);

                $this->recolecciones =
                    array_values($this->recolecciones);

                return true;
            }
        }

        return false;
    }

    /**
     * Normaliza los IDs de los contenedores.
     *
     * Convierte todos los valores a enteros y elimina duplicados.
     */
    private function normalizarIds(array $ids): array
    {
        $idsNormalizados = array_map(
            static function ($id): int {
                return (int) $id;
            },
            $ids
        );

        return array_values(array_unique($idsNormalizados));
    }

    /**
     * Convierte la hora de finalización vacía en null.
     */
    private function normalizarHoraFin($horaFin): ?string
    {
        if ($horaFin === null) {
            return null;
        }

        $horaFin = trim((string) $horaFin);

        return $horaFin === "" ? null : $horaFin;
    }

    /**
     * Genera el siguiente ID disponible.
     */
    private function generarNuevoId(): int
    {
        $ids = array_column($this->recolecciones, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}
