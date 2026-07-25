<?php

// Crea la clase que representa el modelo de usuarios.
class Usuario
{
    private array $usuarios;

    public function __construct()
    {
        $this->usuarios = [
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

    // Devuelve todos los usuarios sin incluir sus contraseñas
    public function obtenerTodos(): array
    {
        return array_map(
            fn(array $usuario): array => $this->ocultarContrasena($usuario),
            $this->usuarios
        );
    }

    // Busca un usuario por su ID
    public function obtenerPorId(int $id): ?array
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario["id"] === $id) {
                return $this->ocultarContrasena($usuario);
            }
        }

        return null;
    }

    // Busca un usuario por su correo y la contraseña cifrada
    public function obtenerPorCorreo(string $correo): ?array
    {
        foreach ($this->usuarios as $usuario) {
            if (
                strtolower($usuario["correo"])
                === strtolower(trim($correo))
            ) {
                return $usuario;
            }
        }

        return null;
    }

    // Comprueba si ya existe un usuario con ese documento
    public function existeDocumento(
        string $documento,
        ?int $idExcluido = null
    ): bool {
        foreach ($this->usuarios as $usuario) {
            if (
                $usuario["documento"] === trim($documento)
                && $usuario["id"] !== $idExcluido
            ) {
                return true;
            }
        }

        return false;
    }

    // Comprueba si ya existe un usuario con ese correo
    public function existeCorreo(
        string $correo,
        ?int $idExcluido = null
    ): bool {
        foreach ($this->usuarios as $usuario) {
            if (
                strtolower($usuario["correo"])
                === strtolower(trim($correo))
                && $usuario["id"] !== $idExcluido
            ) {
                return true;
            }
        }

        return false;
    }

    // Crea una nueva solicitud de registro
    public function crear(array $datos): array
    {
        $ids = array_column($this->usuarios, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        $nuevoUsuario = [
            "id" => $nuevoId,
            "nombre" => trim($datos["nombre"]),
            "apellido" => trim($datos["apellido"]),
            "documento" => trim($datos["documento"]),
            "telefono" => trim($datos["telefono"]),
            "correo" => strtolower(trim($datos["correo"])),
            "contrasena" => password_hash(
                $datos["contrasena"],
                PASSWORD_DEFAULT
            ),
            "rol" => trim($datos["rol"]),            
            "estado" => "Activo"
        ];

        $this->usuarios[] = $nuevoUsuario;

        return $this->ocultarContrasena($nuevoUsuario);
    }

    // Actualiza la información personal de un usuario
    public function actualizarPerfil(
        int $id,
        array $datos
    ): ?array {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                $this->usuarios[$indice]["nombre"] =
                    trim($datos["nombre"]);

                $this->usuarios[$indice]["apellido"] =
                    trim($datos["apellido"]);

                $this->usuarios[$indice]["documento"] =
                    trim($datos["documento"]);

                $this->usuarios[$indice]["correo"] =
                    strtolower(trim($datos["correo"]));

                return $this->ocultarContrasena(
                    $this->usuarios[$indice]
                );
            }
        }

        return null;
    }

    // Cambia el estado de un usuario
    public function cambiarEstado(
        int $id,
        string $estado
    ): ?array {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                $this->usuarios[$indice]["estado"] = $estado;

                return $this->ocultarContrasena(
                    $this->usuarios[$indice]
                );
            }
        }

        return null;
    }

    // Cambia el rol de un usuario
    public function cambiarRol(
        int $id,
        string $rol
    ): ?array {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                $this->usuarios[$indice]["rol"] = $rol;

                return $this->ocultarContrasena(
                    $this->usuarios[$indice]
                );
            }
        }

        return null;
    }

    // Actualiza la contraseña de un usuario
    public function cambiarContrasena(
        int $id,
        string $nuevaContrasena
    ): bool {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                $this->usuarios[$indice]["contrasena"] = password_hash(
                    $nuevaContrasena,
                    PASSWORD_DEFAULT
                );

                return true;
            }
        }

        return false;
    }

    // Elimina un usuario
    public function eliminar(int $id): bool
    {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                unset($this->usuarios[$indice]);

                $this->usuarios = array_values($this->usuarios);

                return true;
            }
        }

        return false;
    }

    // Elimina la contraseña antes de devolver un usuario
    private function ocultarContrasena(array $usuario): array
    {
        unset($usuario["contrasena"]);

        return $usuario;
    }
}