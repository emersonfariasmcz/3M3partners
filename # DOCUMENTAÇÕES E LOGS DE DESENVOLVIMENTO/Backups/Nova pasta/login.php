<?php
session_start();
?>

<!-- Página de Login - login.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IGA - Controle de Estoque</title>

  <!-- CSS externo com as cores da identidade visual -->
  <link rel="stylesheet" href="../../assets/css/style.css">

  <!-- Ícones FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
  <!-- Container principal -->
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <!-- Logo -->
        <img src="../../assets/img/img_logo.png" alt="Logo do Sistema" class="login-logo" />
        <!--<h1 class="login-title">Acesso ao Sistema</h1>-->
      </div>

<!-- Formulário de login -->

<?php if (isset($_SESSION['erro_login'])): ?>
  <div style="color: red; text-align: center; margin-bottom: 15px;">
    <?= $_SESSION['erro_login']; unset($_SESSION['erro_login']); ?>
  </div>
<?php endif; ?>

<!--<form method="POST" action="/iga/app/controllers/autenticacao.php" class="login-form">-->
<form method="POST" action="../../app/controllers/autenticacao.php" class="login-form">
<label for="username">Usuário</label>
  <div class="input-group">
    <i class="fas fa-user"></i>
    <input type="text" name="username" id="username" required />
  </div>

  <label for="password">Senha</label>
  <div class="input-group">
    <i class="fas fa-lock"></i>
    <input type="password" name="password" id="password" required />
  </div>

  <button type="submit" class="btn-primary">Entrar</button>

  <p class="recovery-link">
 <!-- <a href="/iga/app/views/recuperar_senha.php">Esqueceu sua senha?</a>-->
 <a href="../../app/views/recuperar_senha.php">Esqueceu sua senha?</a>

  </p>
</form>


    </div>
  </div>
</body>
</html>
