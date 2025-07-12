<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-car"></i> Editar Vehículo
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('vehiculos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Vehículo</h5>
            </div>
            <div class="card-body">
                <form id="formEditarVehiculo" method="post" action="<?= base_url('vehiculos/actualizar/' . $vehiculo['id_vehiculo']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                    
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patente" class="form-label">Patente *</label>
                            <input type="text" class="form-control" id="patente" name="patente" value="<?= $vehiculo['patente'] ?>" required maxlength="10" placeholder="Ej: ABC123">
                            <div class="invalid-feedback" id="patente-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca *</label>
                            <select class="form-select" id="marca" name="marca" required>
                                <option value="">Seleccionar marca</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca['marca'] ?>" <?= $vehiculo['marca'] == $marca['marca'] ? 'selected' : '' ?>>
                                        <?= $marca['marca'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="marca-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label">Modelo *</label>
                            <select class="form-select" id="modelo" name="modelo" required>
                                <option value="">Seleccionar modelo</option>
                            </select>
                            <div class="invalid-feedback" id="modelo-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="anio" class="form-label">Año *</label>
                            <input type="number" class="form-control" id="anio" name="anio" value="<?= $vehiculo['anio'] ?>" min="1900" max="2030" required placeholder="Ej: 2020">
                            <div class="invalid-feedback" id="anio-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_tipo" class="form-label">Tipo de Vehículo *</label>
                            <select class="form-select" id="id_tipo" name="id_tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>" <?= $vehiculo['id_tipo'] == $tipo['id_tipo'] ? 'selected' : '' ?>>
                                        <?= $tipo['nombre_tipo'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="id_tipo-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_cliente" class="form-label">Cliente *</label>
                            <select class="form-select" id="id_cliente" name="id_cliente" required>
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id_cliente'] ?>" <?= $vehiculo['id_cliente'] == $cliente['id_cliente'] ? 'selected' : '' ?>>
                                        <?= $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'] . ' (' . $cliente['run'] . ')' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="id_cliente-error"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('vehiculos') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Vehículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Vehículo</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> <?= $vehiculo['id_vehiculo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Patente:</strong> <?= $vehiculo['patente'] ?>
                </div>
                <div class="mb-3">
                    <strong>Marca:</strong> <?= $vehiculo['marca'] ?>
                </div>
                <div class="mb-3">
                    <strong>Modelo:</strong> <?= $vehiculo['modelo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Año:</strong> <?= $vehiculo['anio'] ?>
                </div>
                <div class="mb-3">
                    <strong>Tipo:</strong> <?= $vehiculo['tipo_vehiculo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Cliente:</strong><br>
                    <?= $vehiculo['cliente_nombres'] . ' ' . $vehiculo['cliente_apellido_paterno'] . ' ' . ($vehiculo['cliente_apellido_materno'] ?? '') ?>
                </div>
                <div class="mb-3">
                    <strong>Teléfono Cliente:</strong> <?= $vehiculo['cliente_telefono'] ?: 'No especificado' ?>
                </div>
                <div class="mb-3">
                    <strong>Correo Cliente:</strong> <?= $vehiculo['cliente_correo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Fecha de Creación:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($vehiculo['created_at'])) ?>
                </div>
                <?php if ($vehiculo['updated_at']): ?>
                <div class="mb-3">
                    <strong>Última Actualización:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($vehiculo['updated_at'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Variables globales para JavaScript -->
<script>
    var baseUrl = '<?= base_url() ?>/';
    var vehiculoActual = {
        marca: '<?= $vehiculo['marca'] ?>',
        modelo: '<?= $vehiculo['modelo'] ?>'
    };
</script>

<!-- Script para cargar modelos dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que jQuery esté disponible
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está disponible');
        return;
    }
    
    // Cargar modelos cuando se selecciona una marca
    $('#marca').on('change', function() {
        const marca = $(this).val();
        const modeloSelect = $('#modelo');
        
        // Limpiar opciones actuales
        modeloSelect.empty();
        modeloSelect.append('<option value="">Seleccionar modelo</option>');
        
        if (marca) {
            $.ajax({
                url: baseUrl + 'vehiculos/getModelosByMarca',
                type: 'POST',
                data: { marca: marca },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(item) {
                            const selected = (item.modelo === vehiculoActual.modelo && marca === vehiculoActual.marca) ? 'selected' : '';
                            modeloSelect.append(`<option value="${item.modelo}" ${selected}>${item.modelo}</option>`);
                        });
                    }
                },
                error: function() {
                    console.error('Error al cargar modelos');
                }
            });
        }
    });
    
    // Cargar modelos iniciales si ya hay una marca seleccionada
    if (vehiculoActual.marca) {
        $('#marca').trigger('change');
    }
});

// Autocomplete para clientes
$(document).ready(function() {
    let searchTimeout;
    const clienteSearch = $('#cliente_search');
    const clienteResults = $('#cliente_results');
    const idClienteInput = $('#id_cliente');
    
    // Búsqueda de clientes
    clienteSearch.on('input', function() {
        const query = $(this).val().trim();
        
        // Limpiar timeout anterior
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            clienteResults.hide();
            return;
        }
        
        // Esperar 300ms antes de buscar para evitar muchas peticiones
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: baseUrl + 'vehiculos/buscarClientes',
                type: 'POST',
                data: { query: query },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        mostrarResultadosClientes(response.data);
                    } else {
                        clienteResults.hide();
                    }
                },
                error: function() {
                    clienteResults.hide();
                }
            });
        }, 300);
    });
    
    // Mostrar resultados de clientes
    function mostrarResultadosClientes(clientes) {
        clienteResults.empty();
        
        if (clientes.length === 0) {
            clienteResults.append('<div class="list-group-item text-muted">No se encontraron clientes</div>');
        } else {
            clientes.forEach(function(cliente) {
                const item = $(`
                    <div class="list-group-item list-group-item-action cliente-item" 
                         data-id="${cliente.id_cliente}" 
                         data-nombre="${cliente.nombres} ${cliente.apellido_paterno} ${cliente.apellido_materno || ''}"
                         style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${cliente.nombres} ${cliente.apellido_paterno} ${cliente.apellido_materno || ''}</strong>
                            </div>
                            <small class="text-muted">${cliente.run}</small>
                        </div>
                    </div>
                `);
                
                item.on('click', function() {
                    seleccionarCliente(cliente.id_cliente, $(this).data('nombre'));
                });
                
                clienteResults.append(item);
            });
        }
        
        clienteResults.show();
    }
    
    // Seleccionar cliente
    function seleccionarCliente(id, nombre) {
        idClienteInput.val(id);
        clienteSearch.val(nombre);
        clienteResults.hide();
        
        // Validar el campo
        if (id) {
            clienteSearch.removeClass('is-invalid').addClass('is-valid');
        } else {
            clienteSearch.removeClass('is-valid').addClass('is-invalid');
        }
    }
    
    // Ocultar resultados al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#cliente_search, #cliente_results').length) {
            clienteResults.hide();
        }
    });
    
    // Limpiar búsqueda al cambiar el valor
    clienteSearch.on('change', function() {
        if (!$(this).val()) {
            idClienteInput.val('');
            clienteResults.hide();
        }
    });
});
</script>

<?= $this->endSection() ?> 