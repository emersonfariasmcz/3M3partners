<?php
# ========================================
# Formulário de Edição de Configurações Gerais
# Local: /app/views/configuracoesgerais/edit.php
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Gerais - Sistema IGA</title>

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
                            <i class="fas fa-cogs me-2"></i>Configurações Gerais da Empresa
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

                        <form action="/iga/app/controllers/configuracoesgerais_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Nome da Empresa -->
                                <div class="col-md-12">
                                    <label for="config_nome_empresa" class="form-label">Nome da Empresa *</label>
                                    <input type="text" class="form-control" id="config_nome_empresa" name="config_nome_empresa"
                                        value="<?= htmlspecialchars($configuracoes['config_nome_empresa'] ?? '') ?>" required maxlength="255">
                                    <div class="invalid-feedback">Por favor, informe o nome da empresa.</div>
                                </div>

                                <!-- CNPJ -->
                                <div class="col-md-6">
                                    <label for="config_cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="config_cnpj" name="config_cnpj"
                                        value="<?= htmlspecialchars($configuracoes['config_cnpj'] ?? '') ?>" maxlength="18">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-12">
                                    <label for="config_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="config_endereco" name="config_endereco"
                                        value="<?= htmlspecialchars($configuracoes['config_endereco'] ?? '') ?>" maxlength="255">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-6">
                                    <label for="config_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="config_cidade" name="config_cidade"
                                        value="<?= htmlspecialchars($configuracoes['config_cidade'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado (Dropdown) -->
                                <div class="col-md-6">
                                    <label for="config_estado_id" class="form-label">Estado</label>
                                    <select class="form-select" id="config_estado_id" name="config_estado_id">
                                        <option value="">Selecione um estado...</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['estado_id']) ?>"
                                                <?= (isset($configuracoes['config_estado_id']) && $estado['estado_id'] == $configuracoes['config_estado_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estado['estado_nome']) ?> (<?= htmlspecialchars($estado['estado_uf']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- CEP -->
                                <div class="col-md-6">
                                    <label for="config_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control" id="config_cep" name="config_cep"
                                        value="<?= htmlspecialchars($configuracoes['config_cep'] ?? '') ?>" maxlength="10">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-6">
                                    <label for="config_telefone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control" id="config_telefone" name="config_telefone"
                                        value="<?= htmlspecialchars($configuracoes['config_telefone'] ?? '') ?>" maxlength="20">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- E-mail -->
                                <div class="col-md-6">
                                    <label for="config_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="config_email" name="config_email"
                                        value="<?= htmlspecialchars($configuracoes['config_email'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Site -->
                                <div class="col-md-6">
                                    <label for="config_site" class="form-label">Site</label>
                                    <input type="url" class="form-control" id="config_site" name="config_site"
                                        value="<?= htmlspecialchars($configuracoes['config_site'] ?? '') ?>" maxlength="255">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Caminho do Logo -->
                                <div class="col-md-12">
                                    <label for="config_logo_path" class="form-label">Caminho do Logo</label>
                                    <input type="text" class="form-control" id="config_logo_path" name="config_logo_path"
                                        value="<?= htmlspecialchars($configuracoes['config_logo_path'] ?? 'assets/img/img_logo.png') ?>" maxlength="255">
                                    <div class="form-text">Caminho relativo para o arquivo de imagem do logo (ex: assets/img/logo.png)</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/views/dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Salvar Configurações
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