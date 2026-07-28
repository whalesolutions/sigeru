<?php

class Usuario
{
    /**
     * Inicializa los usuarios simulados una sola vez
     * durante la sesión activa.
     */
    public function __construct()
    {
        if (!isset($_SESSION["usuarios"])) {
            $_SESSION["usuarios"] = [
                [
                    "id" => 1,
                    "nombre" => "Romario",
                    "apellido" => "Surita",
                    "documento" => "67152743",
                    "telefono" => "099111111",
                    "correo" => "romariosurita@gmail.com",
                    "contrasena" => password_hash(
                        "Romario123",
                        PASSWORD_DEFAULT
                    ),
                    "rol" => "Operario",
                    "estado" => "Inactivo"
                ],
                [
                    "id" => 2,
                    "nombre" => "Rodrigo",
                    "apellido" => "Morelli",
                    "documento" => "49383843",
                    "telefono" => "099222222",
                    "correo" => "rodrimorelli22@gmail.com",
                    "contrasena" => password_hash(
                        "Rodrigo123",
                        PASSWORD_DEFAULT
                    ),
                    "rol" => "Administrador",
                    "estado" => "Activo"
                ]
            ];
        }
    }

    /**
     * Devuelve todos los usuarios sin incluir sus contraseñas.
     */
    public function obtenerTodos(): array
    {
        return array_map(
            fn(array $usuario): array =>
            $this->ocultarContrasena($usuario),
            $_SESSION["usuarios"]
        );
    }

    /**
     * Busca un usuario por su ID.
     */
    public function obtenerPorId(int $id): ?array
    {
        foreach ($_SESSION["usuarios"] as $usuario) {
            if ((int) $usuario["id"] === $id) {
                return $this->ocultarContrasena($usuario);
            }
        }

        return null;
    }

    /**
     * Busca un usuario por correo.
     *
     * Se devuelve la contraseña cifrada porque el controlador
     * la necesita para validar el inicio de sesión.
     */
    public function obtenerPorCorreo(string $correo): ?array
    {
        $correoBuscado = strtolower(trim($correo));

        foreach ($_SESSION["usuarios"] as $usuario) {
            if (
                strtolower(trim($usuario["correo"])) ===
                $correoBuscado
            ) {
                return $usuario;
            }
        }

        return null;
    }

    /**
     * Comprueba si ya existe un usuario con ese documento.
     */
    public function existeDocumento(
        string $documento,
        ?int $idExcluido = null
    ): bool {
        $documentoBuscado = trim($documento);

        foreach ($_SESSION["usuarios"] as $usuario) {
            $mismoDocumento =
                trim($usuario["documento"]) === $documentoBuscado;

            $esOtroUsuario =
                $idExcluido === null ||
                (int) $usuario["id"] !== $idExcluido;

            if ($mismoDocumento && $esOtroUsuario) {
                return true;
            }
        }

        return false;
    }

    /**
     * Comprueba si ya existe un usuario con ese correo.
     */
    public function existeCorreo(
        string $correo,
        ?int $idExcluido = null
    ): bool {
        $correoBuscado = strtolower(trim($correo));

        foreach ($_SESSION["usuarios"] as $usuario) {
            $mismoCorreo =
                strtolower(trim($usuario["correo"])) ===
                $correoBuscado;

            $esOtroUsuario =
                $idExcluido === null ||
                (int) $usuario["id"] !== $idExcluido;

            if ($mismoCorreo && $esOtroUsuario) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crea un nuevo usuario.
     */
    public function crear(array $datos): array
    {
        $nuevoUsuario = [
            "id" => $this->generarNuevoId(),

            "nombre" => trim(
                (string) $datos["nombre"]
            ),

            "apellido" => trim(
                (string) $datos["apellido"]
            ),

            "documento" => trim(
                (string) $datos["documento"]
            ),

            "telefono" => trim(
                (string) $datos["telefono"]
            ),

            "correo" => strtolower(
                trim((string) $datos["correo"])
            ),

            "contrasena" => password_hash(
                (string) $datos["contrasena"],
                PASSWORD_DEFAULT
            ),

            "rol" => trim(
                (string) $datos["rol"]
            ),

            "estado" => "Activo"
        ];

        $_SESSION["usuarios"][] = $nuevoUsuario;

        return $this->ocultarContrasena($nuevoUsuario);
    }

    /**
     * Actualiza la información personal de un usuario.
     */
    public function actualizarPerfil(
        int $id,
        array $datos
    ): ?array {
        foreach (
            $_SESSION["usuarios"] as $indice => $usuario
        ) {
            if ((int) $usuario["id"] === $id) {
                $_SESSION["usuarios"][$indice]["nombre"] =
                    trim((string) $datos["nombre"]);

                $_SESSION["usuarios"][$indice]["apellido"] =
                    trim((string) $datos["apellido"]);

                $_SESSION["usuarios"][$indice]["documento"] =
                    trim((string) $datos["documento"]);

                $_SESSION["usuarios"][$indice]["correo"] =
                    strtolower(
                        trim((string) $datos["correo"])
                    );

                if (isset($datos["telefono"])) {
                    $_SESSION["usuarios"][$indice]["telefono"] =
                        trim((string) $datos["telefono"]);
                }

                return $this->ocultarContrasena(
                    $_SESSION["usuarios"][$indice]
                );
            }
        }

        return null;
    }

    /**
     * Cambia el estado de un usuario.
     */
    public function cambiarEstado(
        int $id,
        string $estado
    ): ?array {
        foreach (
            $_SESSION["usuarios"] as $indice => $usuario
        ) {
            if ((int) $usuario["id"] === $id) {
                $_SESSION["usuarios"][$indice]["estado"] =
                    trim($estado);

                return $this->ocultarContrasena(
                    $_SESSION["usuarios"][$indice]
                );
            }
        }

        return null;
    }

    /**
     * Cambia el rol de un usuario.
     */
    public function cambiarRol(
        int $id,
        string $rol
    ): ?array {
        foreach (
            $_SESSION["usuarios"] as $indice => $usuario
        ) {
            if ((int) $usuario["id"] === $id) {
                $_SESSION["usuarios"][$indice]["rol"] =
                    trim($rol);

                return $this->ocultarContrasena(
                    $_SESSION["usuarios"][$indice]
                );
            }
        }

        return null;
    }

    /**
     * Actualiza la contraseña de un usuario.
     */
    public function cambiarContrasena(
        int $id,
        string $nuevaContrasena
    ): bool {
        foreach (
            $_SESSION["usuarios"] as $indice => $usuario
        ) {
            if ((int) $usuario["id"] === $id) {
                $_SESSION["usuarios"][$indice]["contrasena"] =
                    password_hash(
                        $nuevaContrasena,
                        PASSWORD_DEFAULT
                    );

                return true;
            }
        }

        return false;
    }

    /**
     * Elimina un usuario.
     */
    public function eliminar(int $id): bool
    {
        foreach (
            $_SESSION["usuarios"] as $indice => $usuario
        ) {
            if ((int) $usuario["id"] === $id) {
                unset($_SESSION["usuarios"][$indice]);

                $_SESSION["usuarios"] =
                    array_values($_SESSION["usuarios"]);

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
        if (empty($_SESSION["usuarios"])) {
            return 1;
        }

        $ids = array_column(
            $_SESSION["usuarios"],
            "id"
        );

        return max($ids) + 1;
    }

    /**
     * Elimina la contraseña antes de devolver un usuario.
     */
    private function ocultarContrasena(array $usuario): array
    {
        unset($usuario["contrasena"]);

        return $usuario;
    }
}