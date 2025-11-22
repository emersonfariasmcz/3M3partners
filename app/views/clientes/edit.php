<?php
# ========================================
# Formulário de Edição de Cliente
# Local: /app/views/clientes/edit.php
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
if (!isset($cliente) || !isset($estados)) {
    $_SESSION['erro_salvar'] = "Cliente ou dados necessários não encontrados.";
    header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Cliente
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

                        <form action="/iga/app/controllers/cliente_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto -->
                            <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente['cliente_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome/Razão Social * -->
                                <div class="col-md-12">
                                    <label for="cliente_nome" class="form-label">Nome/Razão Social *</label>
                                    <input type="text" class="form-control" id="cliente_nome" name="cliente_nome"
                                        value="<?= htmlspecialchars($cliente['cliente_nome'] ?? '') ?>" required maxlength="150">
                                    <div class="invalid-feedback">Por favor, informe o nome ou razão social do cliente.</div>
                                </div>

                                <!-- Nome Fantasia -->
                                <div class="col-md-6">
                                    <label for="cliente_nome_fantasia" class="form-label">Nome Fantasia</label>
                                    <input type="text" class="form-control" id="cliente_nome_fantasia" name="cliente_nome_fantasia"
                                        value="<?= htmlspecialchars($cliente['cliente_nome_fantasia'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CNPJ/CPF -->
                                <div class="col-md-6">
                                    <label for="cliente_cnpj_cpf" class="form-label">CNPJ/CPF</label>
                                    <input type="text" class="form-control cpf-cnpj-input" id="cliente_cnpj_cpf" name="cliente_cnpj_cpf"
                                        value="<?= !empty($cliente['cliente_cnpj_cpf']) ? htmlspecialchars($cliente['cliente_cnpj_cpf']) : '' ?>" maxlength="18">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Inscrição Estadual -->
                                <div class="col-md-6">
                                    <label for="cliente_inscricao_estadual" class="form-label">Inscrição Estadual</label>
                                    <input type="text" class="form-control" id="cliente_inscricao_estadual" name="cliente_inscricao_estadual"
                                        value="<?= htmlspecialchars($cliente['cliente_inscricao_estadual'] ?? '') ?>" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-12">
                                    <label for="cliente_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="cliente_endereco" name="cliente_endereco"
                                        value="<?= htmlspecialchars($cliente['cliente_endereco'] ?? '') ?>" maxlength="255">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Bairro -->
                                <div class="col-md-4">
                                    <label for="cliente_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="cliente_bairro" name="cliente_bairro"
                                        value="<?= htmlspecialchars($cliente['cliente_bairro'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-4">
                                    <label for="cliente_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="cliente_cidade" name="cliente_cidade"
                                        value="<?= htmlspecialchars($cliente['cliente_cidade'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-4">
                                    <label for="cliente_estado_id" class="form-label">Estado</label>
                                    <select class="form-select" id="cliente_estado_id" name="cliente_estado_id">
                                        <option value="">Selecione um estado...</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['estado_id']) ?>"
                                                <?= (isset($cliente['cliente_estado_id']) && $estado['estado_id'] == $cliente['cliente_estado_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estado['estado_nome']) ?> (<?= htmlspecialchars($estado['estado_uf']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CEP -->
                                <div class="col-md-4">
                                    <label for="cliente_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control cep-input" id="cliente_cep" name="cliente_cep"
                                        value="<?= htmlspecialchars($cliente['cliente_cep'] ?? '') ?>" maxlength="10">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-4">
                                    <label for="cliente_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone-input" id="cliente_telefone" name="cliente_telefone"
                                        value="<?= htmlspecialchars($cliente['cliente_telefone'] ?? '') ?>" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone Secundário -->
                                <div class="col-md-4">
                                    <label for="cliente_telefone_secundario" class="form-label">Telefone Secundário</label>
                                    <input type="text" class="form-control telefone-input" id="cliente_telefone_secundario" name="cliente_telefone_secundario"
                                        value="<?= htmlspecialchars($cliente['cliente_telefone_secundario'] ?? '') ?>" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-6">
                                    <label for="cliente_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="cliente_email" name="cliente_email"
                                        value="<?= htmlspecialchars($cliente['cliente_email'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Contato Principal -->
                                <div class="col-md-6">
                                    <label for="cliente_contato_principal" class="form-label">Contato Principal</label>
                                    <input type="text" class="form-control" id="cliente_contato_principal" name="cliente_contato_principal"
                                        value="<?= htmlspecialchars($cliente['cliente_contato_principal'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Status * -->
                                <div class="col-md-12">
                                    <label for="cliente_status" class="form-label">Status *</label>
                                    <select class="form-select" id="cliente_status" name="cliente_status" required>
                                        <option value="ativo" <?= (isset($cliente['cliente_status']) && $cliente['cliente_status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                        <option value="inativo" <?= (isset($cliente['cliente_status']) && $cliente['cliente_status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                    <div class="invalid-feedback">Por favor, selecione o status.</div>
                                </div>

                                <!-- Observações -->
                                <div class="col-md-12">
                                    <label for="cliente_observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="cliente_observacoes" name="cliente_observacoes" rows="3" maxlength="500"><?= htmlspecialchars($cliente['cliente_observacoes'] ?? '') ?></textarea>
                                    <div class="form-text">Opcional - máximo 500 caracteres</div>
                                </div>

                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/cliente_controller.php?acao=listar" class="btn btn-secondary">
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

        // Máscaras simples para Telefone, CEP e CNPJ/CPF
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

             const cepInputs = document.querySelectorAll('.cep-input');
             cepInputs.forEach(function(input) {
                 // Aplica a máscara ao carregar a página, se houver valor
                 if (input.value) {
                     let value = input.value.replace(/\D/g, '');
                     if (value.length > 8) value = value.substring(0, 8);
                     let formattedValue = '';
                     if (value.length > 5) formattedValue = value.substring(0, 5) + '-' + value.substring(5);
                     else formattedValue = value;
                     input.value = formattedValue;
                 }
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     if (value.length > 8) value = value.substring(0, 8);
                     let formattedValue = '';
                     if (value.length > 5) formattedValue = value.substring(0, 5) + '-' + value.substring(5);
                     else formattedValue = value;
                     e.target.value = formattedValue;
                 });
             });

             const cpfCnpjInputs = document.querySelectorAll('.cpf-cnpj-input');
             cpfCnpjInputs.forEach(function(input) {
                 // Aplica a máscara ao carregar a página, se houver valor
                 if (input.value) {
                     let value = input.value.replace(/\D/g, '');
                     let formattedValue = '';
                     if (value.length <= 11) { // CPF
                         if (value.length > 3) formattedValue += value.substring(0, 3) + '.';
                         if (value.length > 6) formattedValue += value.substring(3, 6) + '.';
                         if (value.length > 9) formattedValue += value.substring(6, 9) + '-';
                         formattedValue += value.substring(9);
                     } else { // CNPJ
                         if (value.length > 2) formattedValue += value.substring(0, 2) + '.';
                         if (value.length > 5) formattedValue += value.substring(2, 5) + '.';
                         if (value.length > 8) formattedValue += value.substring(5, 8) + '/';
                         if (value.length > 12) formattedValue += value.substring(8, 12) + '-';
                         formattedValue += value.substring(12, 14);
                     }
                     input.value = formattedValue;
                 }
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     let formattedValue = '';
                     if (value.length <= 11) { // CPF
                         if (value.length > 3) formattedValue += value.substring(0, 3) + '.';
                         if (value.length > 6) formattedValue += value.substring(3, 6) + '.';
                         if (value.length > 9) formattedValue += value.substring(6, 9) + '-';
                         formattedValue += value.substring(9);
                     } else { // CNPJ
                         if (value.length > 2) formattedValue += value.substring(0, 2) + '.';
                         if (value.length > 5) formattedValue += value.substring(2, 5) + '.';
                         if (value.length > 8) formattedValue += value.substring(5, 8) + '/';
                         if (value.length > 12) formattedValue += value.substring(8, 12) + '-';
                         formattedValue += value.substring(12, 14);
                     }
                     e.target.value = formattedValue;
                 });
             });
        });
    </script>
</body>
</html>