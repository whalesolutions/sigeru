<?php

class Vertedero
{
    // Simulamos una base de datos utilizando un arreglo
    private array $vertederos = [
        [
            "id" => 1,
            "nombre" => "DFR. Felipe Cardozo",
            "telefono" => "26984353",
            "direccion" => "Camino Felipe Cardozo 12100, Montevideo, Departamento de Montevideo",
            "longitud" => -57.6356,
            "latitud" => -25.2926,
            "correo" => "nachomenendezf@gmail.com",
            "estado" => "Inactivo",
            "capacidadMaxima" => 1000,
        ],
    ];

    // Devuelve todos los vertederos
    public function obtenerTodos(): array
    {
        return $this->vertederos;
    }

    // Busca un vertedero por su ID
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->vertederos as $vertedero) {
            if ($vertedero["id"] === $id) {
                return $vertedero;
            }
        }

        return null;
    }

    // Crea un nuevo vertedero
    public function crear(array $datos): array
    {
        // Generamos un nuevo ID
        $ids = array_column($this->vertederos, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        // Creamos el vertedero nuevo
        $nuevoVertedero = [
            "id" => $nuevoId,
            "nombre" => $datos["nombre"],
            "telefono" => $datos["telefono"],
            "direccion" => $datos["direccion"],
            "longitud" => (float) $datos["longitud"],
            "latitud" => (float) $datos["latitud"],
            "correo" => $datos["correo"],
            "capacidadMaxima" => (int) $datos["capacidadMaxima"],

            // El estado siempre comienza como "Activo"
            "estado" => "Activo"
        ];

        // La agregamos al arreglo
        $this->vertederos[] = $nuevoVertedero;

        // Devolvemos el vertedero creado
        return $nuevoVertedero;
    }

    // Actualiza un vertedero por su ID
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->vertederos as $indice => $vertedero) {
            if ($vertedero["id"] === $id) {
                $this->vertederos[$indice] = [
                    "id" => $id,
                    "nombre" => $datos["nombre"],
                    "telefono" => $datos["telefono"],
                    "direccion" => $datos["direccion"],
                    "longitud" => (float) $datos["longitud"],
                    "latitud" => (float) $datos["latitud"],
                    "correo" => $datos["correo"],
                    "estado" => $datos["estado"],
                    "capacidadMaxima" => (int) $datos["capacidadMaxima"],
                ];

                return $this->vertederos[$indice];
            }
        }

        return null;
    }

    // Elimina un vertedero por su ID
    public function eliminar(int $id): bool
    {
        foreach ($this->vertederos as $indice => $vertedero) {
            if ($vertedero["id"] === $id) {
                unset($this->vertederos[$indice]);

                $this->vertederos = array_values($this->vertederos);

                return true;
            }
        }

        return false;
    }
}
