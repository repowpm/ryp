<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-chart-bar"></i> Reportes del Sistema
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Movimientos de Stock -->
    <div class="col-12 col-md-3">
        <div class="card card-reportes h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-history fa-2x text-primary mb-2"></i>
                <h6 class="card-title mt-2 mb-1">Movimientos de Stock</h6>
                <p class="card-text small">Historial de entradas, salidas y ajustes de inventario</p>
                <a href="<?= base_url('reportes/movimientos-stock') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-chart-line"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <!-- Órdenes por Cliente -->
    <div class="col-12 col-md-3">
        <div class="card card-reportes h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h6 class="card-title mt-2 mb-1">Órdenes por Cliente</h6>
                <p class="card-text small">Órdenes agrupadas por cliente</p>
                <a href="<?= base_url('reportes/ordenes-cliente') ?>" class="btn btn-info btn-sm">
                    <i class="fas fa-user"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <!-- Órdenes por Estado -->
    <div class="col-12 col-md-3">
        <div class="card card-reportes h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                <h6 class="card-title mt-2 mb-1">Órdenes por Estado</h6>
                <p class="card-text small">Distribución de órdenes por estado</p>
                <a href="<?= base_url('reportes/ordenes-estado') ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-chart-pie"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <!-- Total Recaudado -->
    <div class="col-12 col-md-3">
        <div class="card card-reportes h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                <h6 class="card-title mt-2 mb-1">Total Recaudado</h6>
                <p class="card-text small">Ingresos totales por período</p>
                <a href="<?= base_url('reportes/total-recaudado') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-chart-bar"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    <!-- Repuestos Más Utilizados -->
    <div class="col-12 col-md-3">
        <div class="card card-reportes h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-cogs fa-2x text-danger mb-2"></i>
                <h6 class="card-title mt-2 mb-1">Repuestos Más Utilizados</h6>
                <p class="card-text small">Ranking de repuestos con mayor demanda</p>
                <a href="<?= base_url('reportes/repuestos-utilizados') ?>" class="btn btn-danger btn-sm">
                    <i class="fas fa-trophy"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Animación de hover en las tarjetas de reportes
    $('.card-reportes').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-3px) scale(1.03)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0) scale(1)');
        }
    );
});
</script>
<?= $this->endSection() ?> 