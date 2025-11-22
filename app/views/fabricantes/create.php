<?php
# ========================================
# Formulário de Criação de Fabricante
# Local: /app/views/fabricantes/create.php
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Fabricante - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-industry me-2"></i>Novo Fabricante
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/fabricante_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-12">
                                    <label for="fabricante_nome" class="form-label">Nome *</label>
                                    <input type="text" class="form-control" id="fabricante_nome" name="fabricante_nome" required maxlength="100">
                                    <div class="invalid-feedback">Por favor, informe o nome do fabricante.</div>
                                </div>

                                <!-- CNPJ -->
                                <div class="col-md-6">
                                    <label for="fabricante_cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control cnpj-input" id="fabricante_cnpj" name="fabricante_cnpj" maxlength="18">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-6">
                                    <label for="fabricante_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone-input" id="fabricante_telefone" name="fabricante_telefone" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-12">
                                    <label for="fabricante_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="fabricante_email" name="fabricante_email" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-8">
                                    <label for="fabricante_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="fabricante_endereco" name="fabricante_endereco" maxlength="255">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-4">
                                    <label for="fabricante_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="fabricante_cidade" name="fabricante_cidade" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="fabricante_estado" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="fabricante_estado" name="fabricante_estado" maxlength="50">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CEP -->
                                <div class="col-md-6">
                                    <label for="fabricante_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control cep-input" id="fabricante_cep" name="fabricante_cep" maxlength="10">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-12">
                                    <label for="fabricante_status" class="form-label">Status *</label>
                                    <select class="form-select" id="fabricante_status" name="fabricante_status" required>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                    <div class="invalid-feedback">Por favor, selecione o status.</div>
                                </div>

                                <!-- Observações -->
                                <div class="col-md-12">
                                    <label for="fabricante_observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="fabricante_observacoes" name="fabricante_observacoes" rows="3" maxlength="500"></textarea>
                                    <div class="form-text">Opcional - máximo 500 caracteres</div>
                                </div>

                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/fabricante_controller.php?acao=listar" class="btn btn-secondary">
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

        // Máscaras simples
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para CNPJ
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

            // Máscara para Telefone
             const telefoneInputs = document.querySelectorAll('.telefone-input');
             telefoneInputs.forEach(function(input) {
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

            // Máscara para CEP
             const cepInputs = document.querySelectorAll('.cep-input');
             cepInputs.forEach(function(input) {
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     if (value.length > 8) value = value.substring(0, 8);
                     let formattedValue = '';
                     if (value.length > 5) formattedValue = value.substring(0, 5) + '-' + value.substring(5);
                     else formattedValue = value;
                     e.target.value = formattedValue;
                 });
             });
        });
    </script>
</body>
</html>