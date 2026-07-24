<?php

require_once __DIR__ . "/../models/contenedor.php";

class ContenedorController
{
    private Contenedor $modelo;

    public function __construct()
    {
        $this->modelo = new Contenedor();
    }

    public function listar(): array
    {
        return [
            "error" => false,
            "codigo" => 200,
            "contenedores" => $this->modelo->obtenerTodos()
        ];
    }

    public function buscar(int $id): array
    {
        $contenedor = $this->modelo->obtenerPorId($id);

        if ($contenedor === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Contenedor no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "contenedor" => $contenedor
        ];
    }

    public function crear(array $datos): array
    {
        $camposRequeridos = [
            "codigo",
            "direccion",
            "longitud",
            "latitud",
            "estado",
            "capacidadMaxima",
            "capacidadActual"
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
        $datos["direccion"] = trim((string) $datos["direccion"]);
        $datos["estado"] = trim((string) $datos["estado"]);

        if ($datos["codigo"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El código del contenedor no puede estar vacío"
            ];
        }

        if ($datos["direccion"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La dirección del contenedor no puede estar vacía"
            ];
        }

        if (
            !is_numeric($datos["latitud"]) ||
            !is_numeric($datos["longitud"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud y la longitud deben ser valores numéricos"
            ];
        }

        if (
            $datos["latitud"] < -90 ||
            $datos["latitud"] > 90
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe estar entre -90 y 90"
            ];
        }

        if (
            $datos["longitud"] < -180 ||
            $datos["longitud"] > 180
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La longitud debe estar entre -180 y 180"
            ];
        }

        if (
            !is_numeric($datos["capacidadMaxima"]) ||
            !is_numeric($datos["capacidadActual"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "Las capacidades deben ser valores numéricos"
            ];
        }

        if ($datos["capacidadMaxima"] <= 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad máxima debe ser mayor que cero"
            ];
        }

        if ($datos["capacidadActual"] < 0) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad actual no puede ser negativa"
            ];
        }

        if ($datos["capacidadActual"] > $datos["capacidadMaxima"]) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad actual no puede superar la capacidad máxima"
            ];
        }

        $estadosPermitidos = [
            "Activo",
            "Inactivo",
            "Lleno",
            "En mantenimiento"
        ];

        if (!in_array($datos["estado"], $estadosPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El estado ingresado no es válido"
            ];
        }

        if (
            method_exists($this->modelo, "existeCodigo") &&
            $this->modelo->existeCodigo($datos["codigo"])
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un contenedor con ese código"
            ];
        }

        $contenedor = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Contenedor creado correctamente",
            "contenedor" => $contenedor
        ];
    }

    public function actualizar(int $id, array $datos): array
    {
        $contenedorExistente = $this->modelo->obtenerPorId($id);

        if ($contenedorExistente === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Contenedor no encontrado"
            ];
        }

        $datosActualizados = array_merge($contenedorExistente, $datos);

        if (isset($datosActualizados["codigo"])) {
            $datosActualizados["codigo"] =
                trim((string) $datosActualizados["codigo"]);

            if ($datosActualizados["codigo"] === "") {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El código del contenedor no puede estar vacío"
                ];
            }
        }

        if (isset($datosActualizados["direccion"])) {
            $datosActualizados["direccion"] =
                trim((string) $datosActualizados["direccion"]);

            if ($datosActualizados["direccion"] === "") {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "La dirección del contenedor no puede estar vacía"
                ];
            }
        }

        if (
            !isset($datosActualizados["latitud"]) ||
            !is_numeric($datosActualizados["latitud"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe ser un valor numérico"
            ];
        }

        if (
            $datosActualizados["latitud"] < -90 ||
            $datosActualizados["latitud"] > 90
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe estar entre -90 y 90"
            ];
        }

        if (
            !isset($datosActualizados["longitud"]) ||
            !is_numeric($datosActualizados["longitud"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La longitud debe ser un valor numérico"
            ];
        }

        if (
            $datosActualizados["longitud"] < -180 ||
            $datosActualizados["longitud"] > 180
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La longitud debe estar entre -180 y 180"
            ];
        }

        if (
            !isset($datosActualizados["capacidadMaxima"]) ||
            !is_numeric($datosActualizados["capacidadMaxima"]) ||
            $datosActualizados["capacidadMaxima"] <= 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad máxima debe ser un número mayor que cero"
            ];
        }

        if (
            !isset($datosActualizados["capacidadActual"]) ||
            !is_numeric($datosActualizados["capacidadActual"]) ||
            $datosActualizados["capacidadActual"] < 0
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad actual debe ser un número mayor o igual que cero"
            ];
        }

        if (
            $datosActualizados["capacidadActual"] >
            $datosActualizados["capacidadMaxima"]
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad actual no puede superar la capacidad máxima"
            ];
        }

        $estadosPermitidos = [
            "Activo",
            "Inactivo",
            "Lleno",
            "En mantenimiento"
        ];

        if (
            !isset($datosActualizados["estado"]) ||
            !in_array(
                $datosActualizados["estado"],
                $estadosPermitidos,
                true
            )
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El estado ingresado no es válido"
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
                "mensaje" => "Ya existe otro contenedor con ese código"
            ];
        }

        $contenedor = $this->modelo->actualizar(
            $id,
            $datosActualizados
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Contenedor actualizado correctamente",
            "contenedor" => $contenedor
        ];
    }

    public function eliminar(int $id): array
    {
        $contenedor = $this->modelo->obtenerPorId($id);

        if ($contenedor === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Contenedor no encontrado"
            ];
        }

        $this->modelo->eliminar($id);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Contenedor eliminado correctamente"
        ];
    }
}