<?php

class Contenedor
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $contenedores = [
        [
            "id" => 1,
            "codigo" => "CH-001",
            "direccion" => "José Ellauri esq. Solano García, Montevideo",
            "longitud" => -56.1635,
            "latitud" => -34.9068,
            "estado" => "Inactivo",
            "capacidadMaxima" => 3200,
            "capacidadActual" => 1800
        ],
        [
            "id" => 2,
            "codigo" => "CH-002",
            "direccion" => "Av. Tomás Basañez 1212, Montevideo",
            "longitud" => -56.1345,
            "latitud" => -34.8932,
            "estado" => "Activo",
            "capacidadMaxima" => 800,
            "capacidadActual" => 250
        ]
    ];

    /**
     * Devuelve todos los contenedores.
     */
    public function obtenerTodos(): array
    {
        return $this->contenedores;
    }

    /**
     * Busca un contenedor por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->contenedores as $contenedor) {
            if ($contenedor["id"] === $id) {
                return $contenedor;
            }
        }

        return null;
    }

    /**
     * Comprueba si ya existe un contenedor con el código indicado.
     *
     * El parámetro $idExcluir permite ignorar el propio registro
     * durante una actualización.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $codigoBuscado = strtoupper(trim($codigo));

        foreach ($this->contenedores as $contenedor) {
            $mismoCodigo =
                strtoupper($contenedor["codigo"]) === $codigoBuscado;

            $esOtroContenedor =
                $idExcluir === null ||
                $contenedor["id"] !== $idExcluir;

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
        $nuevoId = $this->generarNuevoId();

        $nuevoContenedor = [
            "id" => $nuevoId,
            "codigo" => strtoupper(
                trim((string) $datos["codigo"])
            ),
            "direccion" => trim((string) $datos["direccion"]),
            "longitud" => (float) $datos["longitud"],
            "latitud" => (float) $datos["latitud"],
            "estado" => trim((string) $datos["estado"]),
            "capacidadMaxima" => (int) $datos["capacidadMaxima"],
            "capacidadActual" => (int) $datos["capacidadActual"]
        ];

        $this->contenedores[] = $nuevoContenedor;

        return $nuevoContenedor;
    }

    /**
     * Actualiza un contenedor por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->contenedores as $indice => $contenedor) {
            if ($contenedor["id"] === $id) {
                $this->contenedores[$indice] = [
                    "id" => $id,
                    "codigo" => strtoupper(
                        trim((string) $datos["codigo"])
                    ),
                    "direccion" => trim(
                        (string) $datos["direccion"]
                    ),
                    "longitud" => (float) $datos["longitud"],
                    "latitud" => (float) $datos["latitud"],
                    "estado" => trim((string) $datos["estado"]),
                    "capacidadMaxima" =>
                        (int) $datos["capacidadMaxima"],
                    "capacidadActual" =>
                        (int) $datos["capacidadActual"]
                ];

                return $this->contenedores[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un contenedor por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->contenedores as $indice => $contenedor) {
            if ($contenedor["id"] === $id) {
                unset($this->contenedores[$indice]);

                $this->contenedores =
                    array_values($this->contenedores);

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
        $ids = array_column($this->contenedores, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}