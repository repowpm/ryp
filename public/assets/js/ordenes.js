$(document).ready(function() {
    console.log('ordenes.js - DOM ready');

    // Variable global para evitar múltiples inicializaciones
    if (typeof window.ordenesDataTableInitialized === 'undefined') {
        window.ordenesDataTableInitialized = false;
    }

    if (typeof window.ordenesFormInitialized === 'undefined') {
        window.ordenesFormInitialized = false;
    }

    // Inicializar DataTable para la lista de órdenes
    if ($('#ordenesTable').length && !window.ordenesDataTableInitialized) {
        console.log('Inicializando DataTable para órdenes');
            inicializarDataTable();
            window.ordenesDataTableInitialized = true;
    }

    // Inicializar formulario de crear orden (solo una vez)
    if ($('#ordenForm').length && window.location.pathname.includes('/crear') && !window.ordenesFormInitialized) {
        console.log('Inicializando formulario de crear orden');
        inicializarFormularioCrear();
        window.ordenesFormInitialized = true;
    }

    // Inicializar formulario de editar orden (solo una vez)
    if ($('#ordenForm').length && window.location.pathname.includes('/editar/') && !window.ordenesFormInitialized) {
        console.log('Inicializando formulario de editar orden');
        inicializarFormularioEditar();
        window.ordenesFormInitialized = true;
    }

    if ($('#ordenForm').length && window.location.pathname.includes('/editar/')) {
        calcularTotalNuevosRepuestos(); // Llamar al cargar la página de edición
    }
});

function inicializarDataTable() {
    console.log('Inicializando DataTable para órdenes');
    
    var table = $('#ordenesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'ordenes/listar',
            type: 'GET',
            dataSrc: function(json) {
                console.log('Datos recibidos:', json);
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
                console.error('URL intentada:', base_url + 'ordenes/listar');
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        },
        columns: [
            { data: 0 }, // ID
            { data: 1 }, // Cliente
            { data: 2 }, // Vehículo
            { data: 3 }, // Estado
            { data: 4 }, // Fecha
            { data: 5 }, // Total
            { data: 6, orderable: false, searchable: false } // Acciones
        ],
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[4, 'desc']], // Ordenar por fecha descendente (más reciente arriba)
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
    });

    // Event listener para botones de eliminar orden
    $(document).on('click', '.btn-eliminar', function() {
        console.log('Botón eliminar clickeado');
        var id = $(this).data('id');
        console.log('ID de orden a eliminar:', id);
        if (id) {
            ordenAEliminar = id;
            $('#eliminarModal').modal('show');
        }
    });
    
    // Eliminar orden
    var ordenAEliminar = null;
    window.eliminarOrden = function(id) {
        ordenAEliminar = id;
        $('#eliminarModal').modal('show');
    };
    
    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        console.log('Confirmando eliminación de orden:', ordenAEliminar);
        if (ordenAEliminar) {
            $.ajax({
                url: base_url + 'ordenes/eliminar/' + ordenAEliminar,
                type: 'POST',
                data: {
                    [csrf_token_name]: csrf_token_value
                },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message
                        }).then(() => {
                            table.ajax.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en AJAX:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar la orden'
                    });
                }
            });
        }
        $('#eliminarModal').modal('hide');
    });
}

function inicializarFormularioCrear() {
    console.log('Inicializando formulario de crear orden');

    // Cargar modelos cuando se selecciona una marca
    $('#marca_vehiculo').change(function() {
        var marca = $(this).val();
        if (marca) {
            cargarModelos(marca);
        } else {
            $('#modelo_vehiculo').html('<option value="">Seleccione modelo</option>');
        }
    });

    // Verificar cliente por RUN
    $('#run_cliente').on('blur', function() {
        var run = $(this).val();
        if (run && run.length >= 8) {
            verificarCliente(run);
        }
    });

    // Verificar vehículo por patente
    $('#patente_vehiculo').on('blur', function() {
        var patente = $(this).val();
        if (patente && patente.length >= 4) {
            verificarVehiculo(patente);
        }
    });

    // Inicializar tabla de repuestos
    inicializarTablaRepuestos();

    // Manejar envío del formulario
    $('#ordenForm').submit(function(e) {
        e.preventDefault();
        enviarFormulario();
    });
}

function inicializarFormularioEditar() {
    console.log('Inicializando formulario de editar orden');
    
    // Cargar modelos cuando se selecciona una marca
    $('#marca_vehiculo').change(function() {
        var marca = $(this).val();
        if (marca) {
            cargarModelos(marca);
        } else {
            $('#modelo_vehiculo').html('<option value="">Seleccione modelo</option>');
        }
    });

    // Inicializar tabla de repuestos
    inicializarTablaRepuestos();
    // Llamar al cargar la edición
    calcularTotalNuevosRepuestos();
    // Manejar envío del formulario
    $('#ordenForm').submit(function(e) {
        e.preventDefault();
        enviarFormularioEditar();
    });
}

function cargarModelos(marca) {
    $.ajax({
        url: base_url + 'ordenes/getModelos/' + encodeURIComponent(marca),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Seleccione modelo</option>';
                response.data.forEach(function(modelo) {
                    options += '<option value="' + modelo.modelo + '">' + modelo.modelo + '</option>';
                });
                $('#modelo_vehiculo').html(options);
            } else {
                console.error('Error al cargar modelos:', response.message);
            }
        },
        error: function() {
            console.error('Error al cargar modelos');
        }
    });
}

function verificarCliente(run) {
    $.ajax({
        url: base_url + 'ordenes/verificarCliente/' + encodeURIComponent(run),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                if (response.existe) {
                    // Cliente existe, llenar formulario
                    var cliente = response.cliente;
                    $('#nombres_cliente').val(cliente.nombres);
                    $('#apellido_paterno').val(cliente.apellido_paterno);
                    $('#apellido_materno').val(cliente.apellido_materno || '');
                    $('#telefono_cliente').val(cliente.telefono);
                    $('#correo_cliente').val(cliente.correo || '');
                    $('#direccion_cliente').val(cliente.direccion || '');
                    
                    // Mostrar mensaje
                    Swal.fire({
                        icon: 'info',
                        title: 'Cliente encontrado',
                        text: 'Los datos del cliente han sido cargados automáticamente'
                    });
                }
            } else {
                console.error('Error al verificar cliente:', response.message);
            }
        },
        error: function() {
            console.error('Error al verificar cliente');
        }
    });
}

function verificarVehiculo(patente) {
    $.ajax({
        url: base_url + 'ordenes/verificarVehiculo/' + encodeURIComponent(patente),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                if (response.existe) {
                    // Vehículo existe, llenar formulario
                    var vehiculo = response.vehiculo;
                    $('#marca_vehiculo').val(vehiculo.marca).trigger('change');
                    setTimeout(function() {
                        $('#modelo_vehiculo').val(vehiculo.modelo);
                    }, 500);
                    $('#anio_vehiculo').val(vehiculo.anio);
                    $('#id_tipo_vehiculo').val(vehiculo.id_tipo);
                    
                    // Mostrar mensaje
                    Swal.fire({
                        icon: 'info',
                        title: 'Vehículo encontrado',
                        text: 'Los datos del vehículo han sido cargados automáticamente'
                    });
                }
            } else {
                console.error('Error al verificar vehículo:', response.message);
            }
        },
        error: function() {
            console.error('Error al verificar vehículo');
        }
    });
}

function inicializarTablaRepuestos() {
    var repuestoIndex = 0;

    // Agregar repuesto
    $('#agregarRepuesto').click(function() {
        agregarFilaRepuesto(repuestoIndex++);
        calcularTotalNuevosRepuestos();
    });

    // Eliminar repuesto
    $(document).on('click', '.btn-eliminar-repuesto', function() {
        $(this).closest('tr').remove();
        calcularTotalNuevosRepuestos();
    });

    // Cambiar cantidad o precio
    $(document).on('input', '.cantidad-input, .precio-input', function() {
        calcularSubtotalFila($(this).closest('tr'));
        calcularTotalNuevosRepuestos();
    });

    // Cambiar repuesto seleccionado
    $(document).on('change', '.repuesto-select', function() {
        var row = $(this).closest('tr');
        var option = $(this).find('option:selected');
        var precio = option.data('precio') || 0;
        var stock = option.data('stock') || 0;
        
        row.find('.precio-input').val(precio);
        row.find('.cantidad-input').attr('max', stock);
        
        calcularSubtotalFila(row);
        calcularTotalNuevosRepuestos();
    });

    // Agregar primera fila por defecto
    agregarFilaRepuesto(repuestoIndex++);
    calcularTotalNuevosRepuestos();
}

function agregarFilaRepuesto(index) {
    var template = $('#repuestoRowTemplate').html();
    template = template.replace(/{index}/g, index);
    
    $('#repuestosTable tbody').append(template);
}

function calcularSubtotalFila(row) {
    var cantidad = parseInt(row.find('.cantidad-input').val()) || 0;
    var precio = parseFloat(row.find('.precio-input').val()) || 0;
    var subtotal = cantidad * precio;
    
    row.find('.subtotal').text('$' + subtotal.toLocaleString('es-CL'));
}

function calcularTotal() {
    var subtotal = 0;
    
    $('.repuesto-row').each(function() {
        var cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
        var precio = parseFloat($(this).find('.precio-input').val()) || 0;
        subtotal += cantidad * precio;
    });
    
    var iva = subtotal * 0.19;
    var total = subtotal + iva;
    
    $('#subtotalOrden').text('$' + subtotal.toLocaleString('es-CL'));
    $('#ivaOrden').text('$' + iva.toLocaleString('es-CL'));
    $('#totalOrden').text('$' + total.toLocaleString('es-CL'));
}

function enviarFormulario() {
    // Validar formulario
    if (!validarFormulario()) {
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Creando orden...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar formulario
    $.ajax({
        url: $('#ordenForm').attr('action'),
        type: 'POST',
        data: $('#ordenForm').serialize(),
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    confirmButtonText: 'Ver Orden',
                    showCancelButton: true,
                    cancelButtonText: 'Crear Otra'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = base_url + 'ordenes/ver/' + response.id_orden;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                    mostrarErrores(response.errors);
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
                text: 'Error al crear la orden de trabajo'
            });
        }
    });
}

function enviarFormularioEditar() {
    // Validar formulario
    if (!validarFormulario()) {
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Actualizando orden...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar formulario
    $.ajax({
        url: $('#ordenForm').attr('action'),
        type: 'POST',
        data: $('#ordenForm').serialize(),
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message
                }).then(() => {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                mostrarErrores(response.errors);
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
                text: 'Error al actualizar la orden de trabajo'
            });
        }
    });
}

function validarFormulario() {
    var isValid = true;
    
    // Limpiar errores anteriores
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    // Validar campos requeridos
    $('#ordenForm [required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            var fieldName = $(this).attr('name');
            $('#' + fieldName + '-error').text('Este campo es obligatorio');
            isValid = false;
        }
    });
    
    // Validar RUN
    var run = $('#run_cliente').val();
    if (run && !validarRUN(run)) {
        $('#run_cliente').addClass('is-invalid');
        $('#run_cliente-error').text('RUN inválido');
        isValid = false;
    }
    
    // Validar patente
    var patente = $('#patente_vehiculo').val();
    if (patente && !validarPatente(patente)) {
        $('#patente_vehiculo').addClass('is-invalid');
        $('#patente_vehiculo-error').text('Patente inválida');
        isValid = false;
    }
    
    // Validar que haya al menos un repuesto (solo para crear orden, no para editar)
    if (window.location.pathname.includes('/crear') && $('.repuesto-row').length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Debe agregar al menos un repuesto'
        });
        isValid = false;
    }
    
    return isValid;
}

function validarRUN(run) {
    // Formato básico: 12345678-9
    var regex = /^\d{7,8}-[\dkK]$/;
    return regex.test(run);
}

function validarPatente(patente) {
    // Formato básico: ABCD12 o AB1234
    var regex = /^[A-Z]{2,4}\d{2,4}$/;
    return regex.test(patente.toUpperCase());
}

function mostrarErrores(errors) {
    if (errors) {
        Object.keys(errors).forEach(function(field) {
            $('#' + field).addClass('is-invalid');
            $('#' + field + '-error').text(errors[field]);
        });
    }
} 

// Ajustar el cálculo de totales de nuevos repuestos en la edición
function calcularTotalNuevosRepuestos() {
    var subtotalNuevos = 0;
    // Calcular subtotal de todos los repuestos en la tabla de nuevos repuestos
    $('#nuevosRepuestosTable tbody tr').each(function() {
        var cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
        var precio = parseFloat($(this).find('.precio-input').val()) || 0;
        var subtotalFila = cantidad * precio;
        subtotalNuevos += subtotalFila;
        $(this).find('.subtotal').text('$' + subtotalFila.toLocaleString('es-CL'));
    });
    var ivaNuevos = subtotalNuevos * 0.19;
    var totalNuevos = subtotalNuevos + ivaNuevos;
    $('#subtotalNuevosRepuestos').text('$' + subtotalNuevos.toLocaleString('es-CL'));
    // Calcular subtotal de repuestos existentes
    var subtotalExistentes = 0;
    $('#repuestosExistentesTable tbody tr').each(function() {
        var cantidad = parseInt($(this).find('td:eq(1)').text().replace(/[^\d]/g, '')) || 0;
        var precio = parseFloat($(this).find('td:eq(2)').text().replace(/[^\d]/g, '')) || 0;
        subtotalExistentes += cantidad * precio;
    });
    var ivaExistentes = subtotalExistentes * 0.19;
    var totalExistentes = subtotalExistentes + ivaExistentes;
    var subtotalGeneral = subtotalExistentes + subtotalNuevos;
    var ivaGeneral = ivaExistentes + ivaNuevos;
    var totalGeneral = subtotalGeneral + ivaGeneral;
    $('#subtotalGeneral').text('$' + subtotalGeneral.toLocaleString('es-CL'));
    $('#ivaGeneral').text('$' + ivaGeneral.toLocaleString('es-CL'));
    $('#totalGeneral').text('$' + totalGeneral.toLocaleString('es-CL'));
    // Actualizar el cuadro de la derecha (resumen)
    $('#resumenSubtotal').text('$' + subtotalGeneral.toLocaleString('es-CL'));
    $('#resumenIva').text('$' + ivaGeneral.toLocaleString('es-CL'));
    $('#resumenTotal').text('$' + totalGeneral.toLocaleString('es-CL'));
}
// Forzar refresco tras guardar exitosamente
$(document).on('submit', '#ordenForm', function(e) {
    setTimeout(function() {
        calcularTotalNuevosRepuestos();
    }, 500);
});

// Función para inicializar eventos de nueva fila de repuesto
function initializeNuevoRepuestoRow(row) {
    const repuestoSelect = row.find('.repuesto-select');
    const cantidadInput = row.find('.cantidad-input');
    const precioTd = row.find('.precio-unitario');
    const precioInput = row.find('.precio-input');
    const subtotalTd = row.find('.subtotal');
    const eliminarBtn = row.find('.btn-eliminar-repuesto');
    
    // Remover eventos previos para evitar duplicados
    repuestoSelect.off('change');
    cantidadInput.off('input');
    eliminarBtn.off('click');
    
    // Cambiar repuesto
    repuestoSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const precio = parseFloat(selectedOption.data('precio')) || 0;
        const stock = parseInt(selectedOption.data('stock')) || 0;
        
        precioTd.text('$' + precio.toLocaleString('es-CL'));
        precioInput.val(precio);
        cantidadInput.attr('max', stock);
        
        // Actualizar subtotal de la fila
        const cantidad = parseInt(cantidadInput.val()) || 0;
        const subtotal = cantidad * precio;
        subtotalTd.text('$' + subtotal.toLocaleString('es-CL'));
        
        console.log('Repuesto cambiado - Precio:', precio, 'Cantidad:', cantidad, 'Subtotal:', subtotal);
        calcularTotalNuevosRepuestos();
    });
    
    // Cambiar cantidad
    cantidadInput.on('input', function() {
        const cantidad = parseInt($(this).val()) || 0;
        const precio = parseFloat(precioInput.val()) || 0;
        const subtotal = cantidad * precio;
        subtotalTd.text('$' + subtotal.toLocaleString('es-CL'));
        
        console.log('Cantidad cambiada - Precio:', precio, 'Cantidad:', cantidad, 'Subtotal:', subtotal);
        calcularTotalNuevosRepuestos();
    });
    
    // Eliminar repuesto
    eliminarBtn.on('click', function() {
        console.log('Eliminando repuesto');
        row.remove();
        calcularTotalNuevosRepuestos();
    });
} 