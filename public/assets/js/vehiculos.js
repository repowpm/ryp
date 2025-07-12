// Verificar si ya se ha inicializado
if (typeof window.vehiculosInitialized !== 'undefined' && window.vehiculosInitialized) {
    console.log('vehiculos.js ya fue inicializado, saltando...');
} else {
    // Variables globales
    window.vehiculosTable = null;
    window.vehiculoIdToDelete = null;
    window.vehiculosInitialized = true;

    // Inicialización cuando el documento esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM cargado en vehiculos.js');
        
        // Verificar que jQuery esté disponible
        if (typeof jQuery === 'undefined') {
            console.error('jQuery no está disponible en vehiculos.js');
            return;
        }
        
        // Solo inicializar DataTable si estamos en la página de listado
        if (jQuery('#vehiculosTable').length > 0) {
            console.log('Tabla encontrada, inicializando DataTable...');
            inicializarDataTable();
            configurarEventos();
        } else {
            console.log('Tabla no encontrada');
        }
    });
}

// Inicializar DataTable
function inicializarDataTable() {
    console.log('Inicializando DataTable...');
    
    // Verificar si ya existe una instancia del DataTable y destruirla
    try {
        if (jQuery('#vehiculosTable').DataTable().settings().length > 0) {
            console.log('DataTable ya está inicializado, destruyendo instancia anterior...');
            jQuery('#vehiculosTable').DataTable().destroy();
        }
    } catch (e) {
        console.log('No hay instancia previa del DataTable');
    }
    
    // Limpiar cualquier contenido previo en la tabla
    jQuery('#vehiculosTable tbody').empty();
    
    console.log('Creando nueva instancia de DataTable...');
    console.log('URL de AJAX:', baseUrl + '/vehiculos/getVehiculos');
    
    window.vehiculosTable = jQuery('#vehiculosTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: baseUrl + '/vehiculos/getVehiculos',
            type: 'POST',
            dataSrc: function(json) {
                console.log('Datos recibidos del servidor:', json);
                console.log('Cantidad de registros:', json.data ? json.data.length : 0);
                if (!json.data) {
                    console.error('No hay datos en la respuesta');
                    return [];
                }
                return json.data;
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id_vehiculo' },
            { data: 'patente' },
            { data: 'marca' },
            { data: 'modelo' },
            { data: 'anio' },
            { data: 'tipo_vehiculo' },
            { 
                data: null,
                render: function(data) {
                    return data.cliente_nombres + ' ' + data.cliente_apellido_paterno + ' ' + (data.cliente_apellido_materno || '');
                }
            },
            { 
                data: null,
                width: '120px',
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <a href="${baseUrl}vehiculos/editar/${data.id_vehiculo}" class="btn btn-sm btn-warning me-1" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminar('${data.id_vehiculo}')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: baseUrl + 'assets/js/es-ES.json'
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
    });
}

// Configurar eventos
function configurarEventos() {
    // Confirmar eliminación
    jQuery('#confirmarEliminar').on('click', function() {
        eliminarVehiculo();
    });
    
    // Configurar formulario de editar
    if (jQuery('#formEditarVehiculo').length > 0) {
        configurarFormularioEditar();
    }
}





// Confirmar eliminación
function confirmarEliminar(id) {
    window.vehiculoIdToDelete = id;
    jQuery('#eliminarModal').modal('show');
}

// Eliminar vehículo
function eliminarVehiculo() {
    if (!window.vehiculoIdToDelete) return;

    jQuery.ajax({
        url: baseUrl + '/vehiculos/deleteVehiculo',
        type: 'POST',
        data: { id: window.vehiculoIdToDelete },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: '¡Eliminado!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    jQuery('#eliminarModal').modal('hide');
                    window.vehiculosTable.ajax.reload();
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
        jQuery('#eliminarModal').modal('hide');
        Swal.fire({
            title: 'Error',
            text: 'Error al eliminar vehículo',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
}); 
}

// Configurar formulario de editar
function configurarFormularioEditar() {
    console.log('Configurando formulario de editar...');
    
    // Cargar modelos cuando se selecciona una marca
    jQuery('#marca').on('change', function() {
        const marca = jQuery(this).val();
        const modeloSelect = jQuery('#modelo');
        
        // Limpiar opciones actuales
        modeloSelect.empty();
        modeloSelect.append('<option value="">Seleccionar modelo</option>');
        
        if (marca) {
            jQuery.ajax({
                url: baseUrl + 'vehiculos/getModelosByMarca',
                type: 'POST',
                data: { marca: marca },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(item) {
                            const selected = (item.modelo === window.vehiculoActual.modelo && marca === window.vehiculoActual.marca) ? 'selected' : '';
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
    
    // Cargar modelos iniciales si hay marca seleccionada
    const marcaInicial = jQuery('#marca').val();
    if (marcaInicial) {
        jQuery('#marca').trigger('change');
    }
}