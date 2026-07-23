<?php

class Usuario
{
    // Simulamos una base de datos utilizando un arreglo
    private array $usuarios = [
        [
            "id" => 1,
            "nombre" => "Romario",
            "apellido" => "Surita",
            "documento" => "67152743",
            "correo" => "romariosurita@gmail.com",
            "estado" => "Inactivo"
        ],
        [
            "id" => 2,
            "nombre" => "Rodrigo",
            "apellido" => "Morelli",
            "documento" => "49383843",
            "correo" => "rodrimorelli22@gmail.com",
            "estado" => "Activo"
        ]
    ];

    // Devuelve todos los usuarios
    public function obtenerTodas(): array       //Obtiene todos los usuarios
    {
        return $this->usuarios;
    }

    // Busca un usuario por su ID
    public function obtenerPorId(int $id): ?array       //Obtiene el usuario que se solicite
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario["id"] === $id) {
                return $usuario;
            }
        }

        return null;
    }

    // Crea un nuevo usuario
    public function crear(array $datos): array
    {
        // Generamos un nuevo ID
        $ids = array_column($this->usuarios, "id");
        $nuevoId = empty($ids) ? 1 : max($ids) + 1;

        // Creamos el usuario nuevo
        $nuevoUsuario = [
            "id" => $nuevoId,
            "nombre" => $datos["nombre"],
            "apellido" => $datos["apellido"],
            "documento" => $datos["documento"],
            "correo" => $datos["correo"],

            // El estado siempre comienza como "Pendiente"
            "estado" => "Pendiente"
        ];

        // La agregamos al arreglo
        $this->usuarios[] = $nuevoUsuario;

        // Devolvemos el usuario creado
        return $nuevoUsuario;
    }

    public function actualizar(int $id, array $datos): ?array
    {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                $this->usuarios[$indice] = [
                    "id" => $id,
                    "nombre" => $datos["nombre"],
                    "apellido" => $datos["apellido"],
                    "documento" => $datos["documento"],
                    "correo" => $datos["correo"],
                    "estado" => $datos["estado"],
                ];

                return $this->usuarios[$indice];
            }
        }

        return null;
    }

    public function eliminar(int $id): bool
    {
        foreach ($this->usuarios as $indice => $usuario) {
            if ($usuario["id"] === $id) {
                unset($this->usuarios[$indice]);
                return true;
            }
        }

        return false;
    }
}