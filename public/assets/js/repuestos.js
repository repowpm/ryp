// repuestos.js - Validaciones y formateo específico para formularios de repuestos
console.log('Archivo repuestos.js cargado');

$(document).ready(function() {
    console.log('Repuestos.js - DOM ready');
    
    // Inicialización de DataTable para repuestos
    if ($('#tablaRepuestos').length) {
        var table = $('#tablaRepuestos').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + 'repuestos/listar',
                type: 'GET',
                dataSrc: function(json) {
                    console.log('Datos recibidos:', json);
                    console.log('Número de registros:', json.data ? json.data.length : 0);
                    if (json.data && json.data.length > 0) {
                        console.log('Primer registro:', json.data[0]);
                    }
                    return json.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.error('Error en AJAX:', error);
                    console.error('Respuesta:', xhr.responseText);
                    console.error('Status:', xhr.status);
                }
            },
            columns: [
                { data: 0, visible: false }, // ID (oculto)
                { data: 1 }, // Nombre
                { data: 2 }, // Categoría
                { 
                    data: 3, // Precio
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const precioFormateado = '$' + parseInt(data).toLocaleString('es-CL');
                            console.log('Precio original:', data, 'Formateado:', precioFormateado);
                            return precioFormateado;
                        }
                        return data;
                    }
                },
                { 
                    data: 4, // Stock
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let stockClass = 'normal';
                            let stockText = data;
                            
                            if (data <= 5) {
                                stockClass = 'critico';
                                stockText = data + ' (Crítico)';
                            } else if (data <= 10) {
                                stockClass = 'bajo';
                                stockText = data + ' (Bajo)';
                            }
                            
                            return '<span class="stock-formato ' + stockClass + '">' + stockText + '</span>';
                        }
                        return data;
                    }
                },
                { data: 5 }, // Estado
                { data: 6, orderable: false, searchable: false } // Acciones
            ],
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[1, 'asc']], // Ordenar por nombre
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                console.log('DataTable inicializado correctamente');
            }
        });
        
        // Eliminar repuesto
        var repuestoAEliminar = null;
        window.eliminarRepuesto = function(id) {
            repuestoAEliminar = id;
            $('#eliminarModal').modal('show');
        };
        
        $('#confirmarEliminar').click(function() {
            if (repuestoAEliminar) {
                $.ajax({
                    url: base_url + 'repuestos/eliminar/' + repuestoAEliminar,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        $('#eliminarModal').modal('hide');
                        if (response.success) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function() {
                        $('#eliminarModal').modal('hide');
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al eliminar el repuesto',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }
    
    // Manejo del formulario de creación/edición de repuesto
    $(document).on('submit', '#formCrearRepuesto, #formEditarRepuesto', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        var formData = form.serialize();

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        if ($('#tablaRepuestos').length) {
                            $('#tablaRepuestos').DataTable().ajax.reload();
                        } else {
                            window.location.href = base_url + 'repuestos';
                        }
                    });
                } else {
                    if (response.errors) {
                        for (let campo in response.errors) {
                            $('#' + campo).addClass('is-invalid');
                            $('#' + campo + '-error').text(response.errors[campo]);
                        }
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error inesperado',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Formatear campos numéricos para mostrar solo enteros
    $('#precio, #stock').on('input', function() {
        let value = this.value;
        // Remover caracteres no numéricos excepto el punto decimal
        value = value.replace(/[^0-9.]/g, '');
        // Convertir a entero
        if (value) {
            this.value = parseInt(value) || 0;
        }
    });

    // Función para cargar alertas de stock
    function cargarAlertasStock() {
        console.log('🔄 Cargando alertas de stock...');
        $.ajax({
            url: base_url + 'repuestos/alertas-stock',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('✅ Respuesta de alertas:', response);
                if (response.success && response.data && response.data.length > 0) {
                    // Filtrar alertas cerradas por el usuario
                    const cerradas = JSON.parse(localStorage.getItem('alertasStockCerradas') || '[]');
                    const alertasFiltradas = response.data.filter(function(alerta) {
                        return !cerradas.includes(String(alerta.id_repuesto));
                    });
                    if (alertasFiltradas.length > 0) {
                        mostrarAlertasStock(alertasFiltradas);
                    } else {
                        console.log('ℹ️ Todas las alertas han sido cerradas por el usuario');
                    }
                } else {
                    console.log('ℹ️ No hay alertas de stock para mostrar');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al cargar alertas de stock:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    }

    // Función para mostrar alertas de stock
    function mostrarAlertasStock(alertas) {
        console.log('📊 Mostrando alertas:', alertas);
        
        let html = '<div class="alert alert-warning alert-dismissible fade show alerta-stock-global" role="alert">';
        html += '<h6><i class="fas fa-exclamation-triangle"></i> Alertas de Stock</h6>';
        html += '<ul class="mb-0">';
        
        alertas.forEach(function(alerta) {
            const icono = alerta.nivel_alerta === 'CRÍTICO' ? 'fas fa-exclamation-circle text-danger' : 'fas fa-exclamation-triangle text-warning';
            html += `<li data-id="${alerta.id_repuesto}"><i class="${icono}"></i> <strong>${alerta.nombre}</strong> - Stock: ${alerta.stock} (${alerta.nivel_alerta})</li>`;
        });
        
        html += '</ul>';
        html += '<button type="button" class="btn-close cerrar-alerta-stock" data-bs-dismiss="alert"></button>';
        html += '</div>';
        
        // Insertar alertas al inicio del contenido
        const container = $('.card-repuestos');
        if (container.length > 0) {
            container.prepend(html);
            console.log('✅ Alertas insertadas correctamente');
        } else {
            console.error('❌ No se encontró el contenedor .card-repuestos');
        }
    }

    // Evento para cerrar alerta y guardar en localStorage
    $(document).on('click', '.cerrar-alerta-stock', function() {
        // Obtener los IDs de los repuestos en la alerta
        const alerta = $(this).closest('.alerta-stock-global');
        const ids = [];
        alerta.find('li[data-id]').each(function() {
            ids.push($(this).data('id').toString());
        });
        // Guardar en localStorage
        let cerradas = JSON.parse(localStorage.getItem('alertasStockCerradas') || '[]');
        cerradas = Array.from(new Set([...cerradas, ...ids]));
        localStorage.setItem('alertasStockCerradas', JSON.stringify(cerradas));
        console.log('🗃️ Guardados como cerrados:', cerradas);
    });

    // Cargar alertas al cargar la página
    $(document).ready(function() {
        if ($('#tablaRepuestos').length) {
            console.log('🚀 Inicializando alertas de stock...');
            cargarAlertasStock();
        } else {
            console.log('ℹ️ No se encontró la tabla de repuestos, no se cargarán alertas');
        }
    });
}); 