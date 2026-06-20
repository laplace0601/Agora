<?php
// Arreglo de prueba original de Angel
$reportes = [
    ["id" => 1, "nombre" => "Carlos Mendoza", "banco" => "Banco Mercantil", "ref" => "98231", "monto" => "45.00"],
    ["id" => 2, "nombre" => "María Alejandra Silva", "banco" => "Banesco", "ref" => "10442", "monto" => "60.00"],
    ["id" => 3, "nombre" => "Juan Carlos Pérez", "banco" => "BBVA", "ref" => "55421", "monto" => "35.50"]
];

foreach ($reportes as $pago) {
    $id = $pago['id'];
    echo "<tr>";
    
    // Nombre e indicador estético inicial (Dorado/Warning corporativo)
    echo "<td class='ps-4'>
            <div class='fw-semibold text-dark fs-6'>{$pago['nombre']}</div>
            <span id='badge-{$id}' class='badge rounded-pill px-2.5 py-1 text-dark mt-1' style='background-color: rgba(217, 119, 6, 0.1); color: #D97706 !important;'>Por validar</span>
          </td>";
    
    // Información del reporte bancario
    echo "<td>
            <div class='small text-secondary'>
                <strong>Banco:</strong> {$pago['banco']}<br>
                <strong>Ref:</strong> #{$pago['ref']}<br>
                <strong>Monto:</strong> <span class='text-dark fw-bold'>\${$pago['monto']}</span>
            </div>
          </td>";
    
    // Casilla del comprobante adaptada al diseño correcto
    echo "<td class='text-center'>
            <div class='espacio-comprobante-agora d-inline-flex flex-column align-items-center justify-content-center'>
                <i class='bi bi-image text-muted mb-1'></i>
                <span>Sin Foto</span>
            </div>
          </td>";
    
    // Botones de acción vinculados al JS de Angel
    echo "<td class='text-center pe-4'>
            <div class='d-inline-flex gap-2'>
                <button type='button' class='btn btn-sm btn-success rounded-pill px-3 py-1 fw-medium shadow-sm' onclick='actualizarEstado({$id}, \"aprobado\")'>
                    <i class='bi bi-check-lg me-1'></i> Aprobar
                </button>
                <button type='button' class='btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-medium' onclick='actualizarEstado({$id}, \"rechazado\")'>
                    <i class='bi bi-x-lg me-1'></i> Rechazar
                </button>
            </div>
          </td>";
    echo "</tr>";
}
?>