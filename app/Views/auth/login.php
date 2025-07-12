<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="login-form">
    <form action="<?= base_url('auth/login') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>
        
        <div class="mb-4">
            <label for="username" class="form-label">
                <i class="fas fa-user me-2"></i>Usuario
            </label>
            <div class="input-group input-group-lg">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       required 
                       autofocus 
                       placeholder="Ingrese su usuario"
                       autocomplete="username">
            </div>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-2"></i>Contraseña
            </label>
            <div class="input-group input-group-lg" id="show_hide_password">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Ingrese su contraseña"
                       autocomplete="current-password">
                <button class="btn btn-outline-secondary" 
                        type="button" 
                        id="togglePassword" 
                        tabindex="-1"
                        title="Mostrar/Ocultar contraseña">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>
        
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>
                Iniciar Sesión
            </button>
        </div>
        
        <div class="text-center">
            <small class="text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                Acceso seguro al sistema
            </small>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.title = 'Ocultar contraseña';
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.title = 'Mostrar contraseña';
                }
            });
        }
        
        // Efecto de focus mejorado
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    });
</script>
<?= $this->endSection() ?> 