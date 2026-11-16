<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?> - <?= esc(nombreEmpresa()) ?></title>
    <style>
        /* ========================================================
           Estilos para Generación de PDF e Impresión Profesional
           Compatibles con Dompdf y navegadores modernos (@media print)
           ======================================================== */
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 20mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* Barra de herramientas en pantalla (se oculta al imprimir/PDF) */
        .no-print {
            background-color: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-bottom: 25px;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .btn-print {
            background-color: #10b981;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background-color: #059669;
        }
        .btn-close-tab {
            background-color: #334155;
            color: #f1f5f9;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            margin-left: 8px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* Contenedor principal de la hoja */
        .pdf-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 25px;
            background: #ffffff;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid <?= esc(colorPrimario() ?: '#1e293b') ?>;
            padding-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .brand-logo {
            max-height: 55px;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: 6px;
        }
        .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-info {
            font-size: 11px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
        }

        .receipt-badge-title {
            text-align: right;
        }
        .receipt-main-title {
            font-size: 16px;
            font-weight: 800;
            color: <?= esc(colorPrimario() ?: '#0f172a') ?>;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .receipt-number {
            font-size: 15px;
            font-weight: 700;
            font-family: 'Courier New', Courier, monospace;
            color: #0f172a;
            margin-bottom: 4px;
        }

        /* Sello PAGADO */
        .stamp-container {
            margin: 15px 0 20px 0;
            text-align: right;
        }
        .stamp-paid {
            display: inline-block;
            padding: 6px 18px;
            border: 2px solid #059669;
            border-radius: 8px;
            color: #059669;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            background-color: #ecfdf5;
        }

        /* Tablas de información a dos columnas */
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin-bottom: 20px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .info-col-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .info-row {
            margin-bottom: 5px;
            font-size: 11.5px;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            display: inline-block;
            width: 115px;
        }
        .info-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Tabla de Conceptos */
        .concept-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .concept-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        .concept-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .concept-table .text-right {
            text-align: right;
        }
        .concept-table .text-center {
            text-align: center;
        }
        .total-row td {
            background-color: #f8fafc;
            font-weight: 800;
            font-size: 14px;
            color: #0f172a;
            border-top: 2px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }
        .total-highlight {
            color: #059669;
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
        }

        /* Firmas y Respaldo */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            margin-bottom: 30px;
        }
        .signature-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 30px;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        /* Pie de página legal */
        .footer-note {
            border-top: 1px dashed #cbd5e1;
            padding-top: 12px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <?php if (empty($isDompdf)): ?>
    <!-- Barra de acciones en el navegador (solo visible en vista previa web, excluida en PDF) -->
    <div class="no-print">
        <div style="font-size: 13px; font-weight: 600;">
            Comprobante de Pago Electrónico · Recibo #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?>
        </div>
        <div>
            <button type="button" class="btn-print" onclick="window.print()">
                🖨️ Imprimir / Guardar como PDF
            </button>
            <button type="button" class="btn-close-tab" onclick="window.close()">
                Cerrar
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="pdf-wrapper">
        <!-- 1. Encabezado con Identidad de Marca Blanca -->
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <?php if (!empty($logoBase64)): ?>
                        <img src="<?= $logoBase64 ?>" alt="Logo" class="brand-logo"><br>
                    <?php elseif (logoEmpresa()): ?>
                        <img src="<?= logoEmpresa() ?>" alt="Logo" class="brand-logo"><br>
                    <?php endif; ?>
                    <div class="brand-name"><?= esc(nombreEmpresa()) ?></div>
                    <div class="brand-info">
                        <?php if (!empty($recibo['nombre_condominio']) && $recibo['nombre_condominio'] !== nombreEmpresa()): ?>
                            <strong>Condominio:</strong> <?= esc($recibo['nombre_condominio']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($recibo['rif_jurisdiccion'])): ?>
                            <strong>RIF / Identificación:</strong> <?= esc($recibo['rif_jurisdiccion']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($recibo['direccion_condominio'])): ?>
                            <strong>Dirección:</strong> <?= esc($recibo['direccion_condominio']) ?><br>
                        <?php endif; ?>
                        <?php if (telefonoContacto()): ?>
                            <strong>Contacto:</strong> <?= esc(telefonoContacto()) ?>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="width: 45%;" class="receipt-badge-title">
                    <div class="receipt-main-title">Recibo de Condominio</div>
                    <div class="receipt-number">N° #<?= str_pad($recibo['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    <div class="brand-info">
                        <?php
                            $fechaEmisionRaw = $recibo['fecha_emision'] ?? null;
                            $tsEmision = (!empty($fechaEmisionRaw) && $fechaEmisionRaw !== '0000-00-00 00:00:00') ? strtotime($fechaEmisionRaw) : false;
                            $fechaEmision = $tsEmision ? date('d/m/Y', $tsEmision) : sprintf('01/%02d/%04d', $recibo['mes'], $recibo['anio']);

                            $fechaPagoRaw = $recibo['fecha_pago'] ?? $pago['fecha_registro'] ?? $recibo['fecha_real_pago'] ?? null;
                            $tsPago = (!empty($fechaPagoRaw) && $fechaPagoRaw !== '0000-00-00 00:00:00') ? strtotime($fechaPagoRaw) : time();
                            $fechaPago = date('d/m/Y', $tsPago);
                        ?>
                        <strong>Fecha de Emisión:</strong> <?= $fechaEmision ?><br>
                        <strong>Fecha de Pago:</strong> <?= $fechaPago ?><br>
                        <strong>Período:</strong> <?= str_pad($recibo['mes'], 2, '0', STR_PAD_LEFT) ?> / <?= $recibo['anio'] ?>
                    </div>

                    <div class="stamp-container">
                        <span class="stamp-paid">✓ PAGADO · SOLVENTE</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 2. Información en 2 Columnas: Inmueble/Residente y Transacción Bancaria -->
        <table class="info-table">
            <tr>
                <!-- Columna Izquierda: Datos del Inmueble y Residente -->
                <td class="info-col">
                    <div class="info-col-title">Inmueble y Residente</div>
                    <div class="info-row">
                        <span class="info-label">Inmueble:</span>
                        <span class="info-value">Apto. <?= esc($recibo['nro_apartamento']) ?></span>
                    </div>
                    <?php if (!empty($recibo['nombre_edificio_torre'])): ?>
                    <div class="info-row">
                        <span class="info-label">Torre / Edificio:</span>
                        <span class="info-value"><?= esc($recibo['nombre_edificio_torre']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label">Propietario/Hab.:</span>
                        <span class="info-value"><?= esc($recibo['residente_nombre'] ?? 'Residente Registrado') ?></span>
                    </div>
                    <?php if (!empty($recibo['residente_cedula'])): ?>
                    <div class="info-row">
                        <span class="info-label">Cédula / ID:</span>
                        <span class="info-value"><?= esc($recibo['residente_cedula']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($recibo['alicuota'])): ?>
                    <div class="info-row">
                        <span class="info-label">Alícuota:</span>
                        <span class="info-value"><?= number_format((float)$recibo['alicuota'], 4) ?>%</span>
                    </div>
                    <?php endif; ?>
                </td>

                <!-- Columna Derecha: Datos de la Transacción -->
                <td class="info-col">
                    <div class="info-col-title">Detalles del Pago Aprobado</div>
                    <div class="info-row">
                        <span class="info-label">Forma de Pago:</span>
                        <span class="info-value"><?= esc(!empty($pago['metodo_pago']) ? $pago['metodo_pago'] : 'Transferencia / Pago Móvil') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">N° Referencia:</span>
                        <span class="info-value" style="font-family: monospace;"><?= esc(!empty($pago['referencia_transaccion']) ? $pago['referencia_transaccion'] : 'CONCILIADO') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estatus de Pago:</span>
                        <span class="info-value" style="color: #059669;">Liquidado y Verificado</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Comprobante ID:</span>
                        <span class="info-value">#<?= esc($pago['id'] ?? $recibo['id']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha Validación:</span>
                        <span class="info-value"><?= $fechaPago ?></span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 3. Tabla Desglosada de Conceptos -->
        <table class="concept-table">
            <thead>
                <tr>
                    <th style="width: 10%;" class="text-center">Ítem</th>
                    <th style="width: 60%;">Concepto / Descripción</th>
                    <th style="width: 15%;" class="text-right">Período</th>
                    <th style="width: 15%;" class="text-right">Total ($)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" style="color: #64748b;">1</td>
                    <td>
                        <strong style="color: #0f172a;">
                            <?= htmlspecialchars(!empty($recibo['descripcion']) ? $recibo['descripcion'] : ('Cuota de Mantenimiento ' . str_pad($recibo['mes'], 2, '0', STR_PAD_LEFT) . '/' . $recibo['anio'])) ?>
                        </strong>
                        <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                            Gastos comunes, servicios y mantenimiento mensual del condominio.
                        </div>
                    </td>
                    <td class="text-right" style="color: #475569; font-family: monospace;">
                        <?= str_pad($recibo['mes'], 2, '0', STR_PAD_LEFT) ?>/<?= $recibo['anio'] ?>
                    </td>
                    <td class="text-right" style="font-family: monospace; font-weight: 700; color: #0f172a;">
                        $<?= number_format((float)$recibo['monto_total'], 2) ?>
                    </td>
                </tr>

                <?php if (!empty($recibo['monto_intereses']) && (float)$recibo['monto_intereses'] > 0): ?>
                <tr>
                    <td class="text-center" style="color: #64748b;">2</td>
                    <td>
                        <strong>Recargos / Intereses por Mora</strong>
                    </td>
                    <td class="text-right" style="color: #475569;">—</td>
                    <td class="text-right" style="font-family: monospace; color: #dc2626;">
                        $<?= number_format((float)$recibo['monto_intereses'], 2) ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Fila de Total -->
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL PAGADO (USD):</td>
                    <td class="text-right total-highlight">
                        $<?= number_format((float)$recibo['monto_total'], 2) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 4. Firmas y Validaciones -->
        <table class="signatures-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-line">
                        ADMINISTRACIÓN DEL CONDOMINIO<br>
                        <span style="font-size: 10px; color: #64748b; font-weight: normal;"><?= esc(nombreEmpresa()) ?></span>
                    </div>
                </td>
                <td class="signature-box">
                    <div class="signature-line">
                        RECIBIDO CONFORME<br>
                        <span style="font-size: 10px; color: #64748b; font-weight: normal;">Apto. <?= esc($recibo['nro_apartamento']) ?></span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 5. Nota Legal al Pie -->
        <div class="footer-note">
            <strong>AVISO LEGAL Y ADMINISTRATIVO:</strong> El presente documento es un comprobante de pago administrativo y solvencia emitido por la administración de <strong><?= esc(nombreEmpresa()) ?></strong> para certificar la cancelación conforme de la cuota detallada. No constituye factura mercantil ni genera crédito fiscal de conformidad con las normativas tributarias vigentes. Documento generado electrónicamente el <?= date('d/m/Y H:i:s') ?>.
        </div>
    </div>

</body>
</html>
