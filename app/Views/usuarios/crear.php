<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('usuarios') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <form id="formCrearUsuario" method="post" action="<?= base_url('usuarios/guardar') ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback" id="username-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                            <div class="invalid-feedback" id="nombres-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                            <div class="invalid-feedback" id="apellido_paterno-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno">
                            <div class="invalid-feedback" id="apellido_materno-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="run" class="form-label">RUN *</label>
                            <input type="text" class="form-control" id="run" name="run" required maxlength="12" placeholder="Ej: 12345678-9">
                            <div class="invalid-feedback" id="run-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="+56912345678">
                            <div class="invalid-feedback" id="telefono-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" maxlength="255" placeholder="Ej: Av. Siempre Viva 1234">
                            <div class="invalid-feedback" id="direccion-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_rol" class="form-label">Rol *</label>
                            <select class="form-select" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Mecánico</option>
                            </select>
                            <div class="invalid-feedback" id="id_rol-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_estado_usuario" class="form-label">Estado *</label>
                            <select class="form-select" id="id_estado_usuario" name="id_estado_usuario" required>
                                <option value="">Seleccionar estado</option>
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                            <div class="invalid-feedback" id="id_estado_usuario-error"></div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> El correo electrónico se generará automáticamente usando la función de la base de datos.
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('usuarios') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Usuario
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
                    <li><i class="fas fa-asterisk text-danger"></i> RUN</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Dirección</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Nombre de Usuario</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Contraseña</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Nombres</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Apellido Paterno</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Rol</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Estado</li>
                </ul>
                <hr>
                <h6>Generación Automática</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope text-info"></i> Correo electrónico</li>
                    <li><i class="fas fa-id-card text-info"></i> ID de usuario</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/usuarios.js') ?>"></script>
<?= $this->endSection() ?> 