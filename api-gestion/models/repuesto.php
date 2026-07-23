<?php

class Repuesto
{
    // Simulamos una base de datos utilizando un arreglo
    private array $repuestos = [
        [
            "id" => 1,
            "codigo" => 165485,
            "modelo" => "EN 840",
            "stock" => 535,
            "precio" => 1500.0,
            "descripcion" => "Pasadores de Bisagra y Fijación de Tapa",
        ],
        [
            "id" => 2,
            "codigo" => 105354,
            "modelo" => "SULO MGB 770",
            "stock" => 500,
            "precio" => 2000.0,
            "descripcion" => "Ruedas y Ejes de Rotación",
        ]
    ];

    // Devuelve todos los repuestos
    public function obtenerTodos(): array
    {
        return $this->repuestos;
    }

    // Busca un repuesto por su ID
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->repuestos as $repuesto) {
            if ($repuesto["id"] === $id) {
                return $repuesto;
            }
        }

        return null;
    }

    // Crea un nuevo repuesto
    public function crear(array $datos): array
    {
        // Generamos un nuevo ID
        $ids = array_column($this->repuestos, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        // Creamos el repuesto nuevo
        $nuevoRepuesto = [
            "id" => $nuevoId,
            "codigo" => (int) $datos["codigo"],
            "modelo" => $datos["modelo"],
            "stock" => (int) $datos["stock"],
            "precio" => (float) $datos["precio"],
            "descripcion" => $datos["descripcion"]
        ];

        // La agregamos al arreglo
        $this->repuestos[] = $nuevoRepuesto;

        // Devolvemos el repuesto creado
        return $nuevoRepuesto;
    }

    // Actualiza un repuesto por su ID
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                $this->repuestos[$indice] = [
                    "id" => $id,
                    "codigo" => (int) $datos["codigo"],
                    "modelo" => $datos["modelo"],
                    "stock" => (int) $datos["stock"],
                    "precio" => (float) $datos["precio"],
                    "descripcion" => $datos["descripcion"],
                ];

                return $this->repuestos[$indice];
            }
        }

        return null;
    }

    // Elimina un repuesto por su ID
    public function eliminar(int $id): bool
    {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                unset($this->repuestos[$indice]);

                $this->repuestos = array_values($this->repuestos);

                return true;
            }
        }

        return false;
    }
}
