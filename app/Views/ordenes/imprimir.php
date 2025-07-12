<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Trabajo - <?= $orden['id_orden'] ?></title>
    <style>
        @page {
            size: letter;
            margin: 0.5in;
        }
        
        @media print {
            body { 
                margin: 0; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            .print-container { box-shadow: none !important; }
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .print-container {
            width: 8.5in;
            height: 11in;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        /* Header */
        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            text-align: center;
            border-bottom: 3px solid #e74c3c;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header .orden-numero {
            font-size: 18px;
            margin-top: 5px;
            opacity: 0.9;
            font-weight: bold;
        }
        
        /* Content */
        .content {
            padding: 20px;
            height: calc(11in - 80px - 60px); /* 11in - header - footer */
            overflow: hidden;
        }
        
        /* Información del Cliente y Vehículo */
        .info-section {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-column {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        
        .info-title {
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            min-width: 80px;
        }
        
        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
        }
        
        /* Tabla de Repuestos */
        .repuestos-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-weight: bold;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 5px;
        }
        
        .repuestos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .repuestos-table th {
            background: #34495e;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        
        .repuestos-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        
        .repuestos-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        /* Totales */
        .totals-section {
            margin-top: 15px;
            border-top: 2px solid #e74c3c;
            padding-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        /* Diagnóstico y Observaciones */
        .diagnostico-section {
            margin-top: 15px;
        }
        
        .diagnostico-content {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 10px;
            font-size: 11px;
            min-height: 60px;
        }
        
        /* Footer */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ecf0f1;
            padding: 10px 20px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .status-pendiente { background: #fff3cd; color: #856404; }
        .status-en-proceso { background: #d1ecf1; color: #0c5460; }
        .status-completada { background: #d4edda; color: #155724; }
        .status-cancelada { background: #f8d7da; color: #721c24; }
        .status-facturada { background: #cce5ff; color: #004085; }
        
        /* Botones de impresión */
        .print-buttons {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 8.5in) {
            .print-container {
                width: 100%;
                height: auto;
            }
            
            .content {
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="<?= base_url('ordenes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>ORDEN DE TRABAJO</h1>
            <div class="orden-numero"><?= $orden['id_orden'] ?></div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Información del Cliente y Vehículo -->
            <div class="info-section">
                <div class="info-column">
                    <div class="info-title">INFORMACIÓN DEL CLIENTE</div>
                    <div class="info-row">
                        <span class="info-label">Nombre:</span>
                        <span class="info-value"><?= $orden['cliente_nombres'] . ' ' . $orden['cliente_apellido'] . ' ' . ($orden['cliente_apellido_materno'] ?? '') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono:</span>
                        <span class="info-value"><?= $orden['cliente_telefono'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Correo:</span>
                        <span class="info-value"><?= $orden['cliente_correo'] ?? 'N/A' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dirección:</span>
                        <span class="info-value"><?= $orden['cliente_direccion'] ?? 'N/A' ?></span>
                    </div>
                </div>
                
                <div class="info-column">
                    <div class="info-title">INFORMACIÓN DEL VEHÍCULO</div>
                    <div class="info-row">
                        <span class="info-label">Patente:</span>
                        <span class="info-value"><?= $orden['vehiculo_patente'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Marca:</span>
                        <span class="info-value"><?= $orden['vehiculo_marca'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Modelo:</span>
                        <span class="info-value"><?= $orden['vehiculo_modelo'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Año:</span>
                        <span class="info-value"><?= $orden['vehiculo_anio'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value"><?= $orden['tipo_vehiculo'] ?? 'N/A' ?></span>
                    </div>
                </div>
            </div>

            <!-- Información de la Orden -->
            <div class="info-section">
                <div class="info-column">
                    <div class="info-title">DETALLES DE LA ORDEN</div>
                    <div class="info-row">
                        <span class="info-label">Número:</span>
                        <span class="info-value"><?= $orden['id_orden'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha:</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($orden['fecha_registro'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $orden['nombre_estado'])) ?>">
                                <?= $orden['nombre_estado'] ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Usuario:</span>
                        <span class="info-value"><?= $orden['usuario_creacion'] ?? 'SISTEMA' ?></span>
                    </div>
                </div>
                
                <div class="info-column">
                    <div class="info-title">FECHAS IMPORTANTES</div>
                    <div class="info-row">
                        <span class="info-label">Registro:</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($orden['fecha_registro'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Inicio:</span>
                        <span class="info-value"><?= $orden['fecha_inicio'] ? date('d/m/Y H:i', strtotime($orden['fecha_inicio'])) : 'Pendiente' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Finalización:</span>
                        <span class="info-value"><?= $orden['fecha_fin'] ? date('d/m/Y H:i', strtotime($orden['fecha_fin'])) : 'Pendiente' ?></span>
                    </div>
                </div>
            </div>

            <!-- Repuestos -->
            <div class="repuestos-section">
                <div class="section-title">REPUESTOS UTILIZADOS</div>
                <?php if (!empty($repuestosOrden)): ?>
                    <table class="repuestos-table">
                        <thead>
                            <tr>
                                <th class="text-left">Repuesto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalRepuestos = 0;
                            foreach ($repuestosOrden as $repuesto): 
                                $totalRepuestos += $repuesto['subtotal'];
                            ?>
                                <tr>
                                    <td class="text-left"><?= $repuesto['repuesto_nombre'] ?></td>
                                    <td class="text-center"><?= $repuesto['cantidad'] ?></td>
                                    <td class="text-right">$<?= number_format($repuesto['repuesto_precio'], 0, ',', '.') ?></td>
                                    <td class="text-right">$<?= number_format($repuesto['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                        No hay repuestos registrados en esta orden
                    </div>
                <?php endif; ?>
            </div>

            <!-- Totales -->
            <?php $ivaImpresion = round(($totalRepuestos ?? 0) * 0.19); $totalImpresion = ($totalRepuestos ?? 0) + $ivaImpresion; ?>
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal Repuestos:</span>
                    <span>$<?= number_format($totalRepuestos ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="total-row">
                    <span>IVA (19%):</span>
                    <span>$<?= number_format($ivaImpresion, 0, ',', '.') ?></span>
                </div>
                <div class="total-row">
                    <span>Descuento:</span>
                    <span>$<?= number_format($orden['descuento'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="total-row final">
                    <span>TOTAL:</span>
                    <span>$<?= number_format($totalImpresion, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Diagnóstico y Observaciones -->
            <div class="diagnostico-section">
                <div class="section-title">DIAGNÓSTICO Y OBSERVACIONES</div>
                <div class="diagnostico-content">
                    <strong>Diagnóstico:</strong><br>
                    <?= $orden['diagnostico'] ? nl2br(htmlspecialchars($orden['diagnostico'])) : 'Sin diagnóstico registrado' ?>
                </div>
                <div class="diagnostico-content" style="margin-top: 10px;">
                    <strong>Observaciones:</strong><br>
                    <?= $orden['observaciones'] ? nl2br(htmlspecialchars($orden['observaciones'])) : 'Sin observaciones' ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>TALLER MECÁNICO</strong> | 
            Teléfono: (123) 456-7890 | 
            Email: info@taller.com | 
            Dirección: Av. Principal 123, Ciudad
            <br>
            <small>Documento generado el <?= date('d/m/Y H:i:s') ?> | Usuario: <?= $orden['usuario_creacion'] ?? 'SISTEMA' ?></small>
        </div>
    </div>
</body>
</html> 