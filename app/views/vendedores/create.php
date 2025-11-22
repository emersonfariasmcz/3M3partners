<?php
# ========================================
# Formulário de Criação de Vendedor
# Local: /app/views/vendedores/create.php
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

// Verificações para evitar erro - Garante que o formulário só seja acessado via controller
if (!isset($estados) || !isset($vendedores)) {
    error_log("Acesso direto ao create.php de vendedores detectado ou dados necessarios ausentes.");
    $_SESSION['erro_salvar'] = "Erro ao carregar dados necessários. Acesso inválido.";
    header('Location: ../../app/controllers/vendedor_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Vendedor - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12"> <!-- Aumentado para acomodar mais campos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>Novo Vendedor
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../../app/controllers/vendedor_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Nome do Vendedor * -->
                                <div class="col-md-8">
                                    <label for="vendedor_nome" class="form-label">Nome do Vendedor *</label>
                                    <input type="text" class="form-control" id="vendedor_nome" name="vendedor_nome" required maxlength="150">
                                    <div class="invalid-feedback">Por favor, informe o nome do vendedor.</div>
                                </div>

                                <!-- Matrícula -->
                                <div class="col-md-4">
                                    <label for="vendedor_matricula" class="form-label">Matrícula</label>
                                    <input type="text" class="form-control" id="vendedor_matricula" name="vendedor_matricula" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CPF -->
                                <div class="col-md-4">
                                    <label for="vendedor_cpf" class="form-label">CPF</label>
                                    <input type="text" class="form-control cpf-input" id="vendedor_cpf" name="vendedor_cpf" maxlength="14">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-4">
                                    <label for="vendedor_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control telefone-input" id="vendedor_telefone" name="vendedor_telefone" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-4">
                                    <label for="vendedor_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="vendedor_email" name="vendedor_email" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-8">
                                    <label for="vendedor_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="vendedor_endereco" name="vendedor_endereco" maxlength="255">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Bairro -->
                                <div class="col-md-4">
                                    <label for="vendedor_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="vendedor_bairro" name="vendedor_bairro" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-6">
                                    <label for="vendedor_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="vendedor_cidade" name="vendedor_cidade" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="vendedor_estado_id" class="form-label">Estado</label>
                                    <select class="form-select" id="vendedor_estado_id" name="vendedor_estado_id">
                                        <option value="">Selecione um estado...</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['estado_id']) ?>">
                                                <?= htmlspecialchars($estado['estado_nome']) ?> (<?= htmlspecialchars($estado['estado_uf']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CEP -->
                                <div class="col-md-4">
                                    <label for="vendedor_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control cep-input" id="vendedor_cep" name="vendedor_cep" maxlength="10">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Percentual de Comissão -->
                                <div class="col-md-4">
                                    <label for="vendedor_comissao_percentual" class="form-label">Percentual de Comissão (%)</label>
                                    <input type="number" class="form-control" id="vendedor_comissao_percentual" name="vendedor_comissao_percentual" min="0" max="100" step="0.01" value="0.00">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Data de Admissão -->
                                <div class="col-md-4">
                                    <label for="vendedor_data_admissao" class="form-label">Data de Admissão</label>
                                    <input type="date" class="form-control" id="vendedor_data_admissao" name="vendedor_data_admissao">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Supervisor -->
                                <div class="col-md-6">
                                    <label for="vendedor_supervisor_id" class="form-label">Supervisor</label>
                                    <select class="form-select" id="vendedor_supervisor_id" name="vendedor_supervisor_id">
                                        <option value="">Selecione um supervisor...</option>
                                        <?php foreach ($vendedores as $supervisor): ?>
                                            <option value="<?= htmlspecialchars($supervisor['vendedor_id']) ?>">
                                                <?= htmlspecialchars($supervisor['vendedor_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Status * -->
                                <div class="col-md-6">
                                    <label for="vendedor_status" class="form-label">Status *</label>
                                    <select class="form-select" id="vendedor_status" name="vendedor_status" required>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                    <div class="invalid-feedback">Por favor, selecione o status.</div>
                                </div>

                                <!-- Observações -->
                                <div class="col-md-12">
                                    <label for="vendedor_observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="vendedor_observacoes" name="vendedor_observacoes" rows="3" maxlength="500"></textarea>
                                    <div class="form-text">Opcional - máximo 500 caracteres</div>
                                </div>

                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="../../app/controllers/vendedor_controller.php?acao=listar" class="btn btn-secondary">
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

        // Máscaras simples para CPF, Telefone e CEP
        document.addEventListener('DOMContentLoaded', function() {
             const cpfInputs = document.querySelectorAll('.cpf-input');
             cpfInputs.forEach(function(input) {
                 input.addEventListener('input', function (e) {
                     let value = e.target.value.replace(/\D/g, '');
                     if (value.length > 11) value = value.substring(0, 11);
                     let formattedValue = '';
                     if (value.length > 3) formattedValue += value.substring(0, 3) + '.';
                     if (value.length > 6) formattedValue += value.substring(3, 6) + '.';
                     if (value.length > 9) formattedValue += value.substring(6, 9) + '-';
                     formattedValue += value.substring(9);
                     e.target.value = formattedValue;
                 });
             });

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