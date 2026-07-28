<?php

class Contenedor
{
    /**
     * Inicializa los contenedores simulados solamente
     * cuando todavía no existen en la sesión.
     */
    public function __construct()
    {
        if (!isset($_SESSION["contenedores"])) {
            $_SESSION["contenedores"] = [
                [
                    "id" => 1,
                    "codigo" => "CH-001",
                    "direccion" =>
                        "José Ellauri esq. Solano García, Montevideo",
                    "longitud" => -56.1635,
                    "latitud" => -34.9068,
                    "estado" => "Inactivo",
                    "capacidadMaxima" => 3200,
                    "capacidadActual" => 1800,
                    "fechaInstalacion" => "2026-07-01"
                ],
                [
                    "id" => 2,
                    "codigo" => "CH-002",
                    "direccion" =>
                        "Av. Tomás Basañez 1212, Montevideo",
                    "longitud" => -56.1345,
                    "latitud" => -34.8932,
                    "estado" => "Activo",
                    "capacidadMaxima" => 800,
                    "capacidadActual" => 250,
                    "fechaInstalacion" => "2026-07-10"
                ]
            ];
        }
    }

    /**
     * Devuelve todos los contenedores.
     */
    public function obtenerTodos(): array
    {
        return $_SESSION["contenedores"];
    }

    /**
     * Busca un contenedor por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($_SESSION["contenedores"] as $contenedor) {
            if ((int) $contenedor["id"] === $id) {
                return $contenedor;
            }
        }

        return null;
    }

    /**
     * Comprueba si ya existe un contenedor
     * con el código indicado.
     *
     * El parámetro $idExcluir permite ignorar
     * el registro actual durante una actualización.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $codigoBuscado = strtoupper(trim($codigo));

        foreach ($_SESSION["contenedores"] as $contenedor) {
            $mismoCodigo =
                strtoupper(trim($contenedor["codigo"])) ===
                $codigoBuscado;

            $esOtroContenedor =
                $idExcluir === null ||
                (int) $contenedor["id"] !== $idExcluir;

            if ($mismoCodigo && $esOtroContenedor) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo contenedor.
     */
    public function crear(array $datos): array
    {
        $nuevoContenedor = [
            "id" => $this->generarNuevoId(),

            "codigo" => strtoupper(
                trim((string) $datos["codigo"])
            ),

            "direccion" => trim(
                (string) $datos["direccion"]
            ),

            "longitud" => (float) $datos["longitud"],

            "latitud" => (float) $datos["latitud"],

            "estado" => trim(
                (string) $datos["estado"]
            ),

            "capacidadMaxima" =>
                (int) $datos["capacidadMaxima"],

            "capacidadActual" =>
                isset($datos["capacidadActual"])
                ? (int) $datos["capacidadActual"]
                : 0,

            "fechaInstalacion" =>
                isset($datos["fechaInstalacion"])
                ? trim((string) $datos["fechaInstalacion"])
                : null
        ];

        $_SESSION["contenedores"][] = $nuevoContenedor;

        return $nuevoContenedor;
    }

    /**
     * Actualiza un contenedor por su ID.
     */
    public function actualizar(
        int $id,
        array $datos
    ): ?array {
        foreach (
            $_SESSION["contenedores"] as $indice => $contenedor
        ) {
            if ((int) $contenedor["id"] === $id) {
                $_SESSION["contenedores"][$indice] = [
                    "id" => $id,

                    "codigo" => strtoupper(
                        trim((string) $datos["codigo"])
                    ),

                    "direccion" => trim(
                        (string) $datos["direccion"]
                    ),

                    "longitud" => (float) $datos["longitud"],

                    "latitud" => (float) $datos["latitud"],

                    "estado" => trim(
                        (string) $datos["estado"]
                    ),

                    "capacidadMaxima" =>
                        (int) $datos["capacidadMaxima"],

                    "capacidadActual" =>
                        (int) $datos["capacidadActual"],

                    "fechaInstalacion" =>
                        isset($datos["fechaInstalacion"])
                        ? trim(
                            (string) $datos["fechaInstalacion"]
                        )
                        : (
                            $contenedor["fechaInstalacion"]
                            ?? null
                        )
                ];

                return $_SESSION["contenedores"][$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un contenedor por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach (
            $_SESSION["contenedores"] as $indice => $contenedor
        ) {
            if ((int) $contenedor["id"] === $id) {
                unset($_SESSION["contenedores"][$indice]);

                $_SESSION["contenedores"] =
                    array_values($_SESSION["contenedores"]);

                return true;
            }
        }

        return false;
    }

    /**
     * Genera el siguiente ID disponible.
     */
    private function generarNuevoId(): int
    {
        if (empty($_SESSION["contenedores"])) {
            return 1;
        }

        $ids = array_column(
            $_SESSION["contenedores"],
            "id"
        );

        return max($ids) + 1;
    }
}