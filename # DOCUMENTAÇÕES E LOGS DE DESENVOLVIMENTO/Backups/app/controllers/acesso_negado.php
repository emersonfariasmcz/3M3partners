<?php
# ========================================
# Página de Acesso Negado
# Local: /app/controllers/acesso_negado.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erro_acesso = $_SESSION['erro_acesso'] ?? 'Você não tem permissão para acessar esta funcionalidade.';
unset($_SESSION['erro_acesso']);

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_papel = $_SESSION['usuario_papel'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - Sistema IGA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-ban me-2"></i>Acesso Negado
                        </h4>
                    </div>
                    
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-lock fa-4x text-danger mb-3"></i>
                            <h5 class="text-danger">Restrição de Acesso</h5>
                        </div>
                        
                        <p class="lead">Olá, <strong><?= htmlspecialchars($usuario_nome) ?></strong>!</p>
                        <p>Seu perfil atual: <span class="badge bg-secondary"><?= htmlspecialchars($usuario_papel) ?></span></p>
                        
                        <div class="alert alert-warning mt-4">
                            <p class="mb-0"><?= htmlspecialchars($erro_acesso) ?></p>
                        </div>
                        
                        <p class="text-muted mt-3">
                            Esta funcionalidade está restrita aos administradores do sistema. 
                            Contacte o administrador para solicitar acesso.
                        </p>
                    </div>
                    
                    <div class="card-footer bg-light text-center">
                        <a href="/iga/app/views/dashboard.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>