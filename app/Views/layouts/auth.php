<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Taller Rápido y Furioso - Acceso' ?></title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Efecto de ondas en el fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            animation: waveMove 20s ease-in-out infinite;
        }
        
        @keyframes waveMove {
            0%, 100% { transform: translateX(0) translateY(0) rotate(0deg); }
            25% { transform: translateX(-20px) translateY(-10px) rotate(1deg); }
            50% { transform: translateX(10px) translateY(-20px) rotate(-1deg); }
            75% { transform: translateX(-10px) translateY(10px) rotate(0.5deg); }
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            animation: slideInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            z-index: 10;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .auth-header h1 {
            color: #1e3a8a;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            animation: fadeInDown 0.8s ease-out 0.2s both;
            letter-spacing: -0.02em;
        }
        
        .auth-header p {
            color: #64748b;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        
        .form-control {
            background: #f8fafc !important;
            border: 2px solid #e2e8f0 !important;
            color: #1e293b !important;
            border-radius: 12px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            padding: 0.875rem 1rem;
        }
        
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
            background: #ffffff !important;
            transform: translateY(-1px);
        }
        
        .form-control::placeholder {
            color: #94a3b8 !important;
        }
        
        .input-group-text {
            background: #f1f5f9 !important;
            border: 2px solid #e2e8f0 !important;
            color: #64748b !important;
            border-radius: 12px !important;
            transition: all 0.3s ease;
        }
        
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px !important;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.875rem 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
            border: none !important;
            color: #ffffff !important;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-outline-secondary {
            background: transparent !important;
            border: 2px solid #cbd5e1 !important;
            color: #64748b !important;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #475569 !important;
            transform: scale(1.02);
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        
        .input-group-lg .form-control {
            font-size: 1rem;
            padding: 1rem 1.25rem;
        }
        
        .input-group-lg .input-group-text {
            padding: 1rem 1.25rem;
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Animaciones mejoradas */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(60px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .auth-container {
                margin: 1rem;
                padding: 2rem;
            }
            .auth-header h1 {
                font-size: 2rem;
            }
        }
        
        /* Efecto de brillo en hover para inputs */
        .input-group:hover .form-control {
            border-color: #cbd5e1;
        }
        
        .input-group:hover .input-group-text {
            border-color: #cbd5e1;
            background: #f8fafc !important;
        }
        
        /* Efecto de focus mejorado */
        .input-group.focused .form-control {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        }
        
        .input-group.focused .input-group-text {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
            color: #3b82f6 !important;
        }
        
        /* Estilos para el formulario de login */
        .login-form {
            animation: fadeIn 0.6s ease-out 0.6s both;
        }
        
        .login-form .form-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        
        .login-form .form-label i {
            color: #64748b;
        }
        
        /* Mejoras en el botón de mostrar/ocultar contraseña */
        .btn-outline-secondary {
            border-left: none !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        
        .btn-outline-secondary:hover {
            background: #f1f5f9 !important;
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
        
        /* Texto de seguridad */
        .text-muted {
            color: #94a3b8 !important;
            font-size: 0.875rem;
        }
        
        .text-muted i {
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-tools"></i> Taller</h1>
            <p>Rápido y Furioso</p>
        </div>
        
        <?= $this->renderSection('content') ?>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 Notifications -->
    <?php if (session()->getFlashdata('swal')): ?>
    <script>
        Swal.fire(<?= json_encode(session()->getFlashdata('swal')) ?>);
    </script>
    <?php endif; ?>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html> 