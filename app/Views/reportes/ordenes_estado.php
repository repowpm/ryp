<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tasks"></i> Órdenes por Estado
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
            <i class="fas fa-chart-pie text-warning"></i>
            Distribución de Órdenes por Estado
        </h5>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="filtros-reportes">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fecha_inicio" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Fecha Inicio
                    </label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fecha_fin" class="form-label">
                        <i class="fas fa-calendar-alt"></i> Fecha Fin
                    </label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="<?= date('Y-m-t') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-container">
                        <button type="button" class="btn btn-primary" onclick="filtrarOrdenesEstado()">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltrosOrdenesEstado()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de distribución -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> Distribución por Estado
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoEstados" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Resumen
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="resumenEstados">
                            <!-- El resumen se cargará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de órdenes por estado -->
        <div class="table-responsive">
            <table id="tablaOrdenesEstado" class="table table-striped table-hover table-reportes">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-tasks"></i> Estado</th>
                        <th><i class="fas fa-clipboard-list"></i> Cantidad</th>
                        <th><i class="fas fa-dollar-sign"></i> Total Facturado</th>
                        <th><i class="fas fa-chart-pie"></i> Porcentaje</th>
                        <th><i class="fas fa-clock"></i> Promedio por Orden</th>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    inicializarValidacionesFechas();
});

var graficoEstados = null;

function actualizarGrafico(data) {
    const ctx = document.getElementById('graficoEstados').getContext('2d');
    
    if (graficoEstados) {
        graficoEstados.destroy();
    }
    
    const labels = data.map(item => item.estado);
    const values = data.map(item => parseInt(item.cantidad));
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
    ];
    
    graficoEstados = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function actualizarResumen(data) {
    const totalOrdenes = data.reduce((sum, item) => sum + parseInt(item.cantidad), 0);
    const totalFacturado = data.reduce((sum, item) => sum + parseFloat(item.total_facturado || 0), 0);
    
    let html = `
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-primary">${totalOrdenes}</h4>
                <small class="text-muted">Total Órdenes</small>
            </div>
            <div class="col-6">
                <h4 class="text-success">$${parseInt(totalFacturado).toLocaleString('es-CL')}</h4>
                <small class="text-muted">Total Facturado</small>
            </div>
        </div>
    `;
    
    $('#resumenEstados').html(html);
}

function filtrarOrdenesEstado() {
    if (window.tablaOrdenesEstado) {
        window.tablaOrdenesEstado.ajax.reload();
    }
}

function limpiarFiltrosOrdenesEstado() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    filtrarOrdenesEstado();
}
</script>
<?= $this->endSection() ?> 