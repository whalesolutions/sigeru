<?php

class Camion
{
    /**
     * Inicializa los datos simulados únicamente
     * si todavía no existen en la sesión.
     */
    public function __construct()
    {
        if (!isset($_SESSION["camiones"])) {
            $_SESSION["camiones"] = [
                [
                    "id" => 1,
                    "matricula" => "STC-1234",
                    "marca" => "Mercedes-Benz",
                    "modelo" => "Atego 1726",
                    "capacidadCarga" => 12000.0,
                    "kilometraje" => 85400.0,
                    "estado" => "Disponible"
                ],
                [
                    "id" => 2,
                    "matricula" => "SAB-5678",
                    "marca" => "Volkswagen",
                    "modelo" => "Constellation 17.280",
                    "capacidadCarga" => 11000.0,
                    "kilometraje" => 132500.0,
                    "estado" => "En mantenimiento"
                ]
            ];
        }
    }

    /**
     * Devuelve todos los camiones.
     */
    public function obtenerTodos(): array
    {
        return $_SESSION["camiones"];
    }

    /**
     * Busca un camión por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($_SESSION["camiones"] as $camion) {
            if ((int) $camion["id"] === $id) {
                return $camion;
            }
        }

        return null;
    }

    /**
     * Verifica si existe un camión con la matrícula indicada.
     *
     * El parámetro $idExcluir permite ignorar el propio registro
     * durante una actualización.
     */
    public function existeMatricula(
        string $matricula,
        ?int $idExcluir = null
    ): bool {
        $matriculaBuscada = strtoupper(trim($matricula));

        foreach ($_SESSION["camiones"] as $camion) {
            $mismaMatricula =
                strtoupper(trim($camion["matricula"])) ===
                $matriculaBuscada;

            $esOtroCamion =
                $idExcluir === null ||
                (int) $camion["id"] !== $idExcluir;

            if ($mismaMatricula && $esOtroCamion) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo camión.
     */
    public function crear(array $datos): array
    {
        $nuevoCamion = [
            "id" => $this->generarNuevoId(),

            "matricula" => strtoupper(
                trim((string) $datos["matricula"])
            ),

            "marca" => trim(
                (string) $datos["marca"]
            ),

            "modelo" => trim(
                (string) $datos["modelo"]
            ),

            "capacidadCarga" =>
                (float) $datos["capacidadCarga"],

            "kilometraje" =>
                isset($datos["kilometraje"])
                ? (float) $datos["kilometraje"]
                : 0,

            "estado" => trim(
                (string) $datos["estado"]
            )
        ];

        $_SESSION["camiones"][] = $nuevoCamion;

        return $nuevoCamion;
    }

    /**
     * Actualiza un camión por su ID.
     */
    public function actualizar(
        int $id,
        array $datos
    ): ?array {
        foreach (
            $_SESSION["camiones"] as $indice => $camion
        ) {
            if ((int) $camion["id"] === $id) {
                $_SESSION["camiones"][$indice] = [
                    "id" => $id,

                    "matricula" => strtoupper(
                        trim((string) $datos["matricula"])
                    ),

                    "marca" => trim(
                        (string) $datos["marca"]
                    ),

                    "modelo" => trim(
                        (string) $datos["modelo"]
                    ),

                    "capacidadCarga" =>
                        (float) $datos["capacidadCarga"],

                    "kilometraje" =>
                        (float) $datos["kilometraje"],

                    "estado" => trim(
                        (string) $datos["estado"]
                    )
                ];

                return $_SESSION["camiones"][$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un camión por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach (
            $_SESSION["camiones"] as $indice => $camion
        ) {
            if ((int) $camion["id"] === $id) {
                unset($_SESSION["camiones"][$indice]);

                $_SESSION["camiones"] =
                    array_values($_SESSION["camiones"]);

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
        if (empty($_SESSION["camiones"])) {
            return 1;
        }

        $ids = array_column(
            $_SESSION["camiones"],
            "id"
        );

        return max($ids) + 1;
    }
}
