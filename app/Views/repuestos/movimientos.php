<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-exchange-alt"></i> Movimientos de Stock
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= base_url('repuestos') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="card card-repuestos">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter"></i> Filtros
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
            </div>
            <div class="col-md-3">
                <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-t') ?>">
            </div>
            <div class="col-md-3">
                <label for="repuesto_filter" class="form-label">Repuesto:</label>
                <select id="repuesto_filter" class="form-control">
                    <option value="">Todos los repuestos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary d-block" onclick="filtrarMovimientos()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card card-repuestos">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaMovimientos" class="table table-striped table-hover table-repuestos">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Repuesto</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Stock Anterior</th>
                        <th>Stock Nuevo</th>
                        <th>Valor</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    console.log('=== MOVIMIENTOS.JS INICIADO ===');
    
    // Inicializar DataTable SOLO PARA DEBUG
    var tablaMovimientos = $('#tablaMovimientos').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: base_url + 'repuestos/movimientosStock',
            type: 'GET',
            data: function(d) {
                return {
                    fecha_inicio: $('#fecha_inicio').val(),
                    fecha_fin: $('#fecha_fin').val(),
                    id_repuesto: $('#repuesto_filter').val()
                };
            },
            dataSrc: function(json) {
                console.log('Datos recibidos:', json);
                console.log('Estructura de datos:', JSON.stringify(json.data, null, 2));
                if (json.data && json.data.length > 0) {
                    console.log('Primer elemento:', json.data[0]);
                }
                return json.data || [];
            }
        },
        columns: [
            {
                data: null,
                render: function(data, type, row) {
                    return '<pre>' + JSON.stringify(row, null, 2) + '</pre>';
                },
                title: 'DEBUG JSON'
            }
        ],
        responsive: true,
        language: {
            url: base_url + 'assets/js/es-ES.json'
        },
        error: function(xhr, error, thrown) {
            console.error('Error en DataTable:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });
    
    // Cargar lista de repuestos para el filtro
    cargarRepuestos();
    
    // Función para cargar repuestos en el filtro
    function cargarRepuestos() {
        $.ajax({
            url: base_url + 'repuestos/getRepuestos',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var select = $('#repuesto_filter');
                    select.empty();
                    select.append('<option value="">Todos los repuestos</option>');
                    
                    response.data.forEach(function(repuesto) {
                        select.append('<option value="' + repuesto.id_repuesto + '">' + repuesto.nombre + '</option>');
                    });
                }
            },
            error: function() {
                console.error('Error al cargar repuestos');
            }
        });
    }
    
    // Función para filtrar movimientos
    window.filtrarMovimientos = function() {
        console.log('Filtrando movimientos...');
        tablaMovimientos.ajax.reload();
    };
    
    console.log('DataTable inicializado correctamente');
});
</script>
<?= $this->endSection() ?> 