<?php

// Carga el archivo donde está definida la clase Usuario
require_once __DIR__ . "/../models/usuario.php";

// Define el controlador encargado de todas las operaciones relacionadas con usuarios.
class UsuarioController
{
    private Usuario $modelo;

    // Inicializa el modelo para poder utilizar sus métodos
    public function __construct()
    {
        $this->modelo = new Usuario();
    }

    // Pide al modelo todos los usuarios registrados.
    public function listar(): array
    {
        return $this->modelo->obtenerTodos();
    }

    // Busca un usuario mediante su ID.
    public function buscar(int $id): ?array
    {
        return $this->modelo->obtenerPorId($id);
    }
    // Campos para crear usuario
    public function crear(array $datos): array
    {
        $camposObligatorios = [
            "nombre",
            "apellido",
            "documento",
            "correo",
            "contrasena",
            "rol"
        ];

        // Recorre todos los campos uno por uno
        foreach ($camposObligatorios as $campo) {
            if (
                !isset($datos[$campo])
                || trim((string) $datos[$campo]) === ""
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El campo {$campo} es obligatorio"
                ];
            }
        }

        //Comprueba que el correo tenga formato válido.
        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El formato del correo no es válido"
            ];
        }

        // Define qué roles pueden existir.
        $rolesPermitidos = [
            "Administrador",
            "Operario",
            "Cuadrilla"
        ];

        $rol = trim($datos["rol"]);

        // Verifica que el rol ingresado sea válido
        if (!in_array($rol, $rolesPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El rol ingresado no es válido"
            ];
        }

        // Controla que la contraseña tenga mínimo 8 caracteres.
        if (strlen((string) $datos["contrasena"]) < 8) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La contraseña debe tener al menos 8 caracteres"
            ];
        }

        //Verifica que no haya alguien con ese documento
        if ($this->modelo->existeDocumento($datos["documento"])) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un usuario con ese documento"
            ];
        }

        // Verifica que no haya alguien con ese correo
        if ($this->modelo->existeCorreo($datos["correo"])) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe un usuario con ese correo"
            ];
        }

        $datos["rol"] = $rol;

        // Crea el usuario y devuelve la información registrada.
        return [
            "error" => false,
            "codigo" => 201,
            "usuario" => $this->modelo->crear($datos)
        ];
    }

    // Actualiza los datos de un usuario ya registrado.
    public function actualizarPerfil(
        int $id,
        array $datos
    ): array {
        $usuarioExistente = $this->modelo->obtenerPorId($id);

        if ($usuarioExistente === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado"
            ];
        }

        $camposObligatorios = [
            "nombre",
            "apellido",
            "documento",
            "correo"
        ];

        foreach ($camposObligatorios as $campo) {
            if (
                !isset($datos[$campo])
                || trim((string) $datos[$campo]) === ""
            ) {
                return [
                    "error" => true,
                    "codigo" => 400,
                    "mensaje" => "El campo {$campo} es obligatorio"
                ];
            }
        }

        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El formato del correo no es válido"
            ];
        }

        if (
            $this->modelo->existeDocumento(
                $datos["documento"],
                $id
            )
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe otro usuario con ese documento"
            ];
        }

        if (
            $this->modelo->existeCorreo(
                $datos["correo"],
                $id
            )
        ) {
            return [
                "error" => true,
                "codigo" => 409,
                "mensaje" => "Ya existe otro usuario con ese correo"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "usuario" => $this->modelo->actualizarPerfil(
                $id,
                $datos
            )
        ];
    }

    public function cambiarEstado(
        int $id,
        array $datos
    ): array {
        if (
            !isset($datos["estado"])
            || trim((string) $datos["estado"]) === ""
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El estado es obligatorio"
            ];
        }

        // Cambia el estado de un usuario
        $estadosPermitidos = [
            "Pendiente",
            "Activo",
            "Rechazado",
            "Inactivo"
        ];

        $estado = trim($datos["estado"]);

        if (!in_array($estado, $estadosPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El estado ingresado no es válido"
            ];
        }

        $usuario = $this->modelo->cambiarEstado($id, $estado);

        if ($usuario === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "usuario" => $usuario
        ];
    }

    // Modifica el rol asignado a un usuario.
    public function cambiarRol(
        int $id,
        array $datos
    ): array {
        if (
            !isset($datos["rol"])
            || trim((string) $datos["rol"]) === ""
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El rol es obligatorio"
            ];
        }

        $rolesPermitidos = [
            "Administrador",
            "Operario",
            "Cuadrilla"
        ];

        $rol = trim($datos["rol"]);

        if (!in_array($rol, $rolesPermitidos, true)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El rol ingresado no es válido"
            ];
        }

        $usuario = $this->modelo->cambiarRol($id, $rol);

        if ($usuario === null) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "usuario" => $usuario
        ];
    }

    // Actualiza la contraseña del usuario.
    public function cambiarContrasena(
        int $id,
        array $datos
    ): array {
        if (
            !isset($datos["contrasena"])
            || trim((string) $datos["contrasena"]) === ""
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La contraseña es obligatoria"
            ];
        }

        if (strlen((string) $datos["contrasena"]) < 8) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "La contraseña debe tener al menos 8 caracteres"
            ];
        }

        $actualizada = $this->modelo->cambiarContrasena(
            $id,
            $datos["contrasena"]
        );

        if (!$actualizada) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Contraseña actualizada correctamente"
        ];
    }

    public function login(array $datos): array
    {
        if (
            !isset($datos["correo"], $datos["contrasena"])
            || trim((string) $datos["correo"]) === ""
            || trim((string) $datos["contrasena"]) === ""
        ) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El correo y la contraseña son obligatorios"
            ];
        }

        if (!filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            return [
                "error" => true,
                "codigo" => 400,
                "mensaje" => "El formato del correo no es válido"
            ];
        }

        $usuario = $this->modelo->obtenerPorCorreo(
            $datos["correo"]
        );

        if (
            $usuario === null
            || !password_verify(
                $datos["contrasena"],
                $usuario["contrasena"]
            )
        ) {
            return [
                "error" => true,
                "codigo" => 401,
                "mensaje" => "Correo o contraseña incorrectos"
            ];
        }

        if ($usuario["estado"] === "Pendiente") {
            return [
                "error" => true,
                "codigo" => 403,
                "mensaje" => "La cuenta todavía está pendiente de aprobación"
            ];
        }

        if ($usuario["estado"] === "Rechazado") {
            return [
                "error" => true,
                "codigo" => 403,
                "mensaje" => "La solicitud de registro fue rechazada"
            ];
        }

        if ($usuario["estado"] === "Inactivo") {
            return [
                "error" => true,
                "codigo" => 403,
                "mensaje" => "La cuenta se encuentra inactiva"
            ];
        }

        // Elimina la contraseña antes de enviar la información del usuario
        unset($usuario["contrasena"]);

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Inicio de sesión correcto",
            "usuario" => $usuario
        ];
    }

    public function eliminar(int $id): array
    {
        $eliminado = $this->modelo->eliminar($id);

        if (!$eliminado) {
            return [
                "error" => true,
                "codigo" => 404,
                "mensaje" => "Usuario no encontrado"
            ];
        }

        return [
            "error" => false,
            "codigo" => 200,
            "mensaje" => "Usuario eliminado correctamente"
        ];
    }
}