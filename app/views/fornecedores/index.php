<?php
# ========================================
# Visualização de Fornecedores (Listagem)
# Local: /app/views/fornecedores/index.php
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

if (!isset($fornecedores)) {
    header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
    exit;
}

$total_fornecedores = count($fornecedores);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Fornecedores</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/fornecedor_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Fornecedor
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
                                <th>Razão Social</th>
                                <th>CNPJ</th>
                                <th>Cidade/UF</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_fornecedores > 0): ?>
                                <?php foreach ($fornecedores as $fornecedor): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fornecedor['fornecedor_id']) ?></td>
                                        <td><strong><?= htmlspecialchars($fornecedor['fornecedor_nome']) ?></strong></td>
                                        <td><?= !empty($fornecedor['fornecedor_razaosocial']) ? htmlspecialchars($fornecedor['fornecedor_razaosocial']) : 'N/A' ?></td>
                                        <td><?= !empty($fornecedor['fornecedor_cnpj']) ? htmlspecialchars($fornecedor['fornecedor_cnpj']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($fornecedor['fornecedor_cidade'] ?? 'N/A') ?>/<?= htmlspecialchars($fornecedor['estado_uf'] ?? 'N/A') ?></td>
                                        <td><?= !empty($fornecedor['fornecedor_telefone']) ? htmlspecialchars($fornecedor['fornecedor_telefone']) : 'N/A' ?></td>
                                        <td><?= !empty($fornecedor['fornecedor_email']) ? htmlspecialchars($fornecedor['fornecedor_email']) : 'N/A' ?></td>
                                        <td><?= !empty($fornecedor['fornecedor_criadoem']) ? date('d/m/Y H:i', strtotime($fornecedor['fornecedor_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/fornecedor_controller.php?acao=editar&id=<?= htmlspecialchars($fornecedor['fornecedor_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar fornecedor">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/fornecedor_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este fornecedor?');">
                                                    <input type="hidden" name="fornecedor_id" value="<?= htmlspecialchars($fornecedor['fornecedor_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir fornecedor">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum fornecedor encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de fornecedores: <strong><?= htmlspecialchars($total_fornecedores) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>