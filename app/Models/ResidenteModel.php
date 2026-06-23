<?php

namespace App\Models;

use CodeIgniter\Model;

class ResidenteModel extends Model
{
    protected $table            = 'residentes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'usuario_id',
        'nombre_completo',
        'cedula_identidad',
        'telefono',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'usuario_id'       => 'permit_empty|integer',
        'nombre_completo'  => 'required|max_length[100]',
        'cedula_identidad' => 'required|max_length[20]|is_unique[residentes.cedula_identidad,id,{id}]',
        'telefono'         => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'nombre_completo' => [
            'required' => 'El nombre completo es obligatorio.',
        ],
        'cedula_identidad' => [
            'required'  => 'La cédula de identidad es obligatoria.',
            'is_unique' => 'Esta cédula ya está registrada en el sistema.',
        ],
    ];

    /**
     * Obtiene un residente por su usuario_id.
     */
    public function obtenerPorUsuario(int $usuarioId): ?array
    {
        return $this->where('usuario_id', $usuarioId)->first();
    }

    /**
     * Service Layer: Crea un usuario, un residente y le asigna un apartamento en una sola transacción.
     */
    public function crearResidenteCompleto(array $datos, &$errores = []): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $usuarioModel = new \App\Models\UsuarioModel();
        $apartamentoModel = new \App\Models\ApartamentoModel();

        if (!$usuarioModel->insert($datos['usuario'])) {
            $errores = $usuarioModel->errors();
            $db->transRollback();
            return false;
        }
        
        $usuarioId = $usuarioModel->getInsertID();
        $datos['residente']['usuario_id'] = $usuarioId;

        if (!$this->insert($datos['residente'])) {
            $errores = $this->errors();
            $db->transRollback();
            return false;
        }
        
        $residenteId = $this->getInsertID();

        if (!$apartamentoModel->asignarResidente($datos['apartamento_id'], $residenteId)) {
            $errores = ['apartamento' => 'Error al asignar el apartamento.'];
            $db->transRollback();
            return false;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            $errores = ['db' => 'Error de base de datos al procesar la transacción.'];
            return false;
        }

        return true;
    }
}
