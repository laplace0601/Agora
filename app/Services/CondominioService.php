<?php

namespace App\Services;

use App\Models\ApartamentoModel;
use App\Models\CondominioModel;

class CondominioService
{
    /**
     * Recalcula la alícuota de todos los apartamentos de un condominio basándose
     * en la superficie total del condominio y la superficie de cada apartamento.
     * 
     * @param int $condominioId
     * @return bool
     */
    public function recalcularAlicuotas(int $condominioId): bool
    {
        $condoModel = new CondominioModel();
        $aptoModel = new ApartamentoModel();

        $condominio = $condoModel->find($condominioId);
        if (!$condominio) {
            return false;
        }

        $totalMetros = (float)($condominio['total_metros_cuadrados'] ?? 0);
        if ($totalMetros <= 0) {
            return false;
        }

        $apartamentos = $aptoModel->where('condominio_id', $condominioId)->findAll();
        
        if (empty($apartamentos)) {
            return true; // No hay apartamentos que actualizar
        }

        $db = \Config\Database::connect();
        $builder = $db->table('apartamentos');
        
        $batchData = [];
        foreach ($apartamentos as $apto) {
            $aptoMetros = (float)($apto['metros_cuadrados'] ?? 0);
            $nuevaAlicuota = round(($aptoMetros / $totalMetros) * 100, 4);
            
            $batchData[] = [
                'id'       => $apto['id'],
                'alicuota' => $nuevaAlicuota
            ];
        }

        // Si estamos ya en una transacción desde el controlador, updateBatch se acoplará a la misma
        if (!empty($batchData)) {
            $builder->updateBatch($batchData, 'id');
        }

        return true;
    }
}
