<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus"></i> Crear Nueva Orden de Trabajo
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('ordenes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list"></i> Nueva Orden de Trabajo
                </h5>
            </div>
            <div class="card-body">
                <form id="ordenForm" method="post" action="<?= base_url('ordenes/guardar') ?>">
                    <?= csrf_field() ?>
                    
                    <!-- Información del Cliente -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-primary">
                                <i class="fas fa-user"></i> Información del Cliente
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="run_cliente" class="form-label fw-bold">RUN *</label>
                                    <input type="text" class="form-control" id="run_cliente" name="run_cliente" 
                                           placeholder="12345678-9" required>
                                    <div class="invalid-feedback" id="run_cliente-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nombres_cliente" class="form-label fw-bold">Nombres *</label>
                                    <input type="text" class="form-control" id="nombres_cliente" name="nombres_cliente" 
                                           placeholder="Juan Carlos" required>
                                    <div class="invalid-feedback" id="nombres_cliente-error"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="apellido_paterno" class="form-label fw-bold">Apellido Paterno *</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" 
                                           placeholder="González" required>
                                    <div class="invalid-feedback" id="apellido_paterno-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" 
                                           placeholder="Pérez">
                                    <div class="invalid-feedback" id="apellido_materno-error"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono_cliente" class="form-label fw-bold">Teléfono *</label>
                                    <input type="tel" class="form-control" id="telefono_cliente" name="telefono_cliente" 
                                           placeholder="+56 9 1234 5678" required>
                                    <div class="invalid-feedback" id="telefono_cliente-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        <small>El correo electrónico se generará automáticamente</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="direccion_cliente" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion_cliente" name="direccion_cliente" 
                                          rows="2" placeholder="Av. Principal 123, Santiago"></textarea>
                                <div class="invalid-feedback" id="direccion_cliente-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Vehículo -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-car"></i> Información del Vehículo
                            </h6>
                        </div>
                        <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                                    <label for="patente_vehiculo" class="form-label fw-bold">Patente *</label>
                                    <input type="text" class="form-control text-uppercase" id="patente_vehiculo" name="patente_vehiculo" 
                                           placeholder="ABCD12" required>
                                    <div class="invalid-feedback" id="patente_vehiculo-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="id_tipo_vehiculo" class="form-label fw-bold">Tipo de Vehículo *</label>
                                    <select class="form-select" id="id_tipo_vehiculo" name="id_tipo_vehiculo" required>
                                        <option value="">Seleccione tipo</option>
                                        <?php foreach ($tiposVehiculo as $tipo): ?>
                                        <option value="<?= $tipo['id_tipo'] ?>">
                                            <?= esc($tipo['nombre_tipo']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" id="id_tipo_vehiculo-error"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="marca_vehiculo" class="form-label fw-bold">Marca *</label>
                                    <select class="form-select" id="marca_vehiculo" name="marca_vehiculo" required>
                                        <option value="">Seleccione marca</option>
                                        <?php foreach ($marcas as $marca): ?>
                                        <option value="<?= esc($marca['marca']) ?>">
                                            <?= esc($marca['marca']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                                    <div class="invalid-feedback" id="marca_vehiculo-error"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="modelo_vehiculo" class="form-label fw-bold">Modelo *</label>
                                    <select class="form-select" id="modelo_vehiculo" name="modelo_vehiculo" required>
                                        <option value="">Seleccione modelo</option>
                                    </select>
                                    <div class="invalid-feedback" id="modelo_vehiculo-error"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="anio_vehiculo" class="form-label fw-bold">Año *</label>
                                    <input type="number" class="form-control" id="anio_vehiculo" name="anio_vehiculo" 
                                           min="1900" max="2030" value="<?= date('Y') ?>" required>
                                    <div class="invalid-feedback" id="anio_vehiculo-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Orden -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info">
                                <i class="fas fa-clipboard-list"></i> Información de la Orden
                            </h6>
                        </div>
                        <div class="card-body">
                        <div class="mb-3">
        <label for="estado_orden" class="form-label fw-bold">Estado de la Orden</label>
        <input type="text" class="form-control" id="estado_orden" name="estado_orden" value="EN PROCESO" readonly disabled>
    </div>
                    <div class="mb-3">
                        <label for="diagnostico" class="form-label">Diagnóstico</label>
                        <textarea class="form-control" id="diagnostico" name="diagnostico" 
                                  rows="3" placeholder="Descripción del problema o diagnóstico..."></textarea>
                        <div class="invalid-feedback" id="diagnostico-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" 
                                          rows="2" placeholder="Observaciones adicionales..."></textarea>
                                <div class="invalid-feedback" id="observaciones-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Repuestos -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-warning">
                                <i class="fas fa-cogs"></i> Repuestos Utilizados
                            </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="repuestosTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">Repuesto</th>
                                            <th width="15%">Cantidad</th>
                                            <th width="20%">Precio Unitario</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="10%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Las filas se agregarán dinámicamente -->
                                </tbody>
                                    <tfoot class="table-light">
                                    <tr>
                                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                            <td><strong id="subtotalOrden" class="text-primary">$0</strong></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success" id="agregarRepuesto">
                                                    <i class="fas fa-plus"></i> Agregar
                                            </button>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">IVA (19%):</td>
                                            <td><strong id="ivaOrden" class="text-info">$0</strong></td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                            <td><strong id="totalOrden" class="text-white">$0</strong></td>
                                            <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('ordenes') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnCrearOrden">
                            <i class="fas fa-save"></i> Crear Orden
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary">Instrucciones:</h6>
                    <ul class="small text-muted">
                        <li><i class="fas fa-check text-success"></i> Completa la información del cliente</li>
                        <li><i class="fas fa-check text-success"></i> Ingresa los datos del vehículo</li>
                        <li><i class="fas fa-check text-success"></i> Describe el diagnóstico del problema</li>
                        <li><i class="fas fa-check text-success"></i> Agrega los repuestos utilizados</li>
                        <li><i class="fas fa-check text-success"></i> El total se calcula automáticamente</li>
                    </ul>
                </div>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body p-2">
                                <h6 class="text-success mb-0"><?= count($repuestos) ?></h6>
                                <small class="text-muted">Repuestos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body p-2">
                                <h6 class="text-primary mb-0"><?= count($marcas) ?></h6>
                                <small class="text-muted">Marcas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card border-info">
                            <div class="card-body p-2">
                                <h6 class="text-info mb-0"><?= count($tiposVehiculo) ?></h6>
                                <small class="text-muted">Tipos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card border-warning">
                            <div class="card-body p-2">
                                <h6 class="text-warning mb-0"><?= count($estados) ?></h6>
                                <small class="text-muted">Estados</small>
                            </div>
                        </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template para fila de repuesto -->
<template id="repuestoRowTemplate">
    <tr class="repuesto-row">
        <td>
            <select class="form-select repuesto-select" name="repuestos[{index}][id_repuesto]" required>
                <option value="">Seleccione repuesto</option>
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
                   name="repuestos[{index}][cantidad]" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control precio-input" 
                   name="repuestos[{index}][precio_unitario]" step="0.01" min="0" required>
        </td>
        <td>
            <span class="subtotal-repuesto fw-bold text-primary">$0</span>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar-repuesto">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Función para formatear RUN/RUT chileno
function formatearRut(rut) {
    rut = rut.replace(/^0+|[^0-9kK]+/g, '').toUpperCase();
    if (rut.length <= 1) return rut;
    let cuerpo = rut.slice(0, -1);
    let dv = rut.slice(-1);
    cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return cuerpo + '-' + dv;
}

$(document).ready(function() {
    let repuestoIndex = 0;
    
    // Cargar modelos cuando se selecciona una marca
    $('#marca_vehiculo').change(function() {
        const marca = $(this).val();
        const modeloSelect = $('#modelo_vehiculo');
        
        if (marca) {
            $.ajax({
                url: '<?= base_url('ordenes/getModelos') ?>/' + marca,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        modeloSelect.empty().append('<option value="">Seleccione modelo</option>');
                        response.data.forEach(function(modelo) {
                            modeloSelect.append(`<option value="${modelo.modelo}">${modelo.modelo}</option>`);
                        });
                    }
                }
            });
        } else {
            modeloSelect.empty().append('<option value="">Seleccione modelo</option>');
        }
    });
    
    // Agregar repuesto
    $('#agregarRepuesto').click(function() {
        const template = document.getElementById('repuestoRowTemplate');
        const clone = template.content.cloneNode(true);
        
        // Reemplazar {index} con el índice actual
        $(clone).find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('{index}', repuestoIndex));
            }
        });
        
        $('#repuestosTable tbody').append(clone);
        repuestoIndex++;
        
        // Inicializar eventos para la nueva fila
        initializeRepuestoRow($('#repuestosTable tbody tr:last'));
        // Llamar a calcularTotal después de agregar
        calcularTotal();
    });
    
    // Inicializar eventos para fila de repuesto
    function initializeRepuestoRow(row) {
        const repuestoSelect = row.find('.repuesto-select');
        const cantidadInput = row.find('.cantidad-input');
        const precioInput = row.find('.precio-input');
        const subtotalSpan = row.find('.subtotal-repuesto');
        const eliminarBtn = row.find('.btn-eliminar-repuesto');
        
        // Cambiar repuesto
        repuestoSelect.change(function() {
            const option = $(this).find('option:selected');
            const precio = parseFloat(option.data('precio')) || 0;
            const stock = parseInt(option.data('stock')) || 0;
            
            precioInput.val(precio);
            cantidadInput.attr('max', stock);
            
            calcularSubtotal();
        });
        
        // Cambiar cantidad o precio
        cantidadInput.add(precioInput).on('input', function() {
            calcularSubtotal();
        });
        
        // Eliminar repuesto
        eliminarBtn.click(function() {
            row.remove();
            calcularTotal();
        });
        
        function calcularSubtotal() {
            const cantidad = parseInt(cantidadInput.val()) || 0;
            const precio = parseFloat(precioInput.val()) || 0;
            const subtotal = cantidad * precio;
            
            subtotalSpan.text('$' + subtotal.toLocaleString('es-CL'));
            // Llamar a calcularTotal para actualizar los totales generales
            calcularTotal();
        }
    }
    
    // Calcular totales
    function calcularTotal() {
        let subtotal = 0;
        
        $('.subtotal-repuesto').each(function() {
            const valor = $(this).text().replace('$', '').replace(/\./g, '');
            subtotal += parseFloat(valor) || 0;
        });
        
        const iva = subtotal * 0.19;
        const total = subtotal + iva;
        
        $('#subtotalOrden').text('$' + subtotal.toLocaleString('es-CL'));
        $('#ivaOrden').text('$' + iva.toLocaleString('es-CL'));
        $('#totalOrden').text('$' + total.toLocaleString('es-CL'));
    }
    
    // Validar formulario
    $('#ordenForm').submit(function(e) {
        e.preventDefault();
        
        // Validar campos requeridos
        let isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: 'Por favor, complete todos los campos requeridos.'
            });
            return false;
        }
        
        // Mostrar loading
        $('#btnCrearOrden').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');
        
        // Enviar formulario
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location.href = '<?= base_url('ordenes') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear la orden. Intente nuevamente.'
                });
            },
            complete: function() {
                $('#btnCrearOrden').prop('disabled', false).html('<i class="fas fa-save"></i> Crear Orden');
            }
        });
    });
    
    // Verificar cliente existente
    $('#run_cliente').blur(function() {
        const run = $(this).val();
        if (run && run.length >= 8) {
            $.ajax({
                url: '<?= base_url('ordenes/verificarCliente') ?>/' + run,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.existe) {
                        const cliente = response.cliente;
                        $('#nombres_cliente').val(cliente.nombres);
                        $('#apellido_paterno').val(cliente.apellido_paterno);
                        $('#apellido_materno').val(cliente.apellido_materno || '');
                        $('#telefono_cliente').val(cliente.telefono);
                        $('#correo_cliente').val(cliente.correo || '');
                        $('#direccion_cliente').val(cliente.direccion || '');
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Cliente Encontrado',
                            text: 'Se han cargado los datos del cliente existente.'
                        });
                    }
                }
            });
        }
    });
    
    // Verificar vehículo existente
    $('#patente_vehiculo').blur(function() {
        const patente = $(this).val();
        if (patente && patente.length >= 4) {
            $.ajax({
                url: '<?= base_url('ordenes/verificarVehiculo') ?>/' + patente,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.existe) {
                        const vehiculo = response.vehiculo;
                        $('#marca_vehiculo').val(vehiculo.marca).trigger('change');
                        setTimeout(function() {
                            $('#modelo_vehiculo').val(vehiculo.modelo);
                        }, 500);
                        $('#anio_vehiculo').val(vehiculo.anio);
                        $('#id_tipo_vehiculo').val(vehiculo.id_tipo);
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Vehículo Encontrado',
                            text: 'Se han cargado los datos del vehículo existente.'
                        });
                    }
                }
            });
        }
    });

    // Formatear RUN al salir del campo
    $('#run_cliente').on('blur', function() {
        let rut = $(this).val();
        if (rut) {
            $(this).val(formatearRut(rut));
        }
    });
});
</script>
<?= $this->endSection() ?> 