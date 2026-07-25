<?php

require_once __DIR__ . "/../models/vertedero.php";

class VertederoController
{
    private Vertedero $modelo;

    public function __construct()
    {
        $this->modelo = new Vertedero();
    }

    public function listar(): array
    {
        return [
            "error" => false,
            "codigo" => 200,
            "vertederos" => $this->modelo->obtenerTodos()
        ];
    }

    public function buscar(int $id): array
    {
        $vertedero = $this->modelo->obtenerPorId($id);

        if ($vertedero === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Vertedero no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "vertedero" => $vertedero
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

        $datos["nombre"] = trim((string) $datos["nombre"]);
        $datos["telefono"] = trim((string) $datos["telefono"]);
        $datos["direccion"] = trim((string) $datos["direccion"]);
        $datos["correo"] = trim((string) $datos["correo"]);
        $datos["estado"] = trim((string) $datos["estado"]);

        if ($datos["nombre"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El nombre del vertedero no puede estar vacío"
            ];
        }

        if ($datos["telefono"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El teléfono del vertedero no puede estar vacío"
            ];
        }

        if ($datos["direccion"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La dirección del vertedero no puede estar vacía"
            ];
        }

        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El correo ingresado no es válido"
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

        if ($datos["latitud"] < -90 || $datos["latitud"] > 90) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe estar entre -90 y 90"
            ];
        }

        if ($datos["longitud"] < -180 || $datos["longitud"] > 180) {
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
                "mensaje" => "Ya existe un vertedero con ese correo"
            ];
        }

        $vertedero = $this->modelo->crear($datos);

        return [
            "error" => false,
            "codigo" => 201,
            "mensaje" => "Vertedero creado correctamente",
            "vertedero" => $vertedero
        ];
    }

    public function actualizar(int $id, array $datos): array
    {
        $vertederoExistente = $this->modelo->obtenerPorId($id);

        if ($vertederoExistente === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Vertedero no encontrado"
            ];
        }

        $datosActualizados = array_merge($vertederoExistente, $datos);

        $datosActualizados["nombre"] =
            trim((string) $datosActualizados["nombre"]);

        $datosActualizados["telefono"] =
            trim((string) $datosActualizados["telefono"]);

        $datosActualizados["direccion"] =
            trim((string) $datosActualizados["direccion"]);

        $datosActualizados["correo"] =
            trim((string) $datosActualizados["correo"]);

        $datosActualizados["estado"] =
            trim((string) $datosActualizados["estado"]);

        if ($datosActualizados["nombre"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El nombre del vertedero no puede estar vacío"
            ];
        }

        if ($datosActualizados["telefono"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El teléfono del vertedero no puede estar vacío"
            ];
        }

        if ($datosActualizados["direccion"] === "") {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La dirección del vertedero no puede estar vacía"
            ];
        }

        if (!filter_var($datosActualizados["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El correo ingresado no es válido"
            ];
        }

        if (
            !is_numeric($datosActualizados["latitud"]) ||
            $datosActualizados["latitud"] < -90 ||
            $datosActualizados["latitud"] > 90
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La latitud debe ser un número entre -90 y 90"
            ];
        }

        if (
            !is_numeric($datosActualizados["longitud"]) ||
            $datosActualizados["longitud"] < -180 ||
            $datosActualizados["longitud"] > 180
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La longitud debe ser un número entre -180 y 180"
            ];
        }

        if (
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
            "Completo",
            "En mantenimiento"
        ];

        if (
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
            isset($datos["correo"]) &&
            method_exists($this->modelo, "existeCorreo") &&
            $this->modelo->existeCorreo(
                $datosActualizados["correo"],
                $id
            )
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe otro vertedero con ese correo"
            ];
        }

        $vertedero = $this->modelo->actualizar(
            $id,
            $datosActualizados
        );

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Vertedero actualizado correctamente",
            "vertedero" => $vertedero
        ];
    }

    public function eliminar(int $id): array
    {
        $vertedero = $this->modelo->obtenerPorId($id);

        if ($vertedero === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Vertedero no encontrado"
            ];
        }

        $this->modelo->eliminar($id);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Vertedero eliminado correctamente"
        ];
    }
}