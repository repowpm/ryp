<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
            <i class="fas fa-tools"></i> Taller Rápido y Furioso
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'ordenes') !== false ? 'active' : '' ?>" href="<?= base_url('ordenes') ?>">
                        <i class="fas fa-clipboard-list"></i> Órdenes de Trabajo
                    </a>
                </li>
                <?php if (puede_ver_repuestos()): // Administradores y Mecánicos ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'repuestos') !== false ? 'active' : '' ?>" href="<?= base_url('repuestos') ?>">
                        <i class="fas fa-cogs"></i> Repuestos
                    </a>
                </li>
                <?php endif; ?>
                <?php if (session()->get('id_rol') == 1): // Solo Administrador ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'clientes') !== false ? 'active' : '' ?>" href="<?= base_url('clientes') ?>">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'vehiculos') !== false ? 'active' : '' ?>" href="<?= base_url('vehiculos') ?>">
                        <i class="fas fa-car"></i> Vehículos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'usuarios') !== false ? 'active' : '' ?>" href="<?= base_url('usuarios') ?>">
                        <i class="fas fa-user-cog"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(current_url(), 'reportes') !== false ? 'active' : '' ?>" href="<?= base_url('reportes') ?>">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?= session()->get('username') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted">
                            <i class="fas fa-user-tag"></i> 
                            <?= session()->get('rol_nombre') ?>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav> 