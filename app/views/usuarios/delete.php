<?php
# Local: /app/views/usuarios/delete.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuário inválido.");
}

$usuario_id = (int)$_GET['id'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Excluir Usuário - Sistema IGA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body>
<div class="container mt-5">
  <div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">Confirmar Exclusão</h4>
    <p>Você tem certeza que deseja excluir o usuário de ID #<?= htmlspecialchars($usuario_id) ?>?</p>
    <hr>
    <p class="mb-0">Essa ação é irreversível.</p>
  </div>
  <form action="../../app/controllers/usuario_controller.php?acao=excluir" method="POST">
    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
    <button type="submit" class="btn btn-danger">Sim, Excluir</button>
    <a href="../../app/controllers/usuario_controller.php?acao=listar" class="btn btn-secondary">Não, Voltar</a>
  </form>
</div>
</body>
</html>