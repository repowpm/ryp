<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-cogs"></i> Repuestos Más Utilizados
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
            <i class="fas fa-trophy text-danger"></i>
            Ranking de Repuestos con Mayor Demanda
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
                    <label for="limite" class="form-label">
                        <i class="fas fa-list-ol"></i> Top N
                    </label>
                    <select class="form-control" id="limite" name="limite">
                        <option value="10">Top 10</option>
                        <option value="20">Top 20</option>
                        <option value="50">Top 50</option>
                        <option value="100">Top 100</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-container">
                        <button type="button" class="btn btn-primary" onclick="filtrarRepuestosUtilizados()">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltrosRepuestosUtilizados()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de repuestos -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4 id="totalRepuestos">0</h4>
                        <small>Total Repuestos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4 id="totalCantidad">0</h4>
                        <small>Total Cantidad</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 id="totalValor">$0</h4>
                        <small>Total Valor</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 id="promedioUso">0</h4>
                        <small>Promedio por Repuesto</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de barras -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar"></i> Ranking de Repuestos
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoRepuestos" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> Distribución por Categoría
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoCategorias" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de repuestos más utilizados -->
        <div class="table-responsive">
            <table id="tablaRepuestosUtilizados" class="table table-striped table-hover table-reportes">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-trophy"></i> Ranking</th>
                        <th><i class="fas fa-cogs"></i> Repuesto</th>
                        <th><i class="fas fa-tag"></i> Categoría</th>
                        <th><i class="fas fa-sort-numeric-up"></i> Cantidad Utilizada</th>
                        <th><i class="fas fa-dollar-sign"></i> Valor Total</th>
                        <th><i class="fas fa-chart-line"></i> Promedio por Orden</th>
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
var graficoRepuestos = null;
var graficoCategorias = null;

// Funciones para actualizar resumen y gráficos
function actualizarResumenRepuestos(data) {
    if (data && data.length > 0) {
        let totalRepuestos = data.length;
        let totalCantidad = 0;
        let totalValor = 0;
        
        data.forEach(function(item) {
            totalCantidad += parseInt(item.cantidad_utilizada || 0);
            totalValor += parseFloat(item.valor_total || 0);
        });
        
        let promedioUso = totalRepuestos > 0 ? totalCantidad / totalRepuestos : 0;
        
        $('#totalRepuestos').text(totalRepuestos);
        $('#totalCantidad').text(totalCantidad.toLocaleString('es-ES'));
        $('#totalValor').text('$' + parseInt(totalValor).toLocaleString('es-ES'));
        $('#promedioUso').text(promedioUso.toFixed(1));
    }
}

function actualizarGraficosRepuestos(data) {
    // Gráfico de barras - Ranking de repuestos
    const ctxBarras = document.getElementById('graficoRepuestos');
    if (ctxBarras) {
        if (graficoRepuestos) {
            graficoRepuestos.destroy();
        }
        
        const labels = data.slice(0, 10).map(item => item.repuesto);
        const cantidades = data.slice(0, 10).map(item => parseInt(item.cantidad_utilizada || 0));
        
        graficoRepuestos = new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad Utilizada',
                    data: cantidades,
                    backgroundColor: '#dc3545',
                    borderColor: '#c82333',
                    borderWidth: 1
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
                                return value.toLocaleString('es-ES');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de dona - Distribución por categoría
    const ctxDona = document.getElementById('graficoCategorias');
    if (ctxDona) {
        if (graficoCategorias) {
            graficoCategorias.destroy();
        }
        
        // Agrupar por categoría
        const categorias = {};
        data.forEach(function(item) {
            const categoria = item.categoria || 'Sin categoría';
            if (!categorias[categoria]) {
                categorias[categoria] = 0;
            }
            categorias[categoria] += parseInt(item.cantidad_utilizada || 0);
        });
        
        const labels = Object.keys(categorias);
        const valores = Object.values(categorias);
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
        ];
        
        graficoCategorias = new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
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

function filtrarRepuestosUtilizados() {
    if (window.tablaRepuestosUtilizados) {
        window.tablaRepuestosUtilizados.ajax.reload();
    }
}

function limpiarFiltrosRepuestosUtilizados() {
    $('#fecha_inicio').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('#fecha_fin').val(moment().endOf('month').format('YYYY-MM-DD'));
    $('#limite').val('10');
    filtrarRepuestosUtilizados();
}
</script>
<?= $this->endSection() ?> 