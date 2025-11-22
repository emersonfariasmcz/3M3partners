<?php
# ========================================
# Formulário de Cadastro / Edição de Usuário
# Local: /app/views/usuarios/form.php
# ========================================

$edicao = isset($usuario);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title><?= $edicao ? 'Editar' : 'Novo' ?> Usuário</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

  <div class="container mt-5">
    <h2 class="mb-4 text-success"><?= $edicao ? 'Editar Usuário' : 'Novo Usuário' ?></h2>

    <form action="../../app/controllers/usuario_controller.php?acao=salvar" method="POST" class="row g-3">

      <?php if ($edicao): ?>
        <input type="hidden" name="usuario_id" value="<?= $usuario['usuario_id'] ?>">
      <?php endif; ?>

      <div class="col-md-6">
        <label for="usuario_nome" class="form-label">Nome Completo</label>
        <input type="text" class="form-control" name="usuario_nome" id="usuario_nome" required
               value="<?= $edicao ? htmlspecialchars($usuario['usuario_nome']) : '' ?>">
      </div>

      <div class="col-md-6">
        <label for="usuario_email" class="form-label">Email</label>
        <input type="email" class="form-control" name="usuario_email" id="usuario_email"
               value="<?= $edicao ? htmlspecialchars($usuario['usuario_email']) : '' ?>">
      </div>

      <div class="col-md-4">
        <label for="usuario_login" class="form-label">Login</label>
        <input type="text" class="form-control" name="usuario_login" id="usuario_login" required
               value="<?= $edicao ? htmlspecialchars($usuario['usuario_login']) : '' ?>">
      </div>

      <div class="col-md-4">
        <label for="usuario_senha" class="form-label">Senha <?= $edicao ? '(Deixe em branco para manter)' : '' ?></label>
        <input type="password" class="form-control" name="usuario_senha" id="usuario_senha" <?= $edicao ? '' : 'required' ?>>
      </div>

      <div class="col-md-4">
        <label for="papel_id" class="form-label">Nível de Acesso</label>
        <select name="papel_id" id="papel_id" class="form-select" required>
          <option value="">Selecione...</option>
          <?php foreach ($papeis as $papel): ?>
            <option value="<?= $papel['usuariopapel_id'] ?>"
              <?= ($edicao && $usuario['papel_id'] == $papel['usuariopapel_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($papel['usuariopapel_nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 d-flex justify-content-between mt-4">
        <a href="../../app/controllers/usuario_controller.php?acao=listar" class="btn btn-secondary">Voltar</a>
        <button type="submit" class="btn btn-success">Salvar</button>
      </div>
    </form>
  </div>

</body>
</html>
