<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
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