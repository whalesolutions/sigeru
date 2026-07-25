<?php

class CentroAcopio
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $centrosAcopio = [
        [
            "id" => 1,
            "nombre" => "CCZ 5",
            "telefono" => "26041024",
            "direccion" => "José Ellauri esq. Solano García, Montevideo, Uruguay",
            "longitud" => -57.6356,
            "latitud" => -25.2926,
            "correo" => "romariosurita@gmail.com",
            "estado" => "Inactivo",
            "capacidadMaxima" => 7000,
            "capacidadActual" => 3200
        ],
        [
            "id" => 2,
            "nombre" => "Ecocentro Buceo",
            "telefono" => "26041025",
            "direccion" => "Av. Tomas Basañez 1212, Montevideo, Uruguay",
            "longitud" => -56.1345,
            "latitud" => -34.8932,
            "correo" => "elromorelli22@hotmail.com",
            "estado" => "Activo",
            "capacidadMaxima" => 7500,
            "capacidadActual" => 1800
        ]
    ];

    /**
     * Devuelve todos los centros de acopio.
     */
    public function obtenerTodos(): array
    {
        return $this->centrosAcopio;
    }

    /**
     * Busca un centro de acopio por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->centrosAcopio as $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                return $centroAcopio;
            }
        }

        return null;
    }

    /**
     * Comprueba si existe un centro de acopio con el correo indicado.
     *
     * El parámetro $idExcluir se utiliza durante una actualización para
     * evitar considerar el correo del propio centro como duplicado.
     */
    public function existeCorreo(
        string $correo,
        ?int $idExcluir = null
    ): bool {
        $correoBuscado = strtolower(trim($correo));

        foreach ($this->centrosAcopio as $centroAcopio) {
            $mismoCorreo =
                strtolower($centroAcopio["correo"]) === $correoBuscado;

            $esOtroCentro =
                $idExcluir === null ||
                $centroAcopio["id"] !== $idExcluir;

            if ($mismoCorreo && $esOtroCentro) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo centro de acopio.
     */
    public function crear(array $datos): array
    {
        $nuevoId = $this->generarNuevoId();

        $nuevoCentroAcopio = [
            "id" => $nuevoId,
            "nombre" => trim((string) $datos["nombre"]),
            "telefono" => trim((string) $datos["telefono"]),
            "direccion" => trim((string) $datos["direccion"]),
            "longitud" => (float) $datos["longitud"],
            "latitud" => (float) $datos["latitud"],
            "correo" => strtolower(trim((string) $datos["correo"])),
            "estado" => trim((string) $datos["estado"]),
            "capacidadMaxima" => (int) $datos["capacidadMaxima"],
            "capacidadActual" => (int) $datos["capacidadActual"]
        ];

        $this->centrosAcopio[] = $nuevoCentroAcopio;

        return $nuevoCentroAcopio;
    }

    /**
     * Actualiza un centro de acopio por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->centrosAcopio as $indice => $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                $this->centrosAcopio[$indice] = [
                    "id" => $id,
                    "nombre" => trim((string) $datos["nombre"]),
                    "telefono" => trim((string) $datos["telefono"]),
                    "direccion" => trim((string) $datos["direccion"]),
                    "longitud" => (float) $datos["longitud"],
                    "latitud" => (float) $datos["latitud"],
                    "correo" => strtolower(
                        trim((string) $datos["correo"])
                    ),
                    "estado" => trim((string) $datos["estado"]),
                    "capacidadMaxima" =>
                        (int) $datos["capacidadMaxima"],
                    "capacidadActual" =>
                        (int) $datos["capacidadActual"]
                ];

                return $this->centrosAcopio[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un centro de acopio por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->centrosAcopio as $indice => $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                unset($this->centrosAcopio[$indice]);

                // Reordena los índices internos del arreglo.
                $this->centrosAcopio =
                    array_values($this->centrosAcopio);

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
        $ids = array_column($this->centrosAcopio, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}