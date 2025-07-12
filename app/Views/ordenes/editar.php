<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit"></i> Editar Orden de Trabajo
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('ordenes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Orden</h5>
            </div>
            <div class="card-body">
                <form id="ordenForm" method="post" action="<?= base_url('ordenes/actualizar/' . $orden['id_orden']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_estado" class="form-label">Estado *</label>
                            <select class="form-select" id="id_estado" name="id_estado" required>
                                <option value="">Seleccione un estado</option>
                                <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado['id_estado'] ?>" 
                                        <?= ($orden['id_estado'] == $estado['id_estado']) ? 'selected' : '' ?>>
                                    <?= esc($estado['nombre_estado']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="id_estado-error"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="diagnostico" class="form-label">Diagnóstico</label>
                        <textarea class="form-control" id="diagnostico" name="diagnostico" 
                                  rows="3" placeholder="Descripción del problema o diagnóstico..."><?= esc($orden['diagnostico']) ?></textarea>
                        <div class="invalid-feedback" id="diagnostico-error"></div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('ordenes') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Orden
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Repuestos Utilizados -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Repuestos Utilizados</h5>
                <button type="button" class="btn btn-success btn-sm" id="agregarRepuestoBtn" style="display:none;">
                    <i class="fas fa-plus"></i> Agregar Repuesto
                </button>
            </div>
            <div class="card-body">
                <!-- Tabla de repuestos existentes -->
                <?php 
                $subtotalExistentes = 0;
                if (!empty($repuestosOrden)): 
                    foreach ($repuestosOrden as $repuesto) {
                        $subtotalExistentes += $repuesto['cantidad'] * $repuesto['repuesto_precio'];
                    }
                endif; 
                ?>
                <?php if (!empty($repuestosOrden)): ?>
                <div class="table-responsive mb-3">
                    <table class="table table-striped" id="repuestosExistentesTable">
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
                    </table>
                </div>
                <?php endif; ?>

                <!-- Sección para agregar nuevos repuestos -->
                <div id="nuevosRepuestosSection" style="display: none;">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-plus-circle"></i> Nuevos Repuestos
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="nuevosRepuestosTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Repuesto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los nuevos repuestos se agregarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <strong>Total Nuevos Repuestos: <span id="totalNuevosRepuestos">$0</span></strong>
                    </div>
                </div>
                <!-- Total general -->
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Orden</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> <?= $orden['id_orden'] ?>
                </div>
                <div class="mb-3">
                    <strong>Cliente:</strong><br>
                    <?= $orden['cliente_nombres'] . ' ' . $orden['cliente_apellido'] ?>
                </div>
                <div class="mb-3">
                    <strong>Vehículo:</strong><br>
                    <?= $orden['vehiculo_patente'] ?><br>
                    <small class="text-muted">
                        <?= $orden['vehiculo_marca'] . ' ' . $orden['vehiculo_modelo'] ?>
                    </small>
                </div>
                <div class="mb-3">
                    <strong>Estado:</strong> 
                    <span class="badge bg-<?= getEstadoBadgeClass($orden['nombre_estado']) ?>">
                        <?= esc($orden['nombre_estado']) ?>
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Fecha de Registro:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($orden['fecha_registro'])) ?>
                </div>
                <?php
                $subtotalVista = 0;
                foreach ($repuestosOrden as $r) {
                    $subtotalVista += $r['cantidad'] * $r['repuesto_precio'];
                }
                $ivaVista = round($subtotalVista * 0.19);
                $totalVista = $subtotalVista + $ivaVista;
                ?>
                <div class="mb-3">
                    <strong>Subtotal:</strong><br>
                    <span class="h6 text-muted" id="resumenSubtotal">$<?= number_format($subtotalVista, 0, ',', '.') ?></span>
                </div>
                <div class="mb-3">
                    <strong>IVA (19%):</strong><br>
                    <span class="h6 text-muted" id="resumenIva">$<?= number_format($ivaVista, 0, ',', '.') ?></span>
                </div>
                <div class="mb-3">
                    <strong>Total:</strong><br>
                    <span class="h5 text-primary" id="resumenTotal">$<?= number_format($totalVista, 0, ',', '.') ?></span>
                </div>
                <?php if (!empty($orden['diagnostico'])): ?>
                <div class="mb-3">
                    <strong>Diagnóstico:</strong><br>
                    <small class="text-muted"><?= esc($orden['diagnostico']) ?></small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Template para nuevos repuestos -->
<template id="repuestoRowTemplate">
    <tr>
        <td>
            <select class="form-select repuesto-select" name="nuevos_repuestos[{index}][id_repuesto]" required>
                <option value="">Seleccione un repuesto</option>
                <?php foreach ($repuestos as $repuesto): ?>
                <option value="<?= $repuesto['id_repuesto'] ?>" 
                        data-precio="<?= $repuesto['precio'] ?>" 
                        data-stock="<?= $repuesto['stock'] ?>">
                    <?= esc($repuesto['nombre']) ?> - $<?= number_format($repuesto['precio'], 0, ',', '.') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" class="form-control cantidad-input" 
                   name="nuevos_repuestos[{index}][cantidad]" 
                   min="1" value="1" required>
        </td>
        <td class="precio-unitario">$0</td>
        <td class="subtotal">$0</td>
        <td>
            <input type="hidden" class="precio-input" name="nuevos_repuestos[{index}][precio_unitario]" value="0">
            <button type="button" class="btn btn-danger btn-sm btn-eliminar-repuesto">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/ordenes.js') ?>"></script>
<script>
$(document).ready(function() {
    let repuestoIndex = 0;
    
    // Mostrar/ocultar sección de nuevos repuestos
    $('#agregarRepuestoBtn').click(function() {
        $('#nuevosRepuestosSection').show();
        $(this).hide();
    });
    
    // Agregar nuevo repuesto
    $(document).on('click', '#agregarNuevoRepuesto', function() {
        agregarNuevoRepuesto();
    });
    
    function agregarNuevoRepuesto() {
        const template = document.getElementById('repuestoRowTemplate');
        const clone = template.content.cloneNode(true);
        
        // Reemplazar {index} con el índice actual
        $(clone).find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('{index}', repuestoIndex));
            }
        });
        
        $('#nuevosRepuestosTable tbody').append(clone);
        
        // Inicializar eventos para la nueva fila
        const nuevaFila = $('#nuevosRepuestosTable tbody tr:last');
        initializeNuevoRepuestoRow(nuevaFila);
        
        console.log('Agregando repuesto #' + repuestoIndex);
        repuestoIndex++;
        
        // Calcular totales después de agregar
        setTimeout(function() {
            calcularTotalNuevosRepuestos();
        }, 100);
    }
    
    // Agregar botón para agregar repuesto en la sección de nuevos repuestos
    $('#nuevosRepuestosSection').append(`
        <div class="text-center mt-3">
            <button type="button" class="btn btn-primary btn-sm" id="agregarNuevoRepuesto">
                <i class="fas fa-plus"></i> Agregar Otro Repuesto
            </button>
        </div>
    `);
});
</script>
<?= $this->endSection() ?> 