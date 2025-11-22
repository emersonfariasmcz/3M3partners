<?php
# ========================================
# Visualização de Edição de Usuário
# Local: /app/views/usuarios/edit.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);

// As variáveis $usuario e $papeis são carregadas pelo controller
if (!isset($usuario) || !isset($papeis)) {
    die("Erro: Variáveis de usuário ou papéis não foram carregadas corretamente. Tente acessar pela lista de usuários.");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuário - Sistema IGA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Editar Usuário</h2>
    <div>
      <a href="../../app/controllers/usuario_controller.php?acao=listar" class="btn btn-primary me-2">
        <i class="fas fa-arrow-left"></i> Voltar
      </a>
      <a href="../../app/views/dashboard.php" class="btn btn-primary">
        <i class="fas fa-home"></i> Dashboard
      </a>
    </div>
  </div>

  <?php if ($erro): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= $erro ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  <?php endif; ?>

  <form method="POST" action="../../app/controllers/usuario_controller.php?acao=salvar">

    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['usuario_id']) ?>">

    <div class="mb-3">
      <label for="usuario_nome" class="form-label">Nome:</label>
      <input type="text" class="form-control" id="usuario_nome" name="usuario_nome" value="<?= htmlspecialchars($usuario['usuario_nome']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="usuario_login" class="form-label">Login:</label>
      <input type="text" class="form-control" id="usuario_login" name="usuario_login" value="<?= htmlspecialchars($usuario['usuario_login']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="usuario_email" class="form-label">E-mail:</label>
      <input type="email" class="form-control" id="usuario_email" name="usuario_email" value="<?= htmlspecialchars($usuario['usuario_email']) ?>">
    </div>

    <div class="mb-3">
      <label for="usuario_senha" class="form-label">Nova Senha (preencha apenas se desejar alterar):</label>
      <input type="password" class="form-control" id="usuario_senha" name="usuario_senha" minlength="6">
    </div>

    <div class="mb-4">
      <label for="papel_id" class="form-label">Papel:</label>
      <select name="papel_id" id="papel_id" class="form-select" required>
        <?php foreach ($papeis as $papel): ?>
          <option value="<?= htmlspecialchars($papel['usuariopapel_id']) ?>"
            <?= $papel['usuariopapel_id'] == $usuario['papel_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($papel['usuariopapel_nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save"></i> Atualizar
    </button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>