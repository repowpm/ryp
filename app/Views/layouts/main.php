<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Taller Rápido y Furioso' ?></title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <?php
        $segmento = service('uri')->getSegment(1);
        $cssModulos = ['usuarios','clientes','vehiculos','repuestos','ordenes','reportes'];
        if (in_array($segmento, $cssModulos)) {
            echo '<link rel="stylesheet" href="' . base_url('assets/css/' . $segmento . '.css') . '">';
        }
    ?>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
        }
        
        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .navbar-nav .nav-link {
            color: #495057 !important;
        }
        
        .navbar-nav .nav-link:hover {
            color: #007bff !important;
        }
        
        .sidebar {
            min-height: calc(100vh - 60px);
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
        }
        
        .main-content {
            background-color: #f8f9fa;
            min-height: 0;
            color: #212529;
        }
        
        .footer {
            background-color: #ffffff;
            color: #6c757d;
            padding: 1rem 0;
            margin-top: auto;
            border-top: 1px solid #dee2e6;
        }
        
        .card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            color: #212529;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .table {
            color: #212529;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
        }
        
        .table tbody tr {
            background-color: #ffffff;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>
    <div class="container-fluid flex-fill">
        <div class="row">
            <!-- Main Content -->
            <main class="col-12 px-md-4 main-content">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    <!-- Footer -->
    <?= $this->include('components/footer') ?>
    <!-- jQuery con fallback -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        if (typeof jQuery === 'undefined') {
            // Fallback si el primer CDN falla
            document.write('<script src="https://code.jquery.com/jquery-3.7.1.min.js"><\/script>');
        }
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Moment.js para formateo de fechas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/es.js"></script>
    <!-- DataTables JS (después de jQuery) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 Notifications -->
    <?php if (session()->getFlashdata('swal')): ?>
    <script>
        Swal.fire(<?= json_encode(session()->getFlashdata('swal')) ?>);
    </script>
    <?php endif; ?>
    <script>
        var base_url = '<?= base_url() ?>';
        var csrf_token_name = '<?= csrf_token() ?>';
        var csrf_token_value = '<?= csrf_hash() ?>';
    </script>
    <!-- Scripts específicos de cada módulo -->
    <?php
        $uri = service('uri');
        $jsModulos = ['usuarios','clientes','vehiculos','repuestos','reportes'];
        $segmentos = [];
        for ($i = 1; $i <= $uri->getTotalSegments(); $i++) {
            $segmentos[] = $uri->getSegment($i);
        }
        foreach ($jsModulos as $modulo) {
            if (in_array($modulo, $segmentos)) {
                echo '<script src="' . base_url('assets/js/' . $modulo . '.js') . '"></script>';
            }
        }
    ?>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <!-- Scripts adicionales específicos de la vista -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>