// reportes.js - Scripts y validaciones para el módulo de reportes

$(document).ready(function() {
    console.log('=== REPORTES.JS INICIADO ===');
    console.log('base_url:', typeof base_url !== 'undefined' ? base_url : 'NO DEFINIDA');
    
    // Inicializar DataTable para movimientos de stock si existe
    if ($('#tablaMovimientos').length > 0) {
        console.log('Tabla de movimientos encontrada, inicializando...');
        inicializarTablaMovimientos();
    } else {
        console.log('Tabla de movimientos NO encontrada');
    }
    
    // Inicializar DataTable para órdenes por cliente si existe
    if ($('#tablaOrdenesCliente').length > 0) {
        console.log('Tabla de órdenes por cliente encontrada, inicializando...');
        inicializarTablaOrdenesCliente();
    } else {
        console.log('Tabla de órdenes por cliente NO encontrada');
    }
    
    // Inicializar DataTable para órdenes por estado si existe
    if ($('#tablaOrdenesEstado').length > 0) {
        console.log('Tabla de órdenes por estado encontrada, inicializando...');
        inicializarTablaOrdenesEstado();
    }
    
    // Inicializar DataTable para total recaudado si existe
    if ($('#tablaTotalRecaudado').length > 0) {
        console.log('Tabla de total recaudado encontrada, inicializando...');
        inicializarTablaTotalRecaudado();
    }
    
    // Inicializar DataTable para repuestos utilizados si existe
    if ($('#tablaRepuestosUtilizados').length > 0) {
        console.log('Tabla de repuestos utilizados encontrada, inicializando...');
        inicializarTablaRepuestosUtilizados();
    }
    
    // Validación de fechas
    inicializarValidacionesFechas();
});

function inicializarTablaMovimientos() {
    console.log('Inicializando tabla de movimientos...');
    
    try {
        var table = $('#tablaMovimientos').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + 'reportes/get-movimientos-stock',
                type: 'GET',
                data: function(d) {
                    d.fecha_inicio = $('#fecha_inicio').val();
                    d.fecha_fin = $('#fecha_fin').val();
                    d.id_repuesto = $('#id_repuesto').val();
                    console.log('Parámetros enviados:', d);
                },
                error: function(xhr, error, thrown) {
                    console.error('Error en AJAX:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los datos de movimientos'
                    });
                },
                dataSrc: function(json) {
                    console.log('Respuesta del servidor:', json);
                    // Verificar que la respuesta sea válida
                    if (json && json.data && Array.isArray(json.data)) {
                        console.log('Datos válidos encontrados:', json.data.length, 'registros');
                        return json.data;
                    } else {
                        console.error('Formato de respuesta inválido:', json);
                        return [];
                    }
                }
            },
            columns: [
                { 
                    data: 'fecha_movimiento',
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                        return data || '';
                    }
                },
                { 
                    data: 'repuesto',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'tipo_movimiento',
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            let badgeClass = 'badge bg-secondary';
                            let icon = '';
                            let tipo = data.toLowerCase();
                            switch(tipo) {
                                case 'entrada':
                                    badgeClass = 'badge bg-success';
                                    icon = '<i class="fas fa-plus"></i> ';
                                    break;
                                case 'salida':
                                    badgeClass = 'badge bg-danger';
                                    icon = '<i class="fas fa-minus"></i> ';
                                    break;
                                case 'ajuste':
                                    badgeClass = 'badge bg-warning';
                                    icon = '<i class="fas fa-edit"></i> ';
                                    break;
                                case 'orden':
                                    badgeClass = 'badge bg-info';
                                    icon = '<i class="fas fa-tools"></i> ';
                                    break;
                            }
                            return '<span class="' + badgeClass + '">' + icon + data + '</span>';
                        }
                        return data || '';
                    }
                },
                { 
                    data: 'cantidad',
                    render: function(data, type, row) {
                        if (type === 'display' && data !== null && data !== undefined) {
                            let color = data > 0 ? 'text-success' : 'text-danger';
                            let sign = data > 0 ? '+' : '';
                            return '<span class="' + color + ' fw-bold">' + sign + data + '</span>';
                        }
                        return data || 0;
                    }
                },
                { 
                    data: 'stock_anterior',
                    render: function(data, type, row) {
                        return data || 0;
                    }
                },
                { 
                    data: 'stock_nuevo',
                    render: function(data, type, row) {
                        return data || 0;
                    }
                },
                { 
                    data: 'motivo',
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'usuario_movimiento',
                    render: function(data, type, row) {
                        return data || 'Sistema';
                    }
                }
            ],
            responsive: true,
            language: {
                url: base_url + 'assets/js/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                console.log('DataTable inicializada correctamente');
                // Agregar clases CSS para mejorar la apariencia
                $('.dataTables_wrapper').addClass('table-reportes-wrapper');
                $('.dataTables_filter input').addClass('form-control-sm');
                $('.dataTables_length select').addClass('form-control-sm');
            },
            drawCallback: function(settings) {
                console.log('DataTable redibujada');
                if (settings && settings.json && settings.json.data) {
                    console.log('Registros encontrados:', settings.json.data.length);
                } else {
                    console.log('No hay datos en settings.json');
                }
            }
        });

        // Cargar lista de repuestos para el filtro
        cargarRepuestos();
        
        // Hacer la tabla disponible globalmente
        window.tablaMovimientos = table;
        
        console.log('Tabla de movimientos inicializada exitosamente');
        
    } catch (error) {
        console.error('Error al inicializar DataTable:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al inicializar la tabla de movimientos'
        });
    }
}

function cargarRepuestos() {
    console.log('Cargando lista de repuestos...');
    $.ajax({
        url: base_url + 'repuestos/listar',
        type: 'GET',
        success: function(response) {
            console.log('Respuesta de repuestos:', response);
            if (response.data) {
                let select = $('#id_repuesto');
                select.empty();
                select.append('<option value="">Todos los repuestos</option>');
                
                response.data.forEach(function(repuesto) {
                    select.append('<option value="' + repuesto[0] + '">' + repuesto[1] + '</option>');
                });
                console.log('Repuestos cargados:', response.data.length);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar repuestos:', error);
        }
    });
}

function filtrarMovimientos() {
    console.log('Filtrando movimientos...');
    if (window.tablaMovimientos) {
        window.tablaMovimientos.ajax.reload();
    } else {
        console.error('Tabla de movimientos no inicializada');
    }
}

function limpiarFiltros() {
    console.log('Limpiando filtros...');
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    $('#id_repuesto').val('');
    filtrarMovimientos();
}

function inicializarValidacionesFechas() {
    var fechaInicio = document.getElementById('fecha_inicio');
    var fechaFin = document.getElementById('fecha_fin');
    
    if(fechaInicio && fechaFin) {
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value < fechaInicio.value) {
                fechaFin.value = '';
            }
        });
        
        fechaFin.addEventListener('change', function() {
            if (fechaInicio.value && fechaFin.value < fechaInicio.value) {
                this.value = '';
            }
        });
    }
} 

function inicializarTablaOrdenesCliente() {
    console.log('=== INICIALIZANDO TABLA ÓRDENES CLIENTE ===');
    
    if ($.fn.DataTable.isDataTable('#tablaOrdenesCliente')) {
        console.log('Destruyendo tabla existente...');
        $('#tablaOrdenesCliente').DataTable().destroy();
    }
    
    console.log('Creando nueva tabla...');
    
    $('#tablaOrdenesCliente').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'reportes/get-ordenes-cliente',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.nombre_cliente = $('#nombre_cliente').val();
                console.log('Parámetros enviados:', d);
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos de órdenes por cliente'
                });
            },
            dataSrc: function(json) {
                console.log('Respuesta del servidor:', json);
                if (json && json.data && Array.isArray(json.data)) {
                    console.log('Datos válidos encontrados:', json.data.length, 'registros');
                    return json.data;
                } else {
                    console.error('Formato de respuesta inválido:', json);
                    return [];
                }
            }
        },
        columns: [
            { 
                data: 'cliente',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'total_ordenes',
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'total_facturado',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-CL');
                    }
                    return '$0';
                }
            },
            { 
                data: 'promedio_orden',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-CL');
                    }
                    return '$0';
                }
            },
            { 
                data: 'ultima_orden',
                render: function(data, type, row) {
                    if (type === 'display' && data) {
                        return moment(data).format('DD/MM/YYYY');
                    }
                    return data || 'N/A';
                }
            },
            { 
                data: 'porcentaje_total',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                    return '0.00%';
                }
            }
        ],
        responsive: true,
        language: {
            url: base_url + 'assets/js/es-ES.json'
        },
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        initComplete: function() {
            console.log('DataTable inicializada correctamente');
            $('.dataTables_wrapper').addClass('table-reportes-wrapper');
            $('.dataTables_filter input').addClass('form-control-sm');
            $('.dataTables_length select').addClass('form-control-sm');
        },
        drawCallback: function(settings) {
            console.log('DataTable redibujada');
            if (settings && settings.json && settings.json.data) {
                console.log('Registros encontrados:', settings.json.data.length);
            } else {
                console.log('No hay datos en settings.json');
            }
        }
    });

    // Hacer la tabla disponible globalmente
    window.tablaOrdenesCliente = $('#tablaOrdenesCliente').DataTable();
    
    console.log('Tabla de órdenes por cliente inicializada exitosamente');
}

function filtrarOrdenesCliente() {
    console.log('Filtrando órdenes por cliente...');
    console.log('Fecha inicio:', $('#fecha_inicio').val());
    console.log('Fecha fin:', $('#fecha_fin').val());
    
    if (window.tablaOrdenesCliente) {
        window.tablaOrdenesCliente.ajax.reload();
    } else {
        console.error('Tabla de órdenes por cliente no inicializada');
    }
}

function limpiarFiltrosOrdenesCliente() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    filtrarOrdenesCliente();
}

function inicializarTablaOrdenesEstado() {
    console.log('=== INICIALIZANDO TABLA ÓRDENES ESTADO ===');
    
    if ($.fn.DataTable.isDataTable('#tablaOrdenesEstado')) {
        console.log('Destruyendo tabla existente...');
        $('#tablaOrdenesEstado').DataTable().destroy();
    }
    
    console.log('Creando nueva tabla...');
    
    $('#tablaOrdenesEstado').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'reportes/get-ordenes-estado',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.estado = $('#estado_orden').val();
                console.log('Parámetros enviados:', d);
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los datos de órdenes por estado'
                });
            },
            dataSrc: function(json) {
                console.log('Respuesta del servidor:', json);
                if (json && json.data && Array.isArray(json.data)) {
                    console.log('Datos válidos encontrados:', json.data.length, 'registros');
                    // Actualizar gráfico y resumen si existen las funciones
                    if (typeof actualizarGrafico === 'function') {
                        actualizarGrafico(json.data);
                    }
                    if (typeof actualizarResumen === 'function') {
                        actualizarResumen(json.data);
                    }
                    return json.data;
                } else {
                    console.error('Formato de respuesta inválido:', json);
                    return [];
                }
            }
        },
        columns: [
            { 
                data: 'estado',
                render: function(data, type, row) {
                    if (type === 'display' && data) {
                        let badgeClass = 'badge bg-secondary';
                        switch(data.toLowerCase()) {
                            case 'pendiente':
                                badgeClass = 'badge bg-warning';
                                break;
                            case 'en proceso':
                                badgeClass = 'badge bg-info';
                                break;
                            case 'completada':
                                badgeClass = 'badge bg-success';
                                break;
                            case 'cancelada':
                                badgeClass = 'badge bg-danger';
                                break;
                        }
                        return '<span class="' + badgeClass + '">' + data + '</span>';
                    }
                    return data || 'N/A';
                }
            },
            { 
                data: 'cantidad',
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'total_facturado',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-CL');
                    }
                    return '$0';
                }
            },
            { 
                data: 'porcentaje',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                    return '0.00%';
                }
            },
            { 
                data: 'promedio_orden',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-CL');
                    }
                    return '$0';
                }
            }
        ],
        responsive: true,
        language: {
            url: base_url + 'assets/js/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        initComplete: function() {
            console.log('DataTable inicializada correctamente');
            $('.dataTables_wrapper').addClass('table-reportes-wrapper');
            $('.dataTables_filter input').addClass('form-control-sm');
            $('.dataTables_length select').addClass('form-control-sm');
        },
        drawCallback: function(settings) {
            console.log('DataTable redibujada');
            if (settings && settings.json && settings.json.data) {
                console.log('Registros encontrados:', settings.json.data.length);
            } else {
                console.log('No hay datos en settings.json');
            }
        }
    });

    // Hacer la tabla disponible globalmente
    window.tablaOrdenesEstado = $('#tablaOrdenesEstado').DataTable();
    
    console.log('Tabla de órdenes por estado inicializada exitosamente');
}

function filtrarOrdenesEstado() {
    console.log('Filtrando órdenes por estado...');
    console.log('Fecha inicio:', $('#fecha_inicio').val());
    console.log('Fecha fin:', $('#fecha_fin').val());
    console.log('Estado:', $('#estado_orden').val());
    
    if (window.tablaOrdenesEstado) {
        window.tablaOrdenesEstado.ajax.reload();
    } else {
        console.error('Tabla de órdenes por estado no inicializada');
    }
}

function limpiarFiltrosOrdenesEstado() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    $('#estado_orden').val('');
    filtrarOrdenesEstado();
}

function inicializarTablaTotalRecaudado() {
    if ($.fn.DataTable.isDataTable('#tablaTotalRecaudado')) {
        $('#tablaTotalRecaudado').DataTable().destroy();
    }
    
    var table = $('#tablaTotalRecaudado').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'reportes/get-total-recaudado',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                console.log('Parámetros enviados:', d);
            },
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar el total recaudado'
                });
            },
            dataSrc: function(json) {
                console.log('Respuesta del servidor:', json);
                // Verificar que la respuesta sea válida
                if (json && json.data && Array.isArray(json.data)) {
                    console.log('Datos válidos encontrados:', json.data.length, 'registros');
                    // Actualizar resumen y gráficos si las funciones existen
                    if (typeof actualizarResumen === 'function') {
                        actualizarResumen(json.data);
                    }
                    if (typeof actualizarGraficos === 'function') {
                        actualizarGraficos(json.data);
                    }
                    return json.data;
                } else {
                    console.error('Formato de respuesta inválido:', json);
                    return [];
                }
            }
        },
        columns: [
            { 
                data: 'periodo',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'ordenes',
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'ingresos',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-ES');
                    }
                    return '$0';
                }
            },
            { 
                data: 'promedio_diario',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-ES');
                    }
                    return '$0';
                }
            },
            { 
                data: 'promedio_orden',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-ES');
                    }
                    return '$0';
                }
            },
            { 
                data: 'porcentaje',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                    return '0.00%';
                }
            }
        ],
        responsive: true,
        language: {
            url: base_url + 'assets/js/es-ES.json'
        },
        order: [[2, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        initComplete: function() {
            console.log('DataTable inicializada correctamente');
            // Agregar clases CSS para mejorar la apariencia
            $('.dataTables_wrapper').addClass('table-reportes-wrapper');
            $('.dataTables_filter input').addClass('form-control-sm');
            $('.dataTables_length select').addClass('form-control-sm');
        },
        drawCallback: function(settings) {
            console.log('DataTable redibujada');
            if (settings && settings.json && settings.json.data) {
                console.log('Registros encontrados:', settings.json.data.length);
            } else {
                console.log('No hay datos en settings.json');
            }
        }
    });

    // Hacer la tabla disponible globalmente
    window.tablaTotalRecaudado = table;
    
    console.log('Tabla de total recaudado inicializada exitosamente');
}

function inicializarTablaRepuestosUtilizados() {
    if ($.fn.DataTable.isDataTable('#tablaRepuestosUtilizados')) {
        $('#tablaRepuestosUtilizados').DataTable().destroy();
    }
    
    var table = $('#tablaRepuestosUtilizados').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'reportes/get-repuestos-utilizados',
            type: 'GET',
            data: function(d) {
                d.fecha_inicio = $('#fecha_inicio').val();
                d.fecha_fin = $('#fecha_fin').val();
                d.limite = $('#limite').val();
                console.log('Parámetros enviados:', d);
            },
            error: function(xhr, error, thrown) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los repuestos utilizados'
                });
            },
            dataSrc: function(json) {
                console.log('Respuesta del servidor:', json);
                // Verificar que la respuesta sea válida
                if (json && json.data && Array.isArray(json.data)) {
                    console.log('Datos válidos encontrados:', json.data.length, 'registros');
                    // Actualizar resumen y gráficos si las funciones existen
                    if (typeof actualizarResumenRepuestos === 'function') {
                        actualizarResumenRepuestos(json.data);
                    }
                    if (typeof actualizarGraficosRepuestos === 'function') {
                        actualizarGraficosRepuestos(json.data);
                    }
                    return json.data;
                } else {
                    console.error('Formato de respuesta inválido:', json);
                    return [];
                }
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    if (type === 'display') {
                        const ranking = meta.row + 1;
                        let badgeClass = 'badge bg-secondary';
                        if (ranking === 1) badgeClass = 'badge bg-warning';
                        else if (ranking === 2) badgeClass = 'badge bg-secondary';
                        else if (ranking === 3) badgeClass = 'badge bg-danger';
                        return '<span class="' + badgeClass + '">#' + ranking + '</span>';
                    }
                    return meta.row + 1;
                }
            },
            { 
                data: 'repuesto',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'categoria',
                render: function(data, type, row) {
                    if (type === 'display' && data) {
                        return '<span class="badge bg-info">' + data + '</span>';
                    }
                    return data || 'N/A';
                }
            },
            { 
                data: 'cantidad_utilizada',
                render: function(data, type, row) {
                    return data || 0;
                }
            },
            { 
                data: 'valor_total',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return '$' + parseInt(data).toLocaleString('es-ES');
                    }
                    return '$0';
                }
            },
            { 
                data: 'promedio_orden',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return parseFloat(data).toFixed(2);
                    }
                    return '0.00';
                }
            },
            { 
                data: 'porcentaje',
                render: function(data, type, row) {
                    if (type === 'display' && data !== null && data !== undefined) {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                    return '0.00%';
                }
            }
        ],
        responsive: true,
        language: {
            url: base_url + 'assets/js/es-ES.json'
        },
        order: [[3, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        initComplete: function() {
            console.log('DataTable inicializada correctamente');
            // Agregar clases CSS para mejorar la apariencia
            $('.dataTables_wrapper').addClass('table-reportes-wrapper');
            $('.dataTables_filter input').addClass('form-control-sm');
            $('.dataTables_length select').addClass('form-control-sm');
        },
        drawCallback: function(settings) {
            console.log('DataTable redibujada');
            if (settings && settings.json && settings.json.data) {
                console.log('Registros encontrados:', settings.json.data.length);
            } else {
                console.log('No hay datos en settings.json');
            }
        }
    });

    // Hacer la tabla disponible globalmente
    window.tablaRepuestosUtilizados = table;
    
    console.log('Tabla de repuestos utilizados inicializada exitosamente');
} 

// Funciones para total recaudado
function filtrarTotalRecaudado() {
    if (window.tablaTotalRecaudado) {
        window.tablaTotalRecaudado.ajax.reload();
    }
}

function limpiarFiltrosTotalRecaudado() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    filtrarTotalRecaudado();
}

// Funciones para repuestos utilizados
function filtrarRepuestosUtilizados() {
    if (window.tablaRepuestosUtilizados) {
        window.tablaRepuestosUtilizados.ajax.reload();
    }
}

function limpiarFiltrosRepuestosUtilizados() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    $('#limite').val('10');
    filtrarRepuestosUtilizados();
} 