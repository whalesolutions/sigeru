<?php

require_once __DIR__ . "/../models/centroAcopio.php";

class CentroAcopioController
{
    private CentroAcopio $modelo;

    public function __construct()
    {
        $this->modelo = new CentroAcopio();
    }

    public function listar(): array
    {
        return [
            "error" => false,
            "codigo" => 200,
            "centrosAcopio" => $this->modelo->obtenerTodos()
        ];
    }

    public function buscar(int $id): array
    {
        $centro = $this->modelo->obtenerPorId($id);

        if ($centro === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Centro de acopio no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "centroAcopio" => $centro
        ];
    }

    public function crear(array $datos): array
    {
        $camposRequeridos = [
            "nombre",
            "telefono",
            "direccion",
            "longitud",
            "latitud",
            "correo",
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

        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El correo ingresado no es válido"
            ];
        }

        if (!is_numeric($datos["latitud"]) || !is_numeric($datos["longitud"])) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud y la longitud deben ser valores numéricos"
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
            "Completo",
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
            method_exists($this->modelo, "existeCorreo") &&
            $this->modelo->existeCorreo($datos["correo"])
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un centro de acopio con ese correo"
            ];
        }

        $centro = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Centro de acopio creado correctamente",
            "centroAcopio" => $centro
        ];
    }

    public function actualizar(int $id, array $datos): array
    {
        $centroExistente = $this->modelo->obtenerPorId($id);

        if ($centroExistente === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Centro de acopio no encontrado"
            ];
        }

        $datosActualizados = array_merge($centroExistente, $datos);

        if (
            isset($datosActualizados["correo"]) &&
            !filter_var($datosActualizados["correo"], FILTER_VALIDATE_EMAIL)
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El correo ingresado no es válido"
            ];
        }

        if (
            isset($datosActualizados["latitud"]) &&
            !is_numeric($datosActualizados["latitud"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe ser numérica"
            ];
        }

        if (
            isset($datosActualizados["longitud"]) &&
            !is_numeric($datosActualizados["longitud"])
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La longitud debe ser numérica"
            ];
        }

        if (
            isset($datosActualizados["capacidadMaxima"]) &&
            (!is_numeric($datosActualizados["capacidadMaxima"]) ||
             $datosActualizados["capacidadMaxima"] <= 0)
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La capacidad máxima debe ser un número mayor que cero"
            ];
        }

        if (
            isset($datosActualizados["capacidadActual"]) &&
            (!is_numeric($datosActualizados["capacidadActual"]) ||
             $datosActualizados["capacidadActual"] < 0)
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
            "Completo",
            "En mantenimiento"
        ];

        if (
            isset($datosActualizados["estado"]) &&
            !in_array($datosActualizados["estado"], $estadosPermitidos, true)
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El estado ingresado no es válido"
            ];
        }

        $centro = $this->modelo->actualizar($id, $datos);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Centro de acopio actualizado correctamente",
            "centroAcopio" => $centro
        ];
    }

    public function eliminar(int $id): array
    {
        $centro = $this->modelo->obtenerPorId($id);

        if ($centro === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Centro de acopio no encontrado"
            ];
        }

        $this->modelo->eliminar($id);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Centro de acopio eliminado correctamente"
        ];
    }
}