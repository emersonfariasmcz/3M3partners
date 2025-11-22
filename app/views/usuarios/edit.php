<?php
# ========================================
# Visualização de Edição de Usuário
# Local: /app/views/usuarios/edit.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticação e permissões
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Você não tem permissão para acessar esta funcionalidade.";
    header('Location: /iga/app/views/dashboard.php');
    exit;
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

// As variáveis $usuario e $papeis são carregadas pelo controller
if (!isset($usuario) || !isset($papeis)) {
    $_SESSION['erro_salvar'] = "Erro ao carregar dados do usuário.";
    header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Sistema IGA</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>Editar Usuário
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/iga/app/controllers/usuario_controller.php?acao=salvar" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['usuario_id']) ?>">

                            <div class="row g-3">
                                <!-- Nome Completo -->
                                <div class="col-md-12">
                                    <label for="usuario_nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" id="usuario_nome" name="usuario_nome" 
                                           value="<?= htmlspecialchars($usuario['usuario_nome']) ?>" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe o nome completo.
                                    </div>
                                </div>
                                
                                <!-- E-mail -->
                                <div class="col-md-6">
                                    <label for="usuario_email" class="form-label">E-mail *</label>
                                    <input type="email" class="form-control" id="usuario_email" name="usuario_email" 
                                           value="<?= htmlspecialchars($usuario['usuario_email']) ?>" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe um e-mail válido.
                                    </div>
                                </div>
                                
                                <!-- Login -->
                                <div class="col-md-6">
                                    <label for="usuario_login" class="form-label">Login *</label>
                                    <input type="text" class="form-control" id="usuario_login" name="usuario_login" 
                                           value="<?= htmlspecialchars($usuario['usuario_login']) ?>" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe um login de acesso.
                                    </div>
                                </div>
                                
                                <!-- Senha -->
                                <div class="col-md-6">
                                    <label for="usuario_senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="usuario_senha" name="usuario_senha" 
                                               placeholder="Digite apenas se desejar alterar" minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Confirmar Senha -->
                                <div class="col-md-6">
                                    <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                                               placeholder="Confirme a nova senha" minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Nível de Acesso -->
                                <div class="col-md-12">
                                    <label for="papel_id" class="form-label">Nível de Acesso *</label>
                                    <select class="form-select" id="papel_id" name="papel_id" required>
                                        <option value="" disabled>Selecione um nível de acesso...</option>
                                        <?php foreach ($papeis as $papel): ?>
                                            <option value="<?= htmlspecialchars($papel['usuariopapel_id']) ?>" 
                                                <?= $papel['usuariopapel_id'] == $usuario['papel_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($papel['usuariopapel_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Por favor, selecione um nível de acesso.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="/iga/app/controllers/usuario_controller.php?acao=listar" class="btn btn-secondary">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validação do formulário -->
    <script>
        (function () {
            'use strict'
            
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    // Verificar se as senhas coincidem (apenas se alguma senha foi preenchida)
                    const senha = document.getElementById('usuario_senha')
                    const confirmarSenha = document.getElementById('confirmar_senha')
                    
                    if (senha.value || confirmarSenha.value) {
                        if (senha.value !== confirmarSenha.value) {
                            confirmarSenha.setCustomValidity("As senhas não coincidem")
                            confirmarSenha.classList.add('is-invalid')
                            event.preventDefault()
                            event.stopPropagation()
                        } else {
                            confirmarSenha.setCustomValidity("")
                        }
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
            
            // Toggle para mostrar/esconder senha
            document.getElementById('togglePassword').addEventListener('click', function() {
                const senha = document.getElementById('usuario_senha')
                const tipo = senha.getAttribute('type') === 'password' ? 'text' : 'password'
                senha.setAttribute('type', tipo)
                this.innerHTML = tipo === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>'
            })
            
            // Toggle para mostrar/esconder confirmação de senha
            document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                const confirmarSenha = document.getElementById('confirmar_senha')
                const tipo = confirmarSenha.getAttribute('type') === 'password' ? 'text' : 'password'
                confirmarSenha.setAttribute('type', tipo)
                this.innerHTML = tipo === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>'
            })
        })()
    </script>
</body>
</html>