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
        return [
            "error" => false,
            "codigo" => 200,
            "repuestos" => $this->modelo->obtenerTodos()
        ];
    }

    public function buscar(int $id): array
    {
        $repuesto = $this->modelo->obtenerPorId($id);

        if ($repuesto === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Repuesto no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "repuesto" => $repuesto
        ];
    }

    public function crear(array $datos): array
    {
        $camposRequeridos = [
            "codigo",
            "modelo",
            "stock",
            "stockMinimo",
            "precio",
            "descripcion"
        ];

        foreach ($camposRequeridos as $campo) {
            if (
                !array_key_exists($campo, $datos) ||
                $datos[$campo] === "" ||
                $datos[$campo] === null
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El campo '$campo' es obligatorio"
                ];
            }
        }

        $datos["codigo"] = trim((string) $datos["codigo"]);
        $datos["modelo"] = trim((string) $datos["modelo"]);
        $datos["descripcion"] = trim((string) $datos["descripcion"]);

        if ($datos["codigo"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El código del repuesto no puede estar vacío"
            ];
        }

        if ($datos["modelo"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El modelo del repuesto no puede estar vacío"
            ];
        }

        if ($datos["descripcion"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La descripción del repuesto no puede estar vacía"
            ];
        }

        if (!is_numeric($datos["stock"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock debe ser un valor numérico"
            ];
        }

        if ((int) $datos["stock"] != $datos["stock"] || $datos["stock"] < 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock debe ser un número entero mayor o igual que cero"
            ];
        }

        if (!is_numeric($datos["stockMinimo"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock mínimo debe ser un valor numérico"
            ];
        }

        if (
            (int) $datos["stockMinimo"] != $datos["stockMinimo"] ||
            $datos["stockMinimo"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock mínimo debe ser un número entero mayor o igual que cero"
            ];
        }

        if (!is_numeric($datos["precio"]) || $datos["precio"] < 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El precio debe ser un número mayor o igual que cero"
            ];
        }

        if (
            method_exists($this->modelo, "existeCodigo") &&
            $this->modelo->existeCodigo($datos["codigo"])
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un repuesto con ese código"
            ];
        }

        $repuesto = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Repuesto creado correctamente",
            "repuesto" => $repuesto
        ];
    }

    public function actualizar(int $id, array $datos): array
    {
        $repuestoExistente = $this->modelo->obtenerPorId($id);

        if ($repuestoExistente === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Repuesto no encontrado"
            ];
        }

        $datosActualizados = array_merge($repuestoExistente, $datos);

        if (isset($datosActualizados["codigo"])) {
            $datosActualizados["codigo"] =
                trim((string) $datosActualizados["codigo"]);

            if ($datosActualizados["codigo"] === "") {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El código del repuesto no puede estar vacío"
                ];
            }
        }

        if (isset($datosActualizados["modelo"])) {
            $datosActualizados["modelo"] =
                trim((string) $datosActualizados["modelo"]);

            if ($datosActualizados["modelo"] === "") {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El modelo del repuesto no puede estar vacío"
                ];
            }
        }

        if (isset($datosActualizados["descripcion"])) {
            $datosActualizados["descripcion"] =
                trim((string) $datosActualizados["descripcion"]);

            if ($datosActualizados["descripcion"] === "") {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "La descripción del repuesto no puede estar vacía"
                ];
            }
        }

        if (
            !isset($datosActualizados["stock"]) ||
            !is_numeric($datosActualizados["stock"]) ||
            (int) $datosActualizados["stock"] != $datosActualizados["stock"] ||
            $datosActualizados["stock"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock debe ser un número entero mayor o igual que cero"
            ];
        }

        if (
            !isset($datosActualizados["stockMinimo"]) ||
            !is_numeric($datosActualizados["stockMinimo"]) ||
            (int) $datosActualizados["stockMinimo"] !=
                $datosActualizados["stockMinimo"] ||
            $datosActualizados["stockMinimo"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El stock mínimo debe ser un número entero mayor o igual que cero"
            ];
        }

        if (
            !isset($datosActualizados["precio"]) ||
            !is_numeric($datosActualizados["precio"]) ||
            $datosActualizados["precio"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El precio debe ser un número mayor o igual que cero"
            ];
        }

        if (
            isset($datos["codigo"]) &&
            method_exists($this->modelo, "existeCodigo") &&
            $this->modelo->existeCodigo($datosActualizados["codigo"], $id)
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe otro repuesto con ese código"
            ];
        }

        $repuesto = $this->modelo->actualizar(
            $id,
            $datosActualizados
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Repuesto actualizado correctamente",
            "repuesto" => $repuesto
        ];
    }

    public function eliminar(int $id): array
    {
        $repuesto = $this->modelo->obtenerPorId($id);

        if ($repuesto === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Repuesto no encontrado"
            ];
        }

        $this->modelo->eliminar($id);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Repuesto eliminado correctamente"
        ];
    }

    public function listarStockBajo(): array
    {
        if (!method_exists($this->modelo, "obtenerStockBajo")) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "La consulta de stock bajo todavía no está implementada en el modelo"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "repuestos" => $this->modelo->obtenerStockBajo()
        ];
    }

    public function ingresarStock(int $id, array $datos): array
    {
        if (
            !isset($datos["cantidad"]) ||
            !is_numeric($datos["cantidad"]) ||
            (int) $datos["cantidad"] != $datos["cantidad"] ||
            $datos["cantidad"] <= 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La cantidad debe ser un número entero mayor que cero"
            ];
        }

        if ($this->modelo->obtenerPorId($id) === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Repuesto no encontrado"
            ];
        }

        if (!method_exists($this->modelo, "ingresarStock")) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "El ingreso de stock todavía no está implementado en el modelo"
            ];
        }

        $repuesto = $this->modelo->ingresarStock(
            $id,
            (int) $datos["cantidad"]
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Stock ingresado correctamente",
            "repuesto" => $repuesto
        ];
    }

    public function retirarStock(int $id, array $datos): array
    {
        if (
            !isset($datos["cantidad"]) ||
            !is_numeric($datos["cantidad"]) ||
            (int) $datos["cantidad"] != $datos["cantidad"] ||
            $datos["cantidad"] <= 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La cantidad debe ser un número entero mayor que cero"
            ];
        }

        $repuesto = $this->modelo->obtenerPorId($id);

        if ($repuesto === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Repuesto no encontrado"
            ];
        }

        if ((int) $datos["cantidad"] > $repuesto["stock"]) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "No hay stock suficiente para realizar el retiro"
            ];
        }

        if (!method_exists($this->modelo, "retirarStock")) {
            return [
                "error" => true,
                "codigo" => 500,
                "mensaje" => "El retiro de stock todavía no está implementado en el modelo"
            ];
        }

        $repuestoActualizado = $this->modelo->retirarStock(
            $id,
            (int) $datos["cantidad"]
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Stock retirado correctamente",
            "repuesto" => $repuestoActualizado
        ];
    }
}