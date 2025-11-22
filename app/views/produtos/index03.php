<?php
# ========================================
# Visualização de Produtos (Listagem)
# Local: /app/views/produtos/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

// Verificação de papel foi movida para o controller
$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

if (!isset($produtos)) {
    error_log("View index.php: Variável \$produtos não definida. Redirecionando para controller.");
    header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
    exit;
}

$total_produtos = count($produtos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Sistema IGA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Produtos</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/produto_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Produto
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
                                <th>Categoria</th>
                                <th>Unid. Medida</th>
                                <th>Estoque Mín.</th>
                                <th>Estoque Máx.</th>
                                <th>Preço Venda</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_produtos > 0): ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($produto['produto_id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($produto['produto_nome']) ?></strong>
                                            <?php if (!empty($produto['produto_descricao'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($produto['produto_descricao'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($produto['categoria_nome'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($produto['produto_unidade_medida']) ?></td>
                                        <td><?= htmlspecialchars($produto['produto_estoque_minimo']) ?></td>
                                        <td><?= htmlspecialchars($produto['produto_estoque_maximo']) ?></td>
                                        <td>R$ <?= number_format($produto['produto_preco_venda'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge <?= $produto['produto_status'] === 'ativo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?= ucfirst(htmlspecialchars($produto['produto_status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/produto_controller.php?acao=editar&id=<?= htmlspecialchars($produto['produto_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar produto">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/produto_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este produto?');">
                                                    <input type="hidden" name="produto_id" value="<?= htmlspecialchars($produto['produto_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir produto">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum produto encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="total-users mt-3">
            Total de produtos: <strong><?= htmlspecialchars($total_produtos) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>