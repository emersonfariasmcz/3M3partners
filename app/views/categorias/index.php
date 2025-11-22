<?php
# ========================================
# Visualização de Categorias (Listagem)
# Local: /app/views/categorias/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

if (!isset($categorias)) {
    header('Location: ../../app/controllers/categoria_controller.php?acao=listar');
    exit;
}

$total_categorias = count($categorias);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema IGA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Categorias</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/views/categorias/create.php" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Nova Categoria
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_categorias > 0): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($categoria['categoria_id']) ?></td>
                                        <td><?= htmlspecialchars($categoria['categoria_nome']) ?></td>
                                        <td><?= !empty($categoria['categoria_descricao']) ? htmlspecialchars($categoria['categoria_descricao']) : 'N/A' ?></td>
                                        <td><?= !empty($categoria['categoria_criadoem']) ? date('d/m/Y H:i', strtotime($categoria['categoria_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/categoria_controller.php?acao=editar&id=<?= htmlspecialchars($categoria['categoria_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar categoria">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/categoria_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir esta categoria?');">
                                                    <input type="hidden" name="categoria_id" value="<?= htmlspecialchars($categoria['categoria_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir categoria">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhuma categoria encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="total-users mt-3">
            Total de categorias: <strong><?= htmlspecialchars($total_categorias) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>