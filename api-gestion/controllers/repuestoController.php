<?php

require_once __DIR__ . "/../models/repuesto.php";

class RepuestoController
{
    private Repuesto $modelo;

    public function __construct()
    {
        $this->modelo = new Repuesto();
    }

    public function listar(): array
    {
        return $this->modelo->obtenerTodos();
    }

    public function buscar(int $id): ?array
    {
        return $this->modelo->obtenerPorId($id);
    }

    public function crear(array $datos): array
    {
        return $this->modelo->crear($datos);
    }

    public function actualizar(int $id, array $datos): ?array
    {
        return $this->modelo->actualizar($id, $datos);
    }

    public function eliminar(int $id): bool
    {
        return $this->modelo->eliminar($id);
    }
}
