<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-car"></i> Crear Nuevo Vehículo
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
                <form id="formCrearVehiculo" method="post" action="<?= base_url('vehiculos/guardar') ?>">
                    <?= csrf_field() ?>
                    
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
                            <input type="text" class="form-control" id="patente" name="patente" required maxlength="10" placeholder="Ej: ABC123" value="<?= old('patente') ?>">
                            <div class="invalid-feedback" id="patente-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca *</label>
                            <select class="form-select" id="marca" name="marca" required>
                                <option value="">Seleccionar marca</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca['marca'] ?>" <?= old('marca') == $marca['marca'] ? 'selected' : '' ?>>
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
                            <input type="number" class="form-control" id="anio" name="anio" min="1900" max="2030" required placeholder="Ej: 2020" value="<?= old('anio') ?>">
                            <div class="invalid-feedback" id="anio-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_tipo" class="form-label">Tipo de Vehículo *</label>
                            <select class="form-select" id="id_tipo" name="id_tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['id_tipo'] ?>" <?= old('id_tipo') == $tipo['id_tipo'] ? 'selected' : '' ?>>
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
                                    <option value="<?= $cliente['id_cliente'] ?>" <?= old('id_cliente') == $cliente['id_cliente'] ? 'selected' : '' ?>>
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
                            <i class="fas fa-save"></i> Guardar Vehículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información</h5>
            </div>
            <div class="card-body">
                <h6>Campos Requeridos</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-asterisk text-danger"></i> Patente</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Marca</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Modelo</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Año</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Tipo de Vehículo</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Cliente</li>
                </ul>
                <hr>
                <h6>Generación Automática</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-id-card text-info"></i> ID de vehículo</li>
                </ul>
                <hr>
                <h6>Tipos de Vehículo</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-car text-primary"></i> Sedan</li>
                    <li><i class="fas fa-car text-primary"></i> Hatchback</li>
                    <li><i class="fas fa-car text-primary"></i> SUV</li>
                    <li><i class="fas fa-car text-primary"></i> Station Wagon</li>
                    <li><i class="fas fa-truck text-primary"></i> Pick Up</li>
                    <li><i class="fas fa-car text-primary"></i> Jeep</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Variables globales para JavaScript -->
<script>
    var baseUrl = '<?= base_url() ?>/';
</script>

<!-- Script para cargar modelos dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que jQuery esté disponible
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está disponible');
        return;
    }
    
    console.log('JavaScript de vehículos cargado');
    console.log('baseUrl:', baseUrl);
    
    // Cargar modelos cuando se selecciona una marca
    $('#marca').on('change', function() {
        const marca = $(this).val();
        const modeloSelect = $('#modelo');
        
        console.log('Marca seleccionada:', marca);
        
        // Limpiar opciones actuales
        modeloSelect.empty();
        modeloSelect.append('<option value="">Seleccionar modelo</option>');
        
        if (marca) {
            console.log('Cargando modelos para marca:', marca);
            
            const url = baseUrl + 'vehiculos/getModelosByMarca';
            console.log('URL de la petición:', url);
            
            $.ajax({
                url: url,
                type: 'POST',
                data: { marca: marca },
                dataType: 'json',
                beforeSend: function() {
                    console.log('Enviando petición AJAX...');
                },
                success: function(response) {
                    console.log('Respuesta AJAX recibida:', response);
                    if (response.success && response.data) {
                        console.log('Modelos encontrados:', response.data.length);
                        response.data.forEach(function(item) {
                            console.log('Agregando modelo:', item.modelo);
                            modeloSelect.append(`<option value="${item.modelo}">${item.modelo}</option>`);
                        });
                        console.log('Modelos cargados exitosamente');
                    } else {
                        console.error('Error en respuesta:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    console.error('xhr:', xhr);
                }
            });
        } else {
            console.log('No se seleccionó marca');
        }
    });
    
    console.log('Evento change configurado para #marca');
});

// Autocomplete para clientes
$(document).ready(function() {
    console.log('=== INICIO AUTCOMPLETE CLIENTES ===');
    
    let searchTimeout;
    const clienteSearch = $('#cliente_search');
    const clienteResults = $('#cliente_results');
    const idClienteInput = $('#id_cliente');
    
    console.log('Elementos encontrados:', {
        clienteSearch: clienteSearch.length,
        clienteResults: clienteResults.length,
        idClienteInput: idClienteInput.length
    });
    
    // Búsqueda de clientes
    clienteSearch.on('input', function() {
        console.log('Input detectado en cliente_search');
        const query = $(this).val().trim();
        console.log('Query:', query);
        
        // Limpiar timeout anterior
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            console.log('Query muy corta, ocultando resultados');
            clienteResults.hide();
            return;
        }
        
        // Esperar 300ms antes de buscar para evitar muchas peticiones
        searchTimeout = setTimeout(function() {
            console.log('Ejecutando búsqueda para:', query);
            const url = baseUrl + 'vehiculos/buscarClientes';
            console.log('URL:', url);
            
            $.ajax({
                url: url,
                type: 'POST',
                data: { query: query },
                dataType: 'json',
                beforeSend: function() {
                    console.log('Enviando petición AJAX...');
                },
                success: function(response) {
                    console.log('Respuesta recibida:', response);
                    if (response.success && response.data) {
                        console.log('Clientes encontrados:', response.data.length);
                        mostrarResultadosClientes(response.data);
                    } else {
                        console.log('No se encontraron clientes o error en respuesta');
                        clienteResults.hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    clienteResults.hide();
                }
            });
        }, 300);
    });
    
    // Mostrar resultados de clientes
    function mostrarResultadosClientes(clientes) {
        console.log('Mostrando resultados para', clientes.length, 'clientes');
        clienteResults.empty();
        
        if (clientes.length === 0) {
            clienteResults.append('<div class="list-group-item text-muted">No se encontraron clientes</div>');
        } else {
            clientes.forEach(function(cliente) {
                console.log('Agregando cliente:', cliente);
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
                    console.log('Cliente seleccionado:', cliente.id_cliente);
                    seleccionarCliente(cliente.id_cliente, $(this).data('nombre'));
                });
                
                clienteResults.append(item);
            });
        }
        
        clienteResults.show();
        console.log('Resultados mostrados');
    }
    
    // Seleccionar cliente
    function seleccionarCliente(id, nombre) {
        console.log('Seleccionando cliente:', id, nombre);
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
    
    console.log('=== FIN AUTCOMPLETE CLIENTES ===');
});
</script>

<?= $this->endSection() ?> 