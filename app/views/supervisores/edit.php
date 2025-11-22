<?php
# ========================================
# Formulário de Edição de Supervisor
# Local: /app/views/supervisores/edit.php
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
if (!isset($supervisor)) {
    $_SESSION['erro_salvar'] = "Supervisor não encontrado.";
    header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Supervisor - Sistema IGA</title>

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
                            <i class="fas fa-edit me-2"></i>Editar Supervisor
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

                        <form action="/iga/app/controllers/supervisor_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto -->
                            <input type="hidden" name="supervisor_id" value="<?= htmlspecialchars($supervisor['supervisor_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-12">
                                    <label for="supervisor_nome" class="form-label">Nome *</label>
                                    <input type="text" class="form-control" id="supervisor_nome" name="supervisor_nome"
                                        value="<?= htmlspecialchars($supervisor['supervisor_nome'] ?? '') ?>" required maxlength="150">
                                    <div class="invalid-feedback">Por favor, informe o nome do supervisor.</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-12">
                                    <label for="supervisor_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="supervisor_email" name="supervisor_email"
                                        value="<?= htmlspecialchars($supervisor['supervisor_email'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-12">
                                    <label for="supervisor_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone-input" id="supervisor_telefone" name="supervisor_telefone"
                                        value="<?= htmlspecialchars($supervisor['supervisor_telefone'] ?? '') ?>" maxlength="15">
                                    <div class="form-text">Opcional</div>
                                </div>
                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/supervisor_controller.php?acao=listar" class="btn btn-secondary">
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

        // Máscara simples para Telefone
        document.addEventListener('DOMContentLoaded', function() {
             const telefoneInputs = document.querySelectorAll('.telefone-input');
             telefoneInputs.forEach(function(input) {
                  // Aplica a máscara ao carregar a página, se houver valor
                 if (input.value) {
                     let value = input.value.replace(/\D/g, '');
                     let formattedValue = '';
                     if (value.length > 0) formattedValue = '(' + value.substring(0, 2);
                     if (value.length >= 2) formattedValue += ') ' + value.substring(2, 7);
                     if (value.length >= 7) formattedValue += '-' + value.substring(7, 11);
                     if (value.length > 11) formattedValue += value.substring(11, 12); // Para 9 dígitos
                     input.value = formattedValue;
                 }
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     let formattedValue = '';
                     if (value.length > 0) formattedValue = '(' + value.substring(0, 2);
                     if (value.length >= 2) formattedValue += ') ' + value.substring(2, 7);
                     if (value.length >= 7) formattedValue += '-' + value.substring(7, 11);
                     if (value.length > 11) formattedValue += value.substring(11, 12); // Para 9 dígitos
                     e.target.value = formattedValue;
                 });
             });
        });
    </script>
</body>
</html>