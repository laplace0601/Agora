<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table            = 'tickets_soporte';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'usuario_id',
        'categoria',
        'asunto',
        'detalle',
        'estado',
        'fecha_creacion',
        'fecha_resolucion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'categoria'  => 'required|max_length[100]',
        'asunto'     => 'required|max_length[255]',
        'detalle'    => 'required',
        'estado'     => 'required|in_list[Abierto,En Proceso,Resuelto]',
    ];

    /**
     * Lista tickets de un usuario específico (vista del residente).
     */
    public function listarPorUsuario(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('fecha_creacion', 'DESC')
                    ->findAll();
    }

    /**
     * Lista todos los tickets (vista del admin para monitoreo del buzón).
     */
    public function listarTodos(): array
    {
        return $this->orderBy('fecha_creacion', 'DESC')->findAll();
    }

    /**
     * Actualiza el estado de un ticket y registra fecha de resolución si aplica.
     */
    public function actualizarEstado(int $ticketId, string $nuevoEstado): bool
    {
        $datos = ['estado' => $nuevoEstado];

        if ($nuevoEstado === 'Resuelto') {
            $datos['fecha_resolucion'] = date('Y-m-d H:i:s');
        }

        return $this->update($ticketId, $datos);
    }
}
