<?php
# ========================================
# Formulário de Edição de Unidade de Saúde
# Local: /app/views/unidades/edit.php
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

// Verificações para evitar erro - Garante que o formulário só seja acessado via controller
if (!isset($unidade) || !isset($estados) || !isset($distritos) || !isset($supervisores)) {
    error_log("Acesso direto ao edit.php de unidades detectado ou dados necessarios ausentes.");
    $_SESSION['erro_salvar'] = "Unidade ou dados necessários não encontrados.";
    header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Unidade de Saúde - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Corrigido -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Corrigido -->
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12"> <!-- Aumentado para acomodar mais campos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Unidade de Saúde
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/unidade_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <!-- ID oculto -->
                            <input type="hidden" name="unidadedesaude_id" value="<?= htmlspecialchars($unidade['unidadedesaude_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome Completo * -->
                                <div class="col-md-12">
                                    <label for="unidadedesaude_nomecomp" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="unidadedesaude_nomecomp" name="unidadedesaude_nomecomp"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_nomecomp'] ?? '') ?>" required maxlength="280">
                                    <div class="invalid-feedback">Por favor, informe o nome completo da unidade.</div>
                                </div>

                                <!-- Nome Abreviado * -->
                                <div class="col-md-12">
                                    <label for="unidadedesaude_nomeabrev" class="form-label">Nome Abreviado *</label>
                                    <input type="text" class="form-control" id="unidadedesaude_nomeabrev" name="unidadedesaude_nomeabrev"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_nomeabrev'] ?? '') ?>" required maxlength="150">
                                    <div class="invalid-feedback">Por favor, informe o nome abreviado da unidade.</div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-md-8">
                                    <label for="unidadedesaude_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="unidadedesaude_endereco" name="unidadedesaude_endereco"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_endereco'] ?? '') ?>" maxlength="200">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Bairro -->
                                <div class="col-md-4">
                                    <label for="unidadedesaude_bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="unidadedesaude_bairro" name="unidadedesaude_bairro"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_bairro'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Cidade -->
                                <div class="col-md-6">
                                    <label for="unidadedesaude_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="unidadedesaude_cidade" name="unidadedesaude_cidade"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_cidade'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label for="unidadedesaude_estado_id" class="form-label">Estado</label>
                                    <select class="form-select" id="unidadedesaude_estado_id" name="unidadedesaude_estado_id">
                                        <option value="">Selecione um estado...</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['estado_id']) ?>"
                                                <?= (isset($unidade['unidadedesaude_estado_id']) && $estado['estado_id'] == $unidade['unidadedesaude_estado_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estado['estado_nome']) ?> (<?= htmlspecialchars($estado['estado_uf']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Direção Administrativa -->
                                <div class="col-md-12">
                                    <label for="unidadedesaude_direcaoadm" class="form-label">Direção Administrativa</label>
                                    <input type="text" class="form-control" id="unidadedesaude_direcaoadm" name="unidadedesaude_direcaoadm"
                                        value="<?= htmlspecialchars($unidade['unidadedesaude_direcaoadm'] ?? '') ?>" maxlength="100">
                                    <div class="form-text">Opcional</div>
                                </div>

                                <!-- Distrito * -->
                                <div class="col-md-6">
                                    <label for="unidadedesaude_distrito_id" class="form-label">Distrito *</label>
                                    <select class="form-select" id="unidadedesaude_distrito_id" name="unidadedesaude_distrito_id" required>
                                        <option value="">Selecione um distrito...</option>
                                        <?php foreach ($distritos as $distrito): ?>
                                            <option value="<?= htmlspecialchars($distrito['distrito_id']) ?>"
                                                <?= (isset($unidade['unidadedesaude_distrito_id']) && $distrito['distrito_id'] == $unidade['unidadedesaude_distrito_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($distrito['distrito_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Por favor, selecione o distrito.</div>
                                </div>

                                <!-- Supervisor * -->
                                <div class="col-md-6">
                                    <label for="unidadedesaude_supervisor_id" class="form-label">Supervisor *</label>
                                    <select class="form-select" id="unidadedesaude_supervisor_id" name="unidadedesaude_supervisor_id" required>
                                        <option value="">Selecione um supervisor...</option>
                                        <?php foreach ($supervisores as $supervisor): ?>
                                            <option value="<?= htmlspecialchars($supervisor['supervisor_id']) ?>"
                                                <?= (isset($unidade['unidadedesaude_supervisor_id']) && $supervisor['supervisor_id'] == $unidade['unidadedesaude_supervisor_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($supervisor['supervisor_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Por favor, selecione o supervisor.</div>
                                </div>

                            </div> <!-- Fim row -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/unidade_controller.php?acao=listar" class="btn btn-secondary">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Corrigido -->
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