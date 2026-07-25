<?php

class Ruta
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $rutas = [
        [
            "id" => 1,
            "nombre" => "Ruta Centro Matutina",
            "zona" => "Centro",
            "fecha" => "2026-06-10",
            "horaInicio" => "07:00",
            "horaFin" => "11:30",
            "estado" => "Planificada"
        ],
        [
            "id" => 2,
            "nombre" => "Ruta Pocitos Vespertina",
            "zona" => "Pocitos",
            "fecha" => "2026-06-10",
            "horaInicio" => "14:00",
            "horaFin" => "18:00",
            "estado" => "En curso"
        ]
    ];

    /**
     * Devuelve todas las rutas.
     */
    public function obtenerTodos(): array
    {
        return $this->rutas;
    }

    /**
     * Busca una ruta por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->rutas as $ruta) {
            if ($ruta["id"] === $id) {
                return $ruta;
            }
        }

        return null;
    }

    /**
     * Verifica si existe una ruta con el mismo nombre y fecha.
     *
     * El parámetro $idExcluir permite ignorar la propia ruta
     * durante una actualización.
     */
    public function existeRuta(
        string $nombre,
        string $fecha,
        ?int $idExcluir = null
    ): bool {
        $nombreBuscado = strtolower(trim($nombre));
        $fechaBuscada = trim($fecha);

        foreach ($this->rutas as $ruta) {
            $mismoNombre =
                strtolower(trim($ruta["nombre"])) === $nombreBuscado;

            $mismaFecha =
                trim($ruta["fecha"]) === $fechaBuscada;

            $esOtraRuta =
                $idExcluir === null ||
                $ruta["id"] !== $idExcluir;

            if ($mismoNombre && $mismaFecha && $esOtraRuta) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea una nueva ruta.
     */
    public function crear(array $datos): array
    {
        $nuevaRuta = [
            "id" => $this->generarNuevoId(),
            "nombre" => trim((string) $datos["nombre"]),
            "zona" => trim((string) $datos["zona"]),
            "fecha" => trim((string) $datos["fecha"]),
            "horaInicio" => trim((string) $datos["horaInicio"]),
            "horaFin" => trim((string) $datos["horaFin"]),
            "estado" => trim((string) $datos["estado"])
        ];

        $this->rutas[] = $nuevaRuta;

        return $nuevaRuta;
    }

    /**
     * Actualiza una ruta por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->rutas as $indice => $ruta) {
            if ($ruta["id"] === $id) {
                $this->rutas[$indice] = [
                    "id" => $id,
                    "nombre" => trim(
                        (string) $datos["nombre"]
                    ),
                    "zona" => trim(
                        (string) $datos["zona"]
                    ),
                    "fecha" => trim(
                        (string) $datos["fecha"]
                    ),
                    "horaInicio" => trim(
                        (string) $datos["horaInicio"]
                    ),
                    "horaFin" => trim(
                        (string) $datos["horaFin"]
                    ),
                    "estado" => trim(
                        (string) $datos["estado"]
                    )
                ];

                return $this->rutas[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina una ruta por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->rutas as $indice => $ruta) {
            if ($ruta["id"] === $id) {
                unset($this->rutas[$indice]);

                $this->rutas = array_values($this->rutas);

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
        $ids = array_column($this->rutas, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}
