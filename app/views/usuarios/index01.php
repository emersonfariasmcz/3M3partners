<?php
# ========================================
# Visualização de Usuários (Tabela de Listagem)
# Local: /app/views/usuarios/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

// As variáveis $usuarios são carregadas pelo controller
if (!isset($usuarios)) {
    // Redireciona para o controlador para carregar os dados se a variável não estiver presente.
    header('Location: ../../app/controllers/usuario_controller.php?acao=listar');
    exit;
}

$total_usuarios = count($usuarios);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Usuários - Sistema IGA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
  <div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Usuários</h2>
      <div>
        <a href="../../app/views/dashboard.php" class="btn btn-primary me-2">
          <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <a href="../../app/views/usuarios/create.php" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Novo Usuário
        </a>
      </div>
    </div>
    
    <?php if ($sucesso): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $sucesso ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $erro ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom">
      <div class="card-header-custom">
        Lista de Usuários
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Login</th>
                <th>Perfil</th>
                <th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($total_usuarios > 0): ?>
                  <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                      <td><?= htmlspecialchars($usuario['usuario_id']) ?></td>
                      <td><?= htmlspecialchars($usuario['usuario_nome']) ?></td>
                      <td><?= htmlspecialchars($usuario['usuario_email']) ?></td>
                      <td><?= htmlspecialchars($usuario['usuario_login']) ?></td>
                      <td><?= htmlspecialchars($usuario['usuariopapel_nome']) ?></td>
                      <td class="text-center">
                        <a href="../../app/controllers/usuario_controller.php?acao=editar&id=<?= htmlspecialchars($usuario['usuario_id']) ?>" class="btn btn-custom-edit btn-sm me-2">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form action="../../app/controllers/usuario_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
                            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['usuario_id']) ?>">
                            <button type="submit" class="btn btn-custom-delete btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="6" class="text-center">Nenhum usuário encontrado.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="total-users mt-3">
        Total de usuários: <strong><?= htmlspecialchars($total_usuarios) ?></strong>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>