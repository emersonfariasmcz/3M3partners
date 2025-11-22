<?php
# ========================================
# Formulário de Criação de Fornecedor
# Local: /app/views/fornecedores/create.php
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

// Verificação adicionada para evitar erro caso $estados não seja definida
if (!isset($estados)) {
    $_SESSION['erro_salvar'] = "Erro ao carregar estados.";
    header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Fornecedor - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10"> <!-- Aumentado o tamanho para acomodar mais campos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Novo Fornecedor
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/fornecedor_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Nome Fantasia -->
                                <div class="col-md-12">
                                    <label for="fornecedor_nome" class="form-label">Nome Fantasia *</label>
                                    <input type="text" class="form-control" id="fornecedor_nome" name="fornecedor_nome" required maxlength="130">
                                    <div class="invalid-feedback">Por favor, informe o nome fantasia do fornecedor.</div>
                                </div>

                                <!-- Razão Social -->
                                <div class="col-md-12">
                                    <label for="fornecedor_razaosocial" class="form-label">Razão Social</label>
                                    <input type="text" class="form-control" id="fornecedor_razaosocial" name="fornecedor_razaosocial" maxlength="150">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CNPJ -->
                                <div class="col-md-6">
                                    <label for="fornecedor_cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control cnpj-input" id="fornecedor_cnpj" name="fornecedor_cnpj" maxlength="18"> <!-- Máscara JS -->
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-6">
                                    <label for="fornecedor_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone-input" id="fornecedor_telefone" name="fornecedor_telefone" maxlength="20"> <!-- Máscara JS -->
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-12">
                                    <label for="fornecedor_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="fornecedor_email" name="fornecedor_email" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-8">
                                    <label for="fornecedor_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="fornecedor_endereco" name="fornecedor_endereco" maxlength="200">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Bairro -->
                                <div class="col-md-4">
                                    <label for="fornecedor_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="fornecedor_bairro" name="fornecedor_bairro" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-6">
                                    <label for="fornecedor_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="fornecedor_cidade" name="fornecedor_cidade" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="fornecedor_estado_id" class="form-label">Estado</label>
                                    <select class="form-select" id="fornecedor_estado_id" name="fornecedor_estado_id">
                                        <option value="">Selecione um estado...</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['estado_id']) ?>">
                                                <?= htmlspecialchars($estado['estado_nome']) ?> (<?= htmlspecialchars($estado['estado_uf']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/fornecedor_controller.php?acao=listar" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script simples para máscaras (pode ser substituído por uma biblioteca mais robusta) -->
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

        // Máscara para CNPJ
        document.addEventListener('DOMContentLoaded', function() {
            const cnpjInputs = document.querySelectorAll('.cnpj-input');
            cnpjInputs.forEach(function(input) {
                input.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 14) value = value.substring(0, 14);
                    let formattedValue = '';
                    if (value.length > 2) formattedValue += value.substring(0, 2) + '.';
                    if (value.length > 5) formattedValue += value.substring(2, 5) + '.';
                    if (value.length > 8) formattedValue += value.substring(5, 8) + '/';
                    if (value.length > 12) formattedValue += value.substring(8, 12) + '-';
                    formattedValue += value.substring(12);
                    e.target.value = formattedValue;
                });
            });

            // Máscara para Telefone (simples, pode ser aprimorada)
             const telefoneInputs = document.querySelectorAll('.telefone-input');
             telefoneInputs.forEach(function(input) {
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     // Limita a 11 dígitos (para celular) ou 10 (fixo)
                     // if (value.length > 11) value = value.substring(0, 11);
                     let formattedValue = '';
                     if (value.length > 0) formattedValue = '(' + value.substring(0, 2);
                     if (value.length >= 2) formattedValue += ') ' + value.substring(2, 7);
                     if (value.length >= 7) formattedValue += '-' + value.substring(7, 11);
                     // Adiciona mais um dígito se for celular (11 dígitos)
                     if (value.length > 11) formattedValue += value.substring(11, 12);
                     e.target.value = formattedValue;
                 });
             });
        });
    </script>
</body>
</html>