<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Taller Rápido y Furioso' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <?php
        $segmento = service('uri')->getSegment(1);
        $cssModulos = ['usuarios','clientes','vehiculos','repuestos','ordenes','reportes'];
        if (in_array($segmento, $cssModulos)) {
            echo '<link rel="stylesheet" href="' . base_url('assets/css/' . $segmento . '.css') . '">';
        }
    ?>
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #dc3545 !important;
        }
        .sidebar {
            min-height: calc(100vh - 60px);
            background-color: #f8f9fa;
        }
        .main-content {
            /* min-height: calc(100vh - 60px); */
            min-height: 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>
    <div class="container-fluid flex-fill">
        <div class="row">
            <!-- Main Content -->
            <main class="col-12 px-md-4 main-content">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    <!-- Footer -->
    <?= $this->include('components/footer') ?>
    <!-- jQuery con fallback -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        if (typeof jQuery === 'undefined') {
            // Fallback si el primer CDN falla
            document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');
        }
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS (después de jQuery) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 Notifications -->
    <?php if (session()->getFlashdata('swal')): ?>
    <script>
        Swal.fire(<?= json_encode(session()->getFlashdata('swal')) ?>);
    </script>
    <?php endif; ?>
    <!-- Scripts específicos de la página -->
    <?php if (isset($title) && strpos($title, 'Gestión de Usuarios') !== false): ?>
    <script>
        jQuery(document).ready(function($) {
            // Inicializar DataTable
            var table = $('#usuariosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url('usuarios/listar') ?>',
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
                order: [[1, 'asc']], // Ordenar por usuario por defecto
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
            });
            // Variable para almacenar el ID del usuario a eliminar
            var usuarioAEliminar = null;
            // Función para eliminar usuario
            window.eliminarUsuario = function(id) {
                usuarioAEliminar = id;
                $('#eliminarModal').modal('show');
            };
            // Confirmar eliminación
            $('#confirmarEliminar').click(function() {
                if (usuarioAEliminar) {
                    $.ajax({
                        url: '<?= base_url('usuarios/eliminar/') ?>' + usuarioAEliminar,
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
        });
    </script>
    <?php endif; ?>
    <?php if (isset($title) && strpos($title, 'Editar Usuario') !== false): ?>
    <script>
    $(function() {
        let enviando = false;
        // Toggle password visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        // Form submission robusto
        $('#formEditarUsuario').off('submit').on('submit', function(e) {
            e.preventDefault();
            if (enviando) return false;
            enviando = true;
            // Limpiar errores previos
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    enviando = false;
                    if (response.success) {
                        Swal.fire({
                            title: '¡Usuario Actualizado!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '<?= base_url('usuarios') ?>';
                        });
                    } else if (response.errors) {
                        Object.keys(response.errors).forEach(function(field) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(response.errors[field]);
                        });
                        Swal.fire({
                            title: 'Error de Validación',
                            html: '<ul class="text-start">' + Object.values(response.errors).map(e => '<li>' + e + '</li>').join('') + '</ul>',
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
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
                error: function(xhr) {
                    enviando = false;
                    let msg = 'Error al actualizar el usuario';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: msg,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
            return false;
        });
    });
    </script>
    <?php endif; ?>
    <?php if (isset($title) && strpos($title, 'Crear Usuario') !== false): ?>
    <script>
    $(function() {
        // Toggle password visibility en crear usuario
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        let enviando = false;
        $('#formCrearUsuario').off('submit').on('submit', function(e) {
            e.preventDefault();
            if (enviando) return false;
            enviando = true;
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    enviando = false;
                    if (response.success) {
                        Swal.fire({
                            title: '¡Usuario Creado!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '<?= base_url('usuarios') ?>';
                        });
                    } else if (response.errors) {
                        Object.keys(response.errors).forEach(function(field) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field + '-error').text(response.errors[field]);
                        });
                        Swal.fire({
                            title: 'Error de Validación',
                            html: '<ul class="text-start">' + Object.values(response.errors).map(e => '<li>' + e + '</li>').join('') + '</ul>',
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
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
                error: function(xhr) {
                    enviando = false;
                    let msg = 'Error al crear el usuario';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: msg,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
            return false;
        });
    });
    </script>
    <?php endif; ?>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html> 