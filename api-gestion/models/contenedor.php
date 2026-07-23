<?php

class Contenedor
{
    // Simulamos una base de datos utilizando un arreglo
    private array $contenedores = [
        [
            "id" => 1,
            "codigo" => "CH-001",
            "direccion" => "José Ellauri esq. Solano García",
            "longitud" => -37.7556,
            "latitud" => -73.2949,
            "estado" => "Inactivo",
            "capacidadMaxima" => 3200,
        ],
        [
            "id" => 2,
            "codigo" => "CH-002",
            "direccion" => "Av. Tomas Basañez 1212, 11300 Montevideo",
            "longitud" => -33.7686,
            "latitud" => -77.7682,
            "estado" => "Activo",
            "capacidadMaxima" => 800,
        ]
    ];

    // Devuelve todos los contenedores
    public function obtenerTodos(): array
    {
        return $this->contenedores;
    }

    // Busca un contenedor por su ID
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->contenedores as $contenedor) {
            if ($contenedor["id"] === $id) {
                return $contenedor;
            }
        }

        return null;
    }

    // Crea un nuevo contenedor
    public function crear(array $datos): array
    {
        // Generamos un nuevo ID
        $ids = array_column($this->contenedores, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        // Creamos el contenedor nuevo
        $nuevoContenedor = [

            "id" => $nuevoId,
            "codigo" => $datos["codigo"],
            "direccion" => $datos["direccion"],
            "longitud" => (float) $datos["longitud"],
            "latitud" => (float) $datos["latitud"],
            "capacidadMaxima" => (int) $datos["capacidadMaxima"],

            // El estado siempre comienza como "Activo"
            "estado" => "Activo"
        ];

        // La agregamos al arreglo
        $this->contenedores[] = $nuevoContenedor;

        // Devolvemos el contenedor creado
        return $nuevoContenedor;
    }

    // Actualiza un contenedor por su ID
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->contenedores as $indice => $contenedor) {
            if ($contenedor["id"] === $id) {
                $this->contenedores[$indice] = [
                    "id" => $id,
                    "codigo" => $datos["codigo"],
                    "direccion" => $datos["direccion"],
                    "longitud" => (float) $datos["longitud"],
                    "latitud" => (float) $datos["latitud"],
                    "estado" => $datos["estado"],
                    "capacidadMaxima" => (int) $datos["capacidadMaxima"],
                ];

                return $this->contenedores[$indice];
            }
        }

        return null;
    }

    // Elimina un contenedor por su ID
    public function eliminar(int $id): bool
    {
        foreach ($this->contenedores as $indice => $contenedor) {
            if ($contenedor["id"] === $id) {
                unset($this->contenedores[$indice]);

                $this->contenedores = array_values($this->contenedores);

                return true;
            }
        }

        return false;
    }
}
