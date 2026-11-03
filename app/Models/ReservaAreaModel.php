<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ReservaAreaModel
 *
 * Tabla: reservas_areas
 * Gestiona las reservas de áreas comunes con validación de solapamiento horario.
 */
class ReservaAreaModel extends Model
{
    protected $table            = 'reservas_areas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'area_id',
        'apartamento_id',
        'usuario_id',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado',          // Pendiente, Confirmada, Cancelada
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';  // La migración no tiene updated_at

    protected $validationRules = [
        'area_id'        => 'required|integer',
        'apartamento_id' => 'required|integer',
        'usuario_id'     => 'required|integer',
        'fecha_reserva'  => 'required|valid_date[Y-m-d]',
        'hora_inicio'    => 'required',
        'hora_fin'       => 'required',
        'estado'         => 'required|in_list[Pendiente,Confirmada,Cancelada]',
    ];

    // ---------------------------------------------------------------
    // Métodos de negocio
    // ---------------------------------------------------------------

    /**
     * Verifica si existe un solapamiento de horario para un área en una fecha dada.
     *
     * Lógica: dos rangos [A_inicio, A_fin) y [B_inicio, B_fin) se solapan si
     *         A_inicio < B_fin  AND  A_fin > B_inicio
     *
     * Solo se verifican reservas con estado 'Confirmada' o 'Pendiente'.
     *
     * @param int    $areaId       ID del área común
     * @param string $fecha        Fecha en formato Y-m-d
     * @param string $horaInicio   Hora inicio en formato H:i o H:i:s
     * @param string $horaFin      Hora fin en formato H:i o H:i:s
     * @param int|null $excluirId  ID de reserva a excluir (para ediciones)
     * @return bool true si hay solapamiento (NO se puede reservar)
     */
    public function existeSolapamiento(int $areaId, string $fecha, string $horaInicio, string $horaFin, ?int $excluirId = null): bool
    {
        $builder = $this->where('area_id', $areaId)
                        ->where('fecha_reserva', $fecha)
                        ->whereIn('estado', ['Confirmada', 'Pendiente'])
                        ->where('hora_inicio <', $horaFin)
                        ->where('hora_fin >', $horaInicio);

        if ($excluirId !== null) {
            $builder->where('id !=', $excluirId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Lista las reservas de un área en una fecha específica.
     */
    public function listarPorAreaYFecha(int $areaId, string $fecha): array
    {
        return $this->where('area_id', $areaId)
                    ->where('fecha_reserva', $fecha)
                    ->whereIn('estado', ['Confirmada', 'Pendiente'])
                    ->orderBy('hora_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Lista todas las reservas con datos del área y apartamento (para panel admin).
     */
    public function listarConDetalles(): array
    {
        return $this->select('reservas_areas.*, areas_comunes.nombre AS nombre_area, apartamentos.nro_apartamento')
                    ->join('areas_comunes', 'areas_comunes.id = reservas_areas.area_id')
                    ->join('apartamentos', 'apartamentos.id = reservas_areas.apartamento_id')
                    ->orderBy('reservas_areas.fecha_reserva', 'DESC')
                    ->orderBy('reservas_areas.hora_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Lista las reservas de un usuario específico.
     */
    public function listarPorUsuario(int $usuarioId): array
    {
        return $this->select('reservas_areas.*, areas_comunes.nombre AS nombre_area')
                    ->join('areas_comunes', 'areas_comunes.id = reservas_areas.area_id')
                    ->where('reservas_areas.usuario_id', $usuarioId)
                    ->orderBy('reservas_areas.fecha_reserva', 'DESC')
                    ->findAll();
    }
}

