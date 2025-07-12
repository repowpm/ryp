<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-edit"></i> Editar Cliente
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('clientes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Cliente</h5>
            </div>
            <div class="card-body">
                <form id="formEditarCliente" method="post" action="<?= base_url('clientes/actualizar/' . $cliente['id_cliente']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= $cliente['nombres'] ?>" required>
                            <div class="invalid-feedback" id="nombres-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?= $cliente['apellido_paterno'] ?>" required>
                            <div class="invalid-feedback" id="apellido_paterno-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?= $cliente['apellido_materno'] ?>">
                            <div class="invalid-feedback" id="apellido_materno-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="run" class="form-label">RUN *</label>
                            <input type="text" class="form-control" id="run" name="run" value="<?= $cliente['run'] ?>" required maxlength="12" placeholder="Ej: 12345678-9">
                            <div class="invalid-feedback" id="run-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= $cliente['telefono'] ?>" placeholder="+56912345678">
                            <div class="invalid-feedback" id="telefono-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= $cliente['direccion'] ?>" maxlength="255" placeholder="Ej: Av. Siempre Viva 1234">
                            <div class="invalid-feedback" id="direccion-error"></div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('clientes') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Cliente</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> <?= $cliente['id_cliente'] ?>
                </div>
                <div class="mb-3">
                    <strong>Nombre Completo:</strong><br>
                    <?= $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'] ?>
                </div>
                <div class="mb-3">
                    <strong>Correo:</strong> <?= $cliente['correo'] ?>
                </div>
                <div class="mb-3">
                    <strong>Teléfono:</strong> <?= $cliente['telefono'] ?: 'No especificado' ?>
                </div>
                <div class="mb-3">
                    <strong>RUN:</strong> <?= $cliente['run'] ?>
                </div>
                <div class="mb-3">
                    <strong>Dirección:</strong> <?= $cliente['direccion'] ?: 'No especificada' ?>
                </div>
                <div class="mb-3">
                    <strong>Fecha de Creación:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($cliente['created_at'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 