<?php
# ========================================
# Visualização de Supervisores (Listagem)
# Local: /app/views/supervisores/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

if (!isset($supervisores)) {
    header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
    exit;
}

$total_supervisores = count($supervisores);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisores - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Supervisores</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/supervisor_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Supervisor
                </a>
            </div>
        </div>

        <?php if ($sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($sucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($erro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_supervisores > 0): ?>
                                <?php foreach ($supervisores as $supervisor): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($supervisor['supervisor_id']) ?></td>
                                        <td><strong><?= htmlspecialchars($supervisor['supervisor_nome']) ?></strong></td>
                                        <td><?= !empty($supervisor['supervisor_email']) ? htmlspecialchars($supervisor['supervisor_email']) : 'N/A' ?></td>
                                        <td><?= !empty($supervisor['supervisor_telefone']) ? htmlspecialchars($supervisor['supervisor_telefone']) : 'N/A' ?></td>
                                        <td><?= !empty($supervisor['supervisor_criadoem']) ? date('d/m/Y H:i', strtotime($supervisor['supervisor_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/supervisor_controller.php?acao=editar&id=<?= htmlspecialchars($supervisor['supervisor_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar supervisor">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/supervisor_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este supervisor? Esta ação não pode ser desfeita e falhará se o supervisor estiver em uso.');">
                                                    <input type="hidden" name="supervisor_id" value="<?= htmlspecialchars($supervisor['supervisor_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir supervisor">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum supervisor encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de supervisores: <strong><?= htmlspecialchars($total_supervisores) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>