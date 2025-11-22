<?php
# ========================================
# Formulário de Edição de Papel de Usuário
# Local: /app/views/usuariospapeis/edit.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

$sucesso = $_SESSION['sucesso_salvar'] ?? '';
unset($_SESSION['sucesso_salvar']);

// Verificações para evitar erro
if (!isset($papel)) {
    $_SESSION['erro_salvar'] = "Papel de usuário não encontrado.";
    header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Papel de Usuário - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Papel de Usuário
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

                         <?php if ($papel['usuariopapel_id'] == 1): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                O nome do papel "Administrador" é fixo e não pode ser alterado.
                            </div>
                        <?php endif; ?>


                        <form action="/iga/app/controllers/usuariopapel_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto -->
                            <input type="hidden" name="usuariopapel_id" value="<?= htmlspecialchars($papel['usuariopapel_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome do Papel -->
                                <div class="col-md-12">
                                    <label for="usuariopapel_nome" class="form-label">Nome do Papel *</label>
                                    <input type="text" class="form-control" id="usuariopapel_nome" name="usuariopapel_nome"
                                        value="<?= htmlspecialchars($papel['usuariopapel_nome'] ?? '') ?>"
                                        <?= ($papel['usuariopapel_id'] == 1) ? 'readonly' : 'required' ?> maxlength="50">
                                    <div class="invalid-feedback">Por favor, informe o nome do papel.</div>
                                    <?php if ($papel['usuariopapel_id'] == 1): ?>
                                        <div class="form-text">Este nome é fixo e não pode ser alterado.</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Descrição -->
                                <div class="col-md-12">
                                    <label for="usuariopapel_descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="usuariopapel_descricao" name="usuariopapel_descricao" rows="3" maxlength="255"><?= htmlspecialchars($papel['usuariopapel_descricao'] ?? '') ?></textarea>
                                    <div class="form-text">Opcional - máximo 255 caracteres</div>
                                </div>
                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/usuariopapel_controller.php?acao=listar" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success" <?= ($papel['usuariopapel_id'] == 1) ? 'disabled' : '' ?>>
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