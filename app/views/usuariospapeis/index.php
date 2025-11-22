<?php
# ========================================
# Visualização de Papéis de Usuário (Listagem)
# Local: /app/views/usuariospapeis/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

if (!isset($papeis)) {
    header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
    exit;
}

$total_papeis = count($papeis);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papéis de Usuário - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Papéis de Usuário</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/usuariopapel_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Papel
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
                                <th>Descrição</th>
                                <th>Qtd. Usuários</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_papeis > 0): ?>
                                <?php foreach ($papeis as $papel): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($papel['usuariopapel_id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($papel['usuariopapel_nome']) ?></strong>
                                            <?php if ($papel['usuariopapel_id'] == 1): ?>
                                                <span class="badge bg-warning text-dark ms-2">Sistema</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($papel['usuariopapel_descricao']) ? htmlspecialchars($papel['usuariopapel_descricao']) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <?php if ($papel['qtd_usuarios'] > 0): ?>
                                                <span class="badge bg-info"><?= htmlspecialchars($papel['qtd_usuarios']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($papel['usuariopapel_criadoem']) ? date('d/m/Y H:i', strtotime($papel['usuariopapel_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/usuariopapel_controller.php?acao=editar&id=<?= htmlspecialchars($papel['usuariopapel_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar papel">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($papel['usuariopapel_id'] != 1): // Impede exclusão do papel "Administrador" ?>
                                                    <form action="/iga/app/controllers/usuariopapel_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este papel de usuário?\n\nATENÇÃO: A exclusão só será permitida se NENHUM usuário estiver atribuído a este papel.\n\nEsta ação não pode ser desfeita.');">
                                                        <input type="hidden" name="usuariopapel_id" value="<?= htmlspecialchars($papel['usuariopapel_id']) ?>">
                                                        <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir papel">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                     <button class="btn btn-secondary btn-sm" title="Papel do sistema, não pode ser excluído" disabled>
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum papel de usuário encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de papéis: <strong><?= htmlspecialchars($total_papeis) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>