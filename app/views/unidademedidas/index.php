<?php
# ========================================
# Visualização de Unidades de Medida (Listagem)
# Local: /app/views/unidademedidas/index.php
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

if (!isset($unidademedidas)) {
    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
    exit;
}

$total_unidademedidas = count($unidademedidas);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidades de Medida - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Unidades de Medida</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/unidademedida_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Nova Unidade de Medida
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
                                <th>Sigla</th>
                                <th>Descrição</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_unidademedidas > 0): ?>
                                <?php foreach ($unidademedidas as $unidademedida): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($unidademedida['unidademedida_id']) ?></td>
                                        <td><strong><?= htmlspecialchars($unidademedida['unidademedida_nome']) ?></strong></td>
                                        <td><?= htmlspecialchars($unidademedida['unidademedida_sigla']) ?></td>
                                        <td><?= !empty($unidademedida['unidademedida_descricao']) ? htmlspecialchars(substr($unidademedida['unidademedida_descricao'], 0, 50)) . '...' : 'N/A' ?></td>
                                        <td><?= !empty($unidademedida['unidademedida_criadoem']) ? date('d/m/Y H:i', strtotime($unidademedida['unidademedida_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/unidademedida_controller.php?acao=editar&id=<?= htmlspecialchars($unidademedida['unidademedida_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar unidade de medida">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/unidademedida_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir esta unidade de medida?\n\nATENÇÃO: A exclusão só será permitida se NENHUM produto estiver usando esta unidade de medida.\n\nEsta ação não pode ser desfeita.');">
                                                    <input type="hidden" name="unidademedida_id" value="<?= htmlspecialchars($unidademedida['unidademedida_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir unidade de medida">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhuma unidade de medida encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de unidades de medida: <strong><?= htmlspecialchars($total_unidademedidas) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>