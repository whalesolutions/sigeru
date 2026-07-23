<?php

class CentroAcopio
{
    // Simulamos una base de datos utilizando un arreglo
    private array $centroAcopios = [
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
        ],
        [
            "id" => 2,
            "nombre" => "Ecocentro Buceo",
            "telefono" => "26041025",
            "direccion" => "Av. Tomas Basañez 1212, 11300 Montevideo, Departamento de Montevideo",
            "longitud" => -75.7536,
            "latitud" => -52.7292,
            "correo" => "elromorelli22@hotmail.com",
            "estado" => "Activo",
            "capacidadMaxima" => 7500,
        ]
    ];

    // Devuelve todos los centroacopios
    public function obtenerTodos(): array
    {
        return $this->centroAcopios;
    }

    // Busca un centroacopio por su ID
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->centroAcopios as $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                return $centroAcopio;
            }
        }

        return null;
    }

    // Crea un nuevo Centro de Acopio
    public function crear(array $datos): array
    {
        // Generamos un nuevo ID
        $ids = array_column($this->centroAcopios, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        // Creamos el centroAcopio nuevo
        $nuevoCentroAcopio = [
            "id" => $nuevoId,
            "nombre" => $datos["nombre"],
            "telefono" => $datos["telefono"],
            "direccion" => $datos["direccion"],
            "longitud" => (float) $datos["longitud"],
            "latitud" => (float) $datos["latitud"],
            "correo" => $datos["correo"],
            "capacidadMaxima" => (int) $datos["capacidadMaxima"],

            // El estado siempre comienza como "Activo"
            "estado" => "Activo",
        ];

        // La agregamos al arreglo
        $this->centroAcopios[] = $nuevoCentroAcopio;

        // Devolvemos el Centro de Acopio creado
        return $nuevoCentroAcopio;
    }

    // Actualiza un centro de acopio por su ID
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->centroAcopios as $indice => $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                $this->centroAcopios[$indice] = [
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

                return $this->centroAcopios[$indice];
            }
        }

        return null;
    }

    // Elimina un centro de acopio por su ID
    public function eliminar(int $id): bool
    {
        foreach ($this->centroAcopios as $indice => $centroAcopio) {
            if ($centroAcopio["id"] === $id) {
                unset($this->centroAcopios[$indice]);

                $this->centroAcopios = array_values($this->centroAcopios);

                return true;
            }
        }

        return false;
    }
}
