// usuarios.js - Validaciones y formateo específico para formularios de usuario

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

var user = document.getElementById('username');
if(user) {
    user.addEventListener('input', function (e) {
        this.value = soloAlfanumerico(this.value);
    });
}

var run = document.getElementById('run');
if(run) {
    run.addEventListener('input', function (e) {
        this.value = autoformatoRun(this.value);
    });
}

$(document).ready(function() {
    if ($('#usuariosTable').length) {
        var table = $('#usuariosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: base_url + 'usuarios/listar',
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
                { data: 1 }, // Usuario
                { data: 2 }, // Nombre Completo
                { data: 3 }, // Correo
                { data: 4 }, // Teléfono
                { data: 5 }, // Rol
                { data: 6 }, // Estado
                { data: 7, orderable: false, searchable: false } // Acciones
            ],
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
        });
        // Eliminar usuario
        var usuarioAEliminar = null;
        window.eliminarUsuario = function(id) {
            usuarioAEliminar = id;
            $('#eliminarModal').modal('show');
        };
        $('#confirmarEliminar').click(function() {
            if (usuarioAEliminar) {
                $.ajax({
                    url: base_url + 'usuarios/eliminar/' + usuarioAEliminar,
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
                            text: 'Error al eliminar el usuario',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }

    // Manejo del formulario de creación/edición de usuario (ambos formularios) con delegación
    $(document).on('submit', '#formCrearUsuario, #formEditarUsuario', function(e) {
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
                        if ($('#usuariosTable').length) {
                            $('#usuariosTable').DataTable().ajax.reload();
                        } else {
                            window.location.href = base_url + 'usuarios';
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
});