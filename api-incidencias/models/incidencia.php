<?php

class Incidencia
{
    // Simulamos una base de datos utilizando un arreglo
    private array $incidencias = [
        [
            "id" => 1,
            "titulo" => "Contenedor desbordado",
            "descripcion" => "El contenedor está lleno desde hace tres días.",
            "tipo" => "Desborde",
            "estado" => "Abierto"
        ],
        [
            "id" => 2,
            "titulo" => "Contenedor dañado",
            "descripcion" => "La tapa del contenedor está rota.",
            "tipo" => "Rotura",
            "estado" => "En proceso"
        ]
    ];

    // Devuelve todas las incidencias
    public function obtenerTodas(): array       //Obtiene todas las incidencias
    {
        return $this->incidencias;
    }

    // Busca una incidencia por su ID
    public function obtenerPorId(int $id): ?array       //Obtiene la incidencia que solicite el usuario
    {
        foreach ($this->incidencias as $incidencia) {
            if ($incidencia["id"] === $id) {
                return $incidencia;
            }
        }

        return null;
    }

    // Crea una nueva incidencia
    public function crear(array $datos): array      //Crea una incidencia
    {
        // Generamos un nuevo ID
        $nuevoId = count($this->incidencias) + 1;

        // Creamos la nueva incidencia
        $nuevaIncidencia = [
            "id" => $nuevoId,
            "titulo" => $datos["titulo"],
            "descripcion" => $datos["descripcion"],
            "tipo" => $datos["tipo"],

            // El estado siempre comienza como "Abierto"
            "estado" => "Abierto"
        ];

        // La agregamos al arreglo
        $this->incidencias[] = $nuevaIncidencia;

        // Devolvemos la incidencia creada
        return $nuevaIncidencia;
    }
    public function actualizar(int $id, array $datos): ?array
{
    foreach ($this->incidencias as $indice => $incidencia) {
        if ($incidencia["id"] === $id) {
            $this->incidencias[$indice] = [
                "id" => $id,
                "titulo" => $datos["titulo"],
                "descripcion" => $datos["descripcion"],
                "tipo" => $datos["tipo"],
                "estado" => $datos["estado"]
            ];

            return $this->incidencias[$indice];
        }
    }

    return null;
}
public function eliminar(int $id): bool
{
    foreach ($this->incidencias as $indice => $incidencia) {
        if ($incidencia["id"] === $id) {
            unset($this->incidencias[$indice]);
            return true;
        }
    }

    return false;
}
}