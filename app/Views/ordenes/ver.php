<?php
$subtotalVista = 0;
foreach ($repuestosOrden as $r) {
    $subtotalVista += $r['cantidad'] * $r['repuesto_precio'];
}
$ivaVista = round($subtotalVista * 0.19);
$totalVista = $subtotalVista + $ivaVista;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><i class="fas fa-eye"></i> Ver Orden de Trabajo</h1>
        <div>
            <a href="<?= base_url('ordenes/editar/' . $orden['id_orden']) ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= base_url('ordenes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Orden #<?= $orden['id_orden'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Información del Cliente</h6>
                            <p><strong>Nombre:</strong> <?= esc($orden['cliente_nombres'] . ' ' . $orden['cliente_apellido']) ?></p>
                            <p><strong>Teléfono:</strong> <?= esc($orden['cliente_telefono']) ?></p>
                            <p><strong>Correo:</strong> <?= esc($orden['cliente_correo']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Información del Vehículo</h6>
                            <p><strong>Patente:</strong> <?= esc($orden['vehiculo_patente']) ?></p>
                            <p><strong>Marca/Modelo:</strong> <?= esc($orden['vehiculo_marca'] . ' ' . $orden['vehiculo_modelo']) ?></p>
                            <p><strong>Año:</strong> <?= esc($orden['vehiculo_anio']) ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Estado de la Orden</h6>
                            <span class="badge badge-<?= getEstadoBadgeClass($orden['nombre_estado']) ?>">
                                <?= esc($orden['nombre_estado']) ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6>Fecha de Registro</h6>
                            <p><?= date('d/m/Y H:i', strtotime($orden['fecha_registro'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($orden['diagnostico'])): ?>
                    <hr>
                    <div>
                        <h6>Diagnóstico</h6>
                        <p><?= nl2br(esc($orden['diagnostico'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary">$<?= number_format($totalVista, 0, ',', '.') ?></h3>
                        <p class="text-muted">Total de la Orden</p>
                    </div>
                    <hr>
                    <div>
                        <p><strong>Subtotal:</strong> $<?= number_format($subtotalVista, 0, ',', '.') ?></p>
                        <p><strong>IVA (19%):</strong> $<?= number_format($ivaVista, 0, ',', '.') ?></p>
                        <p><strong>Repuestos utilizados:</strong> <?= count($repuestosOrden) ?></p>
                        <p><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($orden['created_at'])) ?></p>
                        <?php if ($orden['updated_at'] != $orden['created_at']): ?>
                        <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($orden['updated_at'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repuestos Utilizados -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Repuestos Utilizados</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($repuestosOrden)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Repuesto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repuestosOrden as $repuesto): ?>
                        <tr>
                            <td><?= esc($repuesto['repuesto_nombre']) ?></td>
                            <td><?= $repuesto['cantidad'] ?></td>
                            <td>$<?= number_format($repuesto['repuesto_precio'], 0, ',', '.') ?></td>
                            <td>$<?= number_format($repuesto['cantidad'] * $repuesto['repuesto_precio'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                            <td><strong>$<?= number_format($subtotalVista, 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="3" class="text-right"><strong>IVA (19%):</strong></td>
                            <td><strong>$<?= number_format($ivaVista, 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($totalVista, 0, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p>No se han registrado repuestos para esta orden.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 