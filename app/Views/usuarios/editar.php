<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-edit"></i> Editar Usuario
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
                <form id="formEditarUsuario" method="post" action="<?= base_url('usuarios/actualizar/' . $usuario['id_usuario']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $usuario['username'] ?>" required>
                            <div class="invalid-feedback" id="username-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Dejar vacío para mantener la actual">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="form-text">Deja vacío para mantener la contraseña actual</div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= $usuario['nombres'] ?>" required>
                            <div class="invalid-feedback" id="nombres-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?= $usuario['apellido_paterno'] ?>" required>
                            <div class="invalid-feedback" id="apellido_paterno-error"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?= $usuario['apellido_materno'] ?>">
                            <div class="invalid-feedback" id="apellido_materno-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="run" class="form-label">RUN *</label>
                            <input type="text" class="form-control" id="run" name="run" value="<?= isset($usuario['run']) ? $usuario['run'] : '' ?>" required maxlength="12" placeholder="Ej: 12345678-9">
                            <div class="invalid-feedback" id="run-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= $usuario['telefono'] ?>" placeholder="+56912345678">
                            <div class="invalid-feedback" id="telefono-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= isset($usuario['direccion']) ? $usuario['direccion'] : '' ?>" maxlength="255" placeholder="Ej: Av. Siempre Viva 1234">
                            <div class="invalid-feedback" id="direccion-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_rol" class="form-label">Rol *</label>
                            <select class="form-select" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : '' ?>>Mecánico</option>
                            </select>
                            <div class="invalid-feedback" id="id_rol-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_estado_usuario" class="form-label">Estado *</label>
                            <select class="form-select" id="id_estado_usuario" name="id_estado_usuario" required>
                                <option value="">Seleccionar estado</option>
                                <option value="1" <?= $usuario['id_estado_usuario'] == 1 ? 'selected' : '' ?>>Activo</option>
                                <option value="2" <?= $usuario['id_estado_usuario'] == 2 ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                            <div class="invalid-feedback" id="id_estado_usuario-error"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('usuarios') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> <?= $usuario['id_usuario'] ?>
                </div>
                <div class="mb-3">
                    <strong>Usuario:</strong> <?= $usuario['username'] ?>
                </div>
                <div class="mb-3">
                    <strong>Nombre Completo:</strong><br>
                    <?= $usuario['nombres'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno'] ?>
                </div>
                <div class="mb-3">
                    <strong>Correo:</strong> <?= $usuario['correo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Teléfono:</strong> <?= $usuario['telefono'] ?: 'No especificado' ?>
                </div>
                <div class="mb-3">
                    <strong>RUN:</strong> <?= $usuario['run'] ?>
                </div>
                <div class="mb-3">
                    <strong>Dirección:</strong> <?= $usuario['direccion'] ?: 'No especificada' ?>
                </div>
                <div class="mb-3">
                    <strong>Rol:</strong> 
                    <span class="badge bg-<?= $usuario['id_rol'] == 1 ? 'danger' : 'info' ?>">
                        <?= $usuario['nombre_rol'] ?>
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Estado:</strong> 
                    <span class="badge bg-<?= $usuario['id_estado_usuario'] == 1 ? 'success' : 'secondary' ?>">
                        <?= $usuario['nombre_estado'] ?>
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Fecha de Creación:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?> 