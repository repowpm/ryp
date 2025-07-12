<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-users"></i> Órdenes por Cliente
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= base_url('reportes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="card card-reportes">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-chart-bar text-info"></i>
            Análisis de Órdenes por Cliente
        </h5>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="filtros-reportes">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="fecha_inicio" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Fecha Inicio
                    </label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_fin" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Fecha Fin
                    </label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="<?= date('Y-m-t') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-container">
                        <button type="button" class="btn btn-primary" onclick="filtrarOrdenesCliente()">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltrosOrdenesCliente()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de órdenes por cliente -->
        <div class="table-responsive">
            <table id="tablaOrdenesCliente" class="table table-striped table-hover table-reportes">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-user"></i> Cliente</th>
                        <th><i class="fas fa-clipboard-list"></i> Total Órdenes</th>
                        <th><i class="fas fa-dollar-sign"></i> Total Facturado</th>
                        <th><i class="fas fa-clock"></i> Promedio por Orden</th>
                        <th><i class="fas fa-calendar"></i> Última Orden</th>
                        <th><i class="fas fa-chart-pie"></i> % del Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    console.log('=== VISTA ÓRDENES CLIENTE INICIADA ===');
    console.log('Tabla encontrada:', $('#tablaOrdenesCliente').length > 0);
    
    inicializarValidacionesFechas();
    
    // Verificar si la tabla se inicializó correctamente
    setTimeout(function() {
        if (window.tablaOrdenesCliente) {
            console.log('Tabla inicializada correctamente');
        } else {
            console.error('Tabla NO inicializada');
        }
    }, 1000);
});
</script>
<?= $this->endSection() ?> 