<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Alertas de Stock -->
<?php if (!empty($alertasStock)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle"></i> Alertas de Stock
            </h6>
            <div class="row">
                <?php foreach ($alertasStock as $alerta): ?>
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                        <div>
                            <strong><?= $alerta['nombre'] ?></strong><br>
                            <small class="text-muted">
                                Stock: <?= $alerta['stock'] ?> 
                                <span class="badge bg-<?= $alerta['nivel_alerta'] === 'CRÍTICO' ? 'danger' : 'warning' ?>">
                                    <?= $alerta['nivel_alerta'] ?>
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mt-5 mb-0">
            <div class="card-body text-center">
                <h2 class="card-title text-primary mb-3">
                    <i class="fas fa-tools"></i> ¡Bienvenido/a!
                </h2>
                <p class="lead">
                    Hola, <strong><?= session()->get('nombres') ?></strong>.<br>
                    Bienvenido/a al sistema de gestión del <strong>Taller Rápido y Furioso</strong>.
                </p>
                <p class="mb-3">
                    Tu rol: <span class="badge bg-info"><?= session()->get('rol_nombre') ?></span>
                </p>
                <p>
                    Aquí podrás gestionar clientes, vehículos, órdenes de trabajo y mucho más.<br>
                    Utiliza el menú superior para navegar por las diferentes secciones del sistema.
                </p>
                <p class="text-muted mt-4">
                    ¿Necesitas ayuda? Consulta la documentación o contacta a soporte.
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 