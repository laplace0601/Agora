<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'correo',
        'nombre_usuario',
        'clave',
        'rol',
        'estado',
        'fecha_registro',
    ];

    protected $useTimestamps = false;

    // ---------------------------------------------------------------
    // Reglas de validación — 'clave' NO es required para permitir
    // updates sin cambio de contraseña. La obligatoriedad en insert
    // se maneja en el callback beforeInsert y en los controladores.
    // ---------------------------------------------------------------
    protected $validationRules = [
        'correo' => 'required|valid_email|max_length[255]|is_unique[usuarios.correo,id,{id}]',
        'clave'  => 'permit_empty|min_length[8]',
        'rol'    => 'required|in_list[root,admin,residente]',
        'estado' => 'permit_empty|in_list[activo,inactivo,eliminado]',
    ];

    protected $validationMessages = [
        'correo' => [
            'required'    => 'El correo es obligatorio.',
            'valid_email' => 'Debe ingresar un correo válido.',
            'is_unique'   => 'Este correo ya está registrado en el sistema.',
        ],
        'clave' => [
            'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
        ],
        'rol' => [
            'required' => 'Debe asignar un rol al usuario.',
            'in_list'  => 'El rol debe ser: root, admin o residente.',
        ],
    ];

    // ---------------------------------------------------------------
    // Callbacks: hashear la clave antes de insertar o actualizar
    // ---------------------------------------------------------------

    protected $beforeInsert = ['hashearClave'];
    protected $beforeUpdate = ['hashearClave'];

    protected function hashearClave(array $data): array
    {
        if (isset($data['data']['clave']) && $data['data']['clave'] !== '') {
            // Prevenir doble hashing: si la clave ya es un hash válido (ej. bcrypt), no volver a hashear
            $info = password_get_info($data['data']['clave']);
            if (empty($info['algo'])) {
                $data['data']['clave'] = password_hash(
                    $data['data']['clave'],
                    PASSWORD_BCRYPT
                );
            }
        } else {
            // Si la clave viene vacía o no fue enviada en un update, no sobreescribir la contraseña existente
            unset($data['data']['clave']);
        }

        return $data;
    }

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Busca un usuario por correo para el proceso de login.
     */
    public function buscarPorCorreo(string $correo): ?array
    {
        return $this->where('correo', $correo)->first();
    }

    /**
     * Verifica las credenciales del usuario.
     * Retorna los datos del usuario si son correctas, o null si fallan.
     */
    public function verificarCredenciales(string $correo, string $claveTextoPlano): ?array
    {
        $usuario = $this->buscarPorCorreo($correo);

        if (! $usuario) {
            return null;
        }

        if (! password_verify($claveTextoPlano, $usuario['clave'])) {
            return null;
        }

        if ($usuario['estado'] !== 'activo') {
            return null;
        }

        return $usuario;
    }

    /**
     * Lista usuarios filtrados por rol.
     */
    public function listarPorRol(string $rol): array
    {
        return $this->where('rol', $rol)
                    ->orderBy('fecha_registro', 'DESC')
                    ->findAll();
    }

    /**
     * Cambia el estado de un usuario (activo/inactivo).
     */
    public function cambiarEstado(int $id, string $nuevoEstado): bool
    {
        return $this->update($id, ['estado' => $nuevoEstado]);
    }
}
