<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-history"></i> Movimientos de Stock
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
            <i class="fas fa-chart-line text-primary"></i>
            Historial de Movimientos
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
                    <label for="id_repuesto" class="form-label">
                        <i class="fas fa-cogs"></i> Repuesto (Opcional)
                    </label>
                    <select class="form-control" id="id_repuesto" name="id_repuesto">
                        <option value="">Todos los repuestos</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-container">
                        <button type="button" class="btn btn-primary" onclick="filtrarMovimientos()">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de movimientos -->
        <div class="table-responsive">
            <table id="tablaMovimientos" class="table table-striped table-hover table-reportes">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-calendar"></i> Fecha</th>
                        <th><i class="fas fa-cogs"></i> Repuesto</th>
                        <th><i class="fas fa-exchange-alt"></i> Tipo</th>
                        <th><i class="fas fa-sort-numeric-up"></i> Cantidad</th>
                        <th><i class="fas fa-boxes"></i> Stock Anterior</th>
                        <th><i class="fas fa-boxes"></i> Stock Posterior</th>
                        <th><i class="fas fa-comment"></i> Motivo</th>
                        <th><i class="fas fa-user"></i> Usuario</th>
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
// Las funciones están ahora en reportes.js
// Solo agregamos funciones específicas de esta vista si es necesario
</script>
<?= $this->endSection() ?> 