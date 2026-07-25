<?php

class Camion
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $camiones = [
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

    /**
     * Devuelve todos los camiones.
     */
    public function obtenerTodos(): array
    {
        return $this->camiones;
    }

    /**
     * Busca un camión por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->camiones as $camion) {
            if ($camion["id"] === $id) {
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

        foreach ($this->camiones as $camion) {
            $mismaMatricula =
                strtoupper(trim($camion["matricula"])) ===
                $matriculaBuscada;

            $esOtroCamion =
                $idExcluir === null ||
                $camion["id"] !== $idExcluir;

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
            "marca" => trim((string) $datos["marca"]),
            "modelo" => trim((string) $datos["modelo"]),
            "capacidadCarga" => (float) $datos["capacidadCarga"],
            "kilometraje" => (float) $datos["kilometraje"],
            "estado" => trim((string) $datos["estado"])
        ];

        $this->camiones[] = $nuevoCamion;

        return $nuevoCamion;
    }

    /**
     * Actualiza un camión por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->camiones as $indice => $camion) {
            if ($camion["id"] === $id) {
                $this->camiones[$indice] = [
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

                return $this->camiones[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un camión por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->camiones as $indice => $camion) {
            if ($camion["id"] === $id) {
                unset($this->camiones[$indice]);

                $this->camiones = array_values($this->camiones);

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
        $ids = array_column($this->camiones, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}
