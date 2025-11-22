<?php
# ========================================
# Formulário de Edição de Distrito
# Local: /app/views/distritos/edit.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../app/views/login.php');
    exit;
}

if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: ../../app/views/acesso_negado.php');
    exit;
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

$sucesso = $_SESSION['sucesso_salvar'] ?? '';
unset($_SESSION['sucesso_salvar']);

// Verificações para evitar erro
if (!isset($distrito)) {
    $_SESSION['erro_salvar'] = "Distrito não encontrado.";
    header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Distrito - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Distrito
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Exibe mensagem de sucesso, se houver -->
                        <?php if ($sucesso): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($sucesso) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <!-- Exibe mensagem de erro, se houver -->
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                         <?php if ($distrito['distrito_id'] == 1): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                O nome do distrito "SEM DISTRITO" é fixo e não pode ser alterado.
                            </div>
                        <?php endif; ?>


                        <form action="../../app/controllers/distrito_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto -->
                            <input type="hidden" name="distrito_id" value="<?= htmlspecialchars($distrito['distrito_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-12">
                                    <label for="distrito_nome" class="form-label">Nome do Distrito *</label>
                                    <input type="text" class="form-control" id="distrito_nome" name="distrito_nome"
                                        value="<?= htmlspecialchars($distrito['distrito_nome'] ?? '') ?>"
                                        <?= ($distrito['distrito_id'] == 1) ? 'readonly' : 'required' ?> maxlength="100">
                                    <div class="invalid-feedback">Por favor, informe o nome do distrito.</div>
                                    <?php if ($distrito['distrito_id'] == 1): ?>
                                        <div class="form-text">Este nome é fixo e não pode ser alterado.</div>
                                    <?php endif; ?>
                                </div>
                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../app/controllers/distrito_controller.php?acao=listar" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success" <?= ($distrito['distrito_id'] == 1) ? 'disabled' : '' ?>>
                                    <i class="fas fa-save me-1"></i> Atualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário Bootstrap
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>