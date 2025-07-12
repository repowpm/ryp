// clientes.js - Validaciones y formateo específico para formularios de cliente

['nombres', 'apellido_paterno', 'apellido_materno'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function (e) {
            this.value = soloTexto(this.value);
        });
    }
});

var tel = document.getElementById('telefono');
if(tel) {
    tel.addEventListener('input', function (e) {
        this.value = soloNumeros(this.value);
    });
}

var run = document.getElementById('run');
if(run) {
    run.addEventListener('input', function (e) {
        this.value = autoformatoRun(this.value);
    });
}

// DataTable y acciones
$(document).ready(function() {
    if ($('#clientesTable').length) {
        var table = $('#clientesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: base_url + 'clientes/listar',
                type: 'GET',
                dataSrc: function(json) {
                    return json.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.error('Error en AJAX:', error);
                }
            },
            columns: [
                { data: 0, visible: false }, // ID (oculto)
                { data: 1 }, // RUN
                { data: 2 }, // Nombre Completo
                { data: 3 }, // Correo
                { data: 4 }, // Teléfono
                { data: 5 }, // Dirección
                { data: 6, orderable: false, searchable: false } // Acciones
            ],
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'desc']], // Ordenar por ID descendente (más reciente arriba)
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
        });
        // Eliminar cliente
        var clienteAEliminar = null;
        window.eliminarCliente = function(id) {
            clienteAEliminar = id;
            $('#eliminarModal').modal('show');
        };
        $('#confirmarEliminar').click(function() {
            if (clienteAEliminar) {
                $.ajax({
                    url: base_url + 'clientes/eliminar/' + clienteAEliminar,
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
                            text: 'Error al eliminar el cliente',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }

    // Manejo de formulario de creación
    $('#formCrearCliente').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        title: '¡Cliente Creado!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.href = base_url + 'clientes';
                    });
                } else {
                    if (response.errors) {
                        // Mostrar errores de validación
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
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo procesar la solicitud. Intenta más tarde.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Manejo de formulario de edición
    $('#formEditarCliente').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        title: '¡Cliente Actualizado!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.href = base_url + 'clientes';
                    });
                } else {
                    if (response.errors) {
                        // Mostrar errores de validación
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
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo procesar la solicitud. Intenta más tarde.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
});

// Funciones de validación (puedes moverlas a un archivo común si ya existen)
function soloTexto(valor) {
    return valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '');
}
function soloNumeros(valor) {
    return valor.replace(/[^0-9+]/g, '');
}
function autoformatoRun(valor) {
    return valor.replace(/[^0-9Kk-]/g, '').toUpperCase();
} 