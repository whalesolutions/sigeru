<?php

class Vertedero
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $vertederos = [
        [
            "id" => 1,
            "nombre" => "DFR. Felipe Cardozo",
            "telefono" => "26984353",
            "direccion" => "Camino Felipe Cardozo 12100, Montevideo, Uruguay",
            "longitud" => -56.1085,
            "latitud" => -34.8413,
            "correo" => "nachomenendezf@gmail.com",
            "estado" => "Activo",
            "capacidadMaxima" => 1000,
            "capacidadActual" => 650
        ]
    ];

    /**
     * Devuelve todos los vertederos.
     */
    public function obtenerTodos(): array
    {
        return $this->vertederos;
    }

    /**
     * Busca un vertedero por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->vertederos as $vertedero) {
            if ($vertedero["id"] === $id) {
                return $vertedero;
            }
        }

        return null;
    }

    /**
     * Verifica si ya existe un vertedero con el correo indicado.
     *
     * El parámetro $idExcluir permite ignorar el propio registro
     * durante una actualización.
     */
    public function existeCorreo(
        string $correo,
        ?int $idExcluir = null
    ): bool {
        $correoBuscado = strtolower(trim($correo));

        foreach ($this->vertederos as $vertedero) {

            $mismoCorreo =
                strtolower($vertedero["correo"]) === $correoBuscado;

            $esOtroVertedero =
                $idExcluir === null ||
                $vertedero["id"] !== $idExcluir;

            if ($mismoCorreo && $esOtroVertedero) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo vertedero.
     */
    public function crear(array $datos): array
    {
        $nuevoId = $this->generarNuevoId();

        $nuevoVertedero = [
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

        $this->vertederos[] = $nuevoVertedero;

        return $nuevoVertedero;
    }

    /**
     * Actualiza un vertedero por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->vertederos as $indice => $vertedero) {

            if ($vertedero["id"] === $id) {

                $this->vertederos[$indice] = [
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

                return $this->vertederos[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un vertedero por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->vertederos as $indice => $vertedero) {

            if ($vertedero["id"] === $id) {

                unset($this->vertederos[$indice]);

                $this->vertederos =
                    array_values($this->vertederos);

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
        $ids = array_column($this->vertederos, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}