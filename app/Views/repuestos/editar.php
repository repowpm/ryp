<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit"></i> Editar Repuesto
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('repuestos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información del Repuesto</h5>
            </div>
            <div class="card-body">
                <form id="formEditarRepuesto" method="post" action="<?= base_url('repuestos/actualizar/' . $repuesto['id_repuesto']) ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Repuesto *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   value="<?= esc($repuesto['nombre']) ?>" placeholder="Ej: Filtro de aceite" maxlength="100">
                            <div class="invalid-feedback" id="nombre-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Seleccione una categoría</option>
                                <option value="Filtros" <?= ($repuesto['categoria'] == 'Filtros') ? 'selected' : '' ?>>Filtros</option>
                                <option value="Aceites" <?= ($repuesto['categoria'] == 'Aceites') ? 'selected' : '' ?>>Aceites</option>
                                <option value="Frenos" <?= ($repuesto['categoria'] == 'Frenos') ? 'selected' : '' ?>>Frenos</option>
                                <option value="Suspensión" <?= ($repuesto['categoria'] == 'Suspensión') ? 'selected' : '' ?>>Suspensión</option>
                                <option value="Motor" <?= ($repuesto['categoria'] == 'Motor') ? 'selected' : '' ?>>Motor</option>
                                <option value="Transmisión" <?= ($repuesto['categoria'] == 'Transmisión') ? 'selected' : '' ?>>Transmisión</option>
                                <option value="Eléctrico" <?= ($repuesto['categoria'] == 'Eléctrico') ? 'selected' : '' ?>>Eléctrico</option>
                                <option value="Carrocería" <?= ($repuesto['categoria'] == 'Carrocería') ? 'selected' : '' ?>>Carrocería</option>
                                <option value="Herramientas" <?= ($repuesto['categoria'] == 'Herramientas') ? 'selected' : '' ?>>Herramientas</option>
                                <option value="Otros" <?= ($repuesto['categoria'] == 'Otros') ? 'selected' : '' ?>>Otros</option>
                            </select>
                            <div class="invalid-feedback" id="categoria-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="precio" name="precio" required 
                                       value="<?= esc(intval($repuesto['precio'])) ?>" placeholder="0" min="1" step="1">
                            </div>
                            <div class="invalid-feedback" id="precio-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control" id="stock" name="stock" required 
                                   value="<?= esc(intval($repuesto['stock'])) ?>" placeholder="0" min="0" step="1">
                            <div class="invalid-feedback" id="stock-error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="activo" <?= ($repuesto['deleted_at'] === null) ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= ($repuesto['deleted_at'] !== null) ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                            <div class="invalid-feedback" id="estado-error"></div>
                        </div>
                        <div class="col-md-6 mb-3" id="motivo-container" style="display: none;">
                            <label for="motivo_stock" class="form-label">Motivo del cambio de stock</label>
                            <textarea class="form-control" id="motivo_stock" name="motivo_stock" rows="2" 
                                      placeholder="Ej: Ajuste de inventario, Corrección de conteo, etc."></textarea>
                            <div class="invalid-feedback" id="motivo_stock-error"></div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Los cambios se guardarán inmediatamente al hacer clic en "Actualizar Repuesto".
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('repuestos') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Repuesto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información</h5>
            </div>
            <div class="card-body">
                <h6>Campos Requeridos</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-asterisk text-danger"></i> Nombre del Repuesto</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Precio</li>
                    <li><i class="fas fa-asterisk text-danger"></i> Stock</li>
                </ul>
                <hr>
                <h6>Información del Repuesto</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-id-card text-info"></i> ID: <?= esc($repuesto['id_repuesto']) ?></li>
                    <li><i class="fas fa-calendar text-info"></i> Creado: <?= date('d/m/Y', strtotime($repuesto['created_at'])) ?></li>
                    <li><i class="fas fa-edit text-info"></i> Última actualización: <?= date('d/m/Y', strtotime($repuesto['updated_at'])) ?></li>
                </ul>
                <hr>
                <h6>Categorías Disponibles</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-filter text-primary"></i> Filtros</li>
                    <li><i class="fas fa-oil-can text-primary"></i> Aceites</li>
                    <li><i class="fas fa-brake-system text-primary"></i> Frenos</li>
                    <li><i class="fas fa-car-side text-primary"></i> Suspensión</li>
                    <li><i class="fas fa-engine text-primary"></i> Motor</li>
                    <li><i class="fas fa-cog text-primary"></i> Transmisión</li>
                    <li><i class="fas fa-bolt text-primary"></i> Eléctrico</li>
                    <li><i class="fas fa-car text-primary"></i> Carrocería</li>
                    <li><i class="fas fa-tools text-primary"></i> Herramientas</li>
                    <li><i class="fas fa-ellipsis-h text-primary"></i> Otros</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
console.log('=== SCRIPT DE EDICIÓN INICIADO ===');
console.log('URL actual:', window.location.href);
console.log('Estamos en la página de edición');

// Verificar elementos
const stockInput = document.getElementById('stock');
const motivoContainer = document.getElementById('motivo-container');
const motivoInput = document.getElementById('motivo_stock');

console.log('Elementos encontrados:', {
    stockInput: stockInput,
    motivoContainer: motivoContainer,
    motivoInput: motivoInput
});

if (stockInput && motivoContainer && motivoInput) {
    const stockOriginal = <?= intval($repuesto['stock']) ?>;
    console.log('Stock original:', stockOriginal);
    
    stockInput.addEventListener('input', function() {
        let value = parseInt(this.value) || 0;
        console.log('Stock cambiado a:', value);
        
        if (value !== stockOriginal) {
            console.log('MOSTRANDO CAMPO DE MOTIVO');
            motivoContainer.style.display = 'block';
            motivoInput.setAttribute('required', 'required');
        } else {
            console.log('OCULTANDO CAMPO DE MOTIVO');
            motivoContainer.style.display = 'none';
            motivoInput.removeAttribute('required');
            motivoInput.value = '';
        }
    });
    
    console.log('Event listener agregado correctamente');
} else {
    console.error('ERROR: No se encontraron todos los elementos necesarios');
}

// Formatear campos numéricos para mostrar solo enteros
document.addEventListener('DOMContentLoaded', function() {
    const precioInput = document.getElementById('precio');
    const stockInput = document.getElementById('stock');
    
    // Formatear precio para mostrar solo enteros
    precioInput.addEventListener('input', function() {
        let value = this.value;
        if (value && !isNaN(value)) {
            this.value = parseInt(value) || 0;
        }
    });
    
    // Formatear stock para mostrar solo enteros
    stockInput.addEventListener('input', function() {
        let value = parseInt(this.value) || 0;
        if (value < 0) {
            this.value = 0;
        } else {
            this.value = value;
        }
    });
});
</script>
<?= $this->endSection() ?> 