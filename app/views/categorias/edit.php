<?php
# ========================================
# Formulário de Edição de Categoria
# Local: /app/views/categorias/edit.php
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
    header('Location: /iga/app/controllers/acesso_negado.php');
    exit;
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

if (!isset($categoria)) {
    $_SESSION['erro_salvar'] = "Categoria não encontrada.";
    header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria - Sistema IGA</title>
    
    <link href="极速cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <i class="fas fa-edit me-2"></i>Editar Categoria
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/categoria_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="categoria_id" value="<?= htmlspecialchars($categoria['categoria_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome da Categoria -->
                                <div class="col-md-12">
                                    <label for="categoria_nome" class="form-label">Nome da Categoria *</label>
                                    <input type="text" class="form-control" id="categoria_nome" name="categoria_nome" 
                                           value="<?= htmlspecialchars($categoria['categoria_nome']) ?>" required maxlength="100">
                                    <div class="invalid-feedback">
                                        Por favor, informe o nome da categoria.
                                    </div>
                                </div>
                                
                                <!-- Descrição -->
                                <div class="col-md-12">
                                    <label for="categoria_descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="categoria_descricao" name="categoria_descricao" 
                                              rows="3" maxlength="500"><?= htmlspecialchars($categoria['categoria_descricao'] ?? '') ?></textarea>
                                    <div class="form-text">Opcional - máximo 500 caracteres</div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-12">
                                    <label for="categoria_status" class="form-label">Status *</label>
                                    <select class="form-select" id="categoria_status" name="categoria_status" required>
                                        <option value="ativo" <?= $categoria['categoria_status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                        <option value="inativo" <?= $categoria['categoria_status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecione o status.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/categoria_controller.php?acao=listar" class="btn btn-secondary">
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
        // Validação do formulário
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