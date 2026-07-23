<?php

require_once __DIR__ . "/../models/incidencia.php";

class IncidenciaController
{
    private Incidencia $modelo;

    public function __construct()
    {
        $this->modelo = new Incidencia();
    }

    public function listar(): array
    {
        return $this->modelo->obtenerTodas();
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