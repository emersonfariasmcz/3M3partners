<?php
# Local: /app/views/usuarios/create.php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/conexao.php';

$sql = "SELECT usuariopapel_id, usuariopapel_nome FROM usuariopapeis ORDER BY usuariopapel_nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$papeis = $stmt->fetchAll();

$erro = $_SESSION['erro_salvar'] ?? '';
unset($_SESSION['erro_salvar']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Cadastrar Novo Usuário</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $erro ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="../../app/controllers/usuario_controller.php?acao=salvar" method="POST">
        <div class="mb-3">
            <label for="usuario_nome" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" id="usuario_nome" name="usuario_nome" required>
        </div>
        <div class="mb-3">
            <label for="usuario_email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="usuario_email" name="usuario_email" required>
        </div>
        <div class="mb-3">
            <label for="usuario_login" class="form-label">Login</label>
            <input type="text" class="form-control" id="usuario_login" name="usuario_login" required>
        </div>
        <div class="mb-3">
            <label for="usuario_senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="usuario_senha" name="usuario_senha" required>
        </div>
        <div class="mb-3">
            <label for="papel_id" class="form-label">Nível de Acesso</label>
            <select class="form-select" id="papel_id" name="papel_id" required>
                <option value="">Selecione um papel</option>
                <?php foreach ($papeis as $papel): ?>
                    <option value="<?= htmlspecialchars($papel['usuariopapel_id']); ?>"><?= htmlspecialchars($papel['usuariopapel_nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="../../app/controllers/usuario_controller.php?acao=listar" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>