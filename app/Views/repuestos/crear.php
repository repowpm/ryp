<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-cogs"></i> Crear Nuevo Repuesto
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
                <form id="formCrearRepuesto" method="post" action="<?= base_url('repuestos/guardar') ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre del Repuesto *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   placeholder="Ej: Filtro de aceite" maxlength="100">
                            <div class="invalid-feedback" id="nombre-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Seleccione una categoría</option>
                                <option value="Filtros">Filtros</option>
                                <option value="Aceites">Aceites</option>
                                <option value="Frenos">Frenos</option>
                                <option value="Suspensión">Suspensión</option>
                                <option value="Motor">Motor</option>
                                <option value="Transmisión">Transmisión</option>
                                <option value="Eléctrico">Eléctrico</option>
                                <option value="Carrocería">Carrocería</option>
                                <option value="Herramientas">Herramientas</option>
                                <option value="Otros">Otros</option>
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
                                       placeholder="0" min="1" step="1">
                            </div>
                            <div class="invalid-feedback" id="precio-error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control" id="stock" name="stock" required 
                                   placeholder="0" min="0" step="1">
                            <div class="invalid-feedback" id="stock-error"></div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> El ID del repuesto se generará automáticamente usando el trigger de la base de datos.
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="window.location.href='<?= base_url('repuestos') ?>'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Repuesto
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
                <h6>Generación Automática</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-id-card text-info"></i> ID del repuesto</li>
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
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.getElementById('formCrearRepuesto');
    
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Formatear precio automáticamente para mostrar solo enteros
    const precioInput = document.getElementById('precio');
    precioInput.addEventListener('input', function() {
        let value = this.value;
        if (value && !isNaN(value)) {
            this.value = parseInt(value) || 0;
        }
    });

    // Validar stock para mostrar solo enteros
    const stockInput = document.getElementById('stock');
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