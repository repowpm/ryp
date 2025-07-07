<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card login-card">
    <div class="card-body">
        <form action="<?= base_url('auth/login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?> 