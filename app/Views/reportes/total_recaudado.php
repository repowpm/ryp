<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-dollar-sign"></i> Total Recaudado
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
            <i class="fas fa-chart-bar text-success"></i>
            Análisis de Ingresos Totales
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
                        <button type="button" class="btn btn-primary" onclick="filtrarTotalRecaudado()">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltrosTotalRecaudado()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de ingresos -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 id="totalIngresos">$0</h4>
                        <small>Total Ingresos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 id="promedioDiario">$0</h4>
                        <small>Promedio Diario</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 id="totalOrdenes">0</h4>
                        <small>Total Órdenes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4 id="promedioOrden">$0</h4>
                        <small>Promedio por Orden</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de ingresos -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-line"></i> Evolución de Ingresos
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoIngresos" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> Distribución por Mes
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoDistribucion" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de ingresos detallados -->
        <div class="table-responsive">
            <table id="tablaTotalRecaudado" class="table table-striped table-hover table-reportes">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-calendar"></i> Período</th>
                        <th><i class="fas fa-clipboard-list"></i> Órdenes</th>
                        <th><i class="fas fa-dollar-sign"></i> Ingresos</th>
                        <th><i class="fas fa-chart-line"></i> Promedio Diario</th>
                        <th><i class="fas fa-clock"></i> Promedio por Orden</th>
                        <th><i class="fas fa-percentage"></i> % del Total</th>
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
// Variables globales para los gráficos
var graficoIngresos = null;
var graficoDistribucion = null;

// Funciones para actualizar resumen y gráficos
function actualizarResumen(data) {
    if (data && data.length > 0) {
        let totalIngresos = 0;
        let totalOrdenes = 0;
        
        data.forEach(function(item) {
            totalIngresos += parseFloat(item.ingresos || 0);
            totalOrdenes += parseInt(item.ordenes || 0);
        });
        
        let promedioDiario = totalIngresos / data.length;
        let promedioOrden = totalOrdenes > 0 ? totalIngresos / totalOrdenes : 0;
        
        $('#totalIngresos').text('$' + parseInt(totalIngresos).toLocaleString('es-ES'));
        $('#promedioDiario').text('$' + parseInt(promedioDiario).toLocaleString('es-ES'));
        $('#totalOrdenes').text(totalOrdenes);
        $('#promedioOrden').text('$' + parseInt(promedioOrden).toLocaleString('es-ES'));
    }
}

function actualizarGraficos(data) {
    // Gráfico de línea - Evolución de ingresos
    const ctxLine = document.getElementById('graficoIngresos');
    if (ctxLine) {
        if (graficoIngresos) {
            graficoIngresos.destroy();
        }
        
        const labels = data.map(item => item.periodo);
        const ingresos = data.map(item => parseInt(item.ingresos || 0));
        
        graficoIngresos = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ingresos,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de dona - Distribución por mes
    const ctxDona = document.getElementById('graficoDistribucion');
    if (ctxDona) {
        if (graficoDistribucion) {
            graficoDistribucion.destroy();
        }
        
        const labels = data.map(item => item.periodo);
        const ingresos = data.map(item => parseInt(item.ingresos || 0));
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
        ];
        
        graficoDistribucion = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: ingresos,
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
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
}

function filtrarTotalRecaudado() {
    if (window.tablaTotalRecaudado) {
        window.tablaTotalRecaudado.ajax.reload();
    }
}

function limpiarFiltrosTotalRecaudado() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    filtrarTotalRecaudado();
}
</script>
<?= $this->endSection() ?> 