<!-- Tela de Recuperação de Senha - recuperar_senha.php -->
<?php session_start(); ?>
<!-- Tela de Recuperação de Senha - recuperar_senha.php -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Recuperar Senha - Sistema IGA</title>

  <!-- Estilos globais -->
  <link rel="stylesheet" href="/iga/assets/css/style.css">

  <!-- FontAwesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <!-- Container principal -->
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <!-- Logo -->
        <img src="/iga/assets/img/img_logo.png" alt="Logo do Sistema" class="login-logo" />
      </div>

      <!-- Mensagem de feedback -->
      <?php if (isset($_SESSION['recuperar_msg'])): ?>
        <div class="alert alert-info" style="color: green; text-align: center;">
          <?= $_SESSION['recuperar_msg']; unset($_SESSION['recuperar_msg']); ?>
        </div>
      <?php endif; ?>

      <!-- Formulário de recuperação -->
      <form method="POST" action="/iga/app/controllers/recuperar_senha_handler.php" class="login-form">
        <label for="email">Informe seu e-mail:</label>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" id="email" required placeholder="email@exemplo.com">
        </div>

        <button type="submit" class="btn-primary">Enviar Link de Recuperação</button>

        <p class="recovery-link">
          <a href="/iga/app/views/login.php">Voltar para o login</a>
        </p>
      </form>
    </div>
  </div>
</body>

</html>

