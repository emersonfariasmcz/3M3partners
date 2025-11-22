<?php
# ========================================
# Formulário de Edição de Unidade de Medida
# Local: /app/views/unidademedidas/edit.php
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

// Verificações para evitar erro - Garante que o formulário só seja acessado via controller
if (!isset($unidademedida)) {
    $_SESSION['erro_salvar'] = "Unidade de medida não encontrada.";
    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Unidade de Medida - Sistema IGA</title>

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
                            <i class="fas fa-edit me-2"></i>Editar Unidade de Medida
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

                        <form action="/iga/app/controllers/unidademedida_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto para identificar a unidade de medida a ser atualizada -->
                            <input type="hidden" name="unidademedida_id" value="<?= htmlspecialchars($unidademedida['unidademedida_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome da Unidade de Medida * -->
                                <div class="col-md-12">
                                    <label for="unidademedida_nome" class="form-label">Nome da Unidade de Medida *</label>
                                    <input type="text" class="form-control" id="unidademedida_nome" name="unidademedida_nome"
                                        value="<?= htmlspecialchars($unidademedida['unidademedida_nome'] ?? '') ?>" required maxlength="50">
                                    <div class="invalid-feedback">Por favor, informe o nome da unidade de medida.</div>
                                </div>

                                <!-- Sigla * -->
                                <div class="col-md-6">
                                    <label for="unidademedida_sigla" class="form-label">Sigla *</label>
                                    <input type="text" class="form-control" id="unidademedida_sigla" name="unidademedida_sigla"
                                        value="<?= htmlspecialchars($unidademedida['unidademedida_sigla'] ?? '') ?>" required maxlength="10">
                                    <div class="invalid-feedback">Por favor, informe a sigla da unidade de medida (ex: UN, KG, L).</div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-md-12">
                                    <label for="unidademedida_descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="unidademedida_descricao" name="unidademedida_descricao" rows="3" maxlength="255"><?= htmlspecialchars($unidademedida['unidademedida_descricao'] ?? '') ?></textarea>
                                    <div class="form-text">Opcional - máximo 255 caracteres</div>
                                </div>
                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/unidademedida_controller.php?acao=listar" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success">
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