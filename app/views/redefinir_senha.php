<?php
session_start();
$token = $_GET['token'] ?? '';

// Se o token estiver vazio, redireciona por segurança
if (empty($token)) {
    $_SESSION['erro_login'] = 'Token inválido ou expirado.';
    header('Location: ../../app/views/login.php');
    exit;
}
?>
<!-- Tela de Redefinição de Senha - redefinir_senha.php -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redefinir Senha - Sistema IGA</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../../assets/css/style.css">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="/iga/assets/img/img_logo.png" alt="Logo do Sistema" class="login-logo" />
      </div>

      <!-- Mensagem de erro ou sucesso -->
      <?php if (isset($_SESSION['redefinir_erro'])): ?>
        <div class="alert alert-danger text-center" style="color: red;">
          <?= $_SESSION['redefinir_erro']; unset($_SESSION['redefinir_erro']); ?>
        </div>
      <?php elseif (isset($_SESSION['redefinir_sucesso'])): ?>
        <div class="alert alert-success text-center" style="color: green;">
          <?= $_SESSION['redefinir_sucesso']; unset($_SESSION['redefinir_sucesso']); ?>
        </div>
      <?php endif; ?>

      <!-- Formulário de redefinição -->
      <form method="POST" action="/iga/app/controllers/redefinir_senha_handler.php" class="login-form">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

        <label for="nova_senha">Nova Senha:</label>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="nova_senha" id="nova_senha" required placeholder="Nova senha">
        </div>

        <label for="confirmar_senha">Confirmar Senha:</label>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="confirmar_senha" id="confirmar_senha" required placeholder="Repita a nova senha">
        </div>

        <button type="submit" class="btn-primary">Redefinir Senha</button>

        <p class="recovery-link">
          <a href="/iga/app/views/login.php">Voltar para o login</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
