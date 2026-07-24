<?php

class Repuesto
{
    // Simulación temporal de una base de datos mediante un arreglo.
    private array $repuestos = [
        [
            "id" => 1,
            "codigo" => "165485",
            "modelo" => "EN 840",
            "stock" => 535,
            "stockMinimo" => 100,
            "precio" => 1500.0,
            "descripcion" => "Pasadores de bisagra y fijación de tapa"
        ],
        [
            "id" => 2,
            "codigo" => "105354",
            "modelo" => "SULO MGB 770",
            "stock" => 500,
            "stockMinimo" => 80,
            "precio" => 2000.0,
            "descripcion" => "Ruedas y ejes de rotación"
        ]
    ];

    /**
     * Devuelve todos los repuestos.
     */
    public function obtenerTodos(): array
    {
        return $this->repuestos;
    }

    /**
     * Busca un repuesto por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->repuestos as $repuesto) {
            if ($repuesto["id"] === $id) {
                return $repuesto;
            }
        }

        return null;
    }

    /**
     * Comprueba si existe un repuesto con el código indicado.
     *
     * El parámetro $idExcluir permite ignorar el propio registro
     * durante una actualización.
     */
    public function existeCodigo(
        string $codigo,
        ?int $idExcluir = null
    ): bool {
        $codigoBuscado = strtoupper(trim($codigo));

        foreach ($this->repuestos as $repuesto) {
            $mismoCodigo =
                strtoupper((string) $repuesto["codigo"]) === $codigoBuscado;

            $esOtroRepuesto =
                $idExcluir === null ||
                $repuesto["id"] !== $idExcluir;

            if ($mismoCodigo && $esOtroRepuesto) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo repuesto.
     */
    public function crear(array $datos): array
    {
        $nuevoId = $this->generarNuevoId();

        $nuevoRepuesto = [
            "id" => $nuevoId,
            "codigo" => strtoupper(
                trim((string) $datos["codigo"])
            ),
            "modelo" => trim((string) $datos["modelo"]),
            "stock" => (int) $datos["stock"],
            "stockMinimo" => (int) $datos["stockMinimo"],
            "precio" => (float) $datos["precio"],
            "descripcion" => trim(
                (string) $datos["descripcion"]
            )
        ];

        $this->repuestos[] = $nuevoRepuesto;

        return $nuevoRepuesto;
    }

    /**
     * Actualiza un repuesto por su ID.
     */
    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                $this->repuestos[$indice] = [
                    "id" => $id,
                    "codigo" => strtoupper(
                        trim((string) $datos["codigo"])
                    ),
                    "modelo" => trim(
                        (string) $datos["modelo"]
                    ),
                    "stock" => (int) $datos["stock"],
                    "stockMinimo" =>
                        (int) $datos["stockMinimo"],
                    "precio" => (float) $datos["precio"],
                    "descripcion" => trim(
                        (string) $datos["descripcion"]
                    )
                ];

                return $this->repuestos[$indice];
            }
        }

        return null;
    }

    /**
     * Devuelve los repuestos cuyo stock actual es menor o igual
     * al stock mínimo establecido.
     */
    public function obtenerStockBajo(): array
    {
        return array_values(
            array_filter(
                $this->repuestos,
                function (array $repuesto): bool {
                    return $repuesto["stock"] <=
                        $repuesto["stockMinimo"];
                }
            )
        );
    }

    /**
     * Incrementa el stock de un repuesto.
     */
    public function ingresarStock(
        int $id,
        int $cantidad
    ): ?array {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                $this->repuestos[$indice]["stock"] += $cantidad;

                return $this->repuestos[$indice];
            }
        }

        return null;
    }

    /**
     * Disminuye el stock de un repuesto.
     *
     * El controlador valida previamente que exista stock suficiente.
     */
    public function retirarStock(
        int $id,
        int $cantidad
    ): ?array {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                if ($cantidad > $repuesto["stock"]) {
                    return null;
                }

                $this->repuestos[$indice]["stock"] -= $cantidad;

                return $this->repuestos[$indice];
            }
        }

        return null;
    }

    /**
     * Elimina un repuesto por su ID.
     */
    public function eliminar(int $id): bool
    {
        foreach ($this->repuestos as $indice => $repuesto) {
            if ($repuesto["id"] === $id) {
                unset($this->repuestos[$indice]);

                $this->repuestos =
                    array_values($this->repuestos);

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
        $ids = array_column($this->repuestos, "id");

        return empty($ids) ? 1 : max($ids) + 1;
    }
}