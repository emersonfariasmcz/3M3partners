<?php
# ========================================
# Visualização de Clientes (Listagem)
# Local: /app/views/clientes/index.php
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

if (!isset($clientes)) {
    header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
    exit;
}

$total_clientes = count($clientes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Clientes</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/cliente_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Cliente
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
                                <th>Nome/Razão Social</th>
                                <th>Nome Fantasia</th>
                                <th>CNPJ/CPF</th>
                                <th>Cidade/UF</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_clientes > 0): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cliente['cliente_id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($cliente['cliente_nome']) ?></strong>
                                            <?php if (!empty($cliente['cliente_contato_principal'])): ?>
                                                <br><small class="text-muted">Contato: <?= htmlspecialchars($cliente['cliente_contato_principal']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($cliente['cliente_nome_fantasia']) ? htmlspecialchars($cliente['cliente_nome_fantasia']) : 'N/A' ?></td>
                                        <td><?= !empty($cliente['cliente_cnpj_cpf']) ? htmlspecialchars($cliente['cliente_cnpj_cpf']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($cliente['cliente_cidade'] ?? 'N/A') ?>/<?= htmlspecialchars($cliente['estado_uf'] ?? 'N/A') ?></td>
                                        <td><?= !empty($cliente['cliente_telefone']) ? htmlspecialchars($cliente['cliente_telefone']) : 'N/A' ?></td>
                                        <td><?= !empty($cliente['cliente_email']) ? htmlspecialchars($cliente['cliente_email']) : 'N/A' ?></td>
                                        <td>
                                            <span class="badge <?= $cliente['cliente_status'] === 'ativo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?= ucfirst(htmlspecialchars($cliente['cliente_status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/cliente_controller.php?acao=editar&id=<?= htmlspecialchars($cliente['cliente_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar cliente">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/cliente_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este cliente?');">
                                                    <input type="hidden" name="cliente_id" value="<?= htmlspecialchars($cliente['cliente_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir cliente">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum cliente encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de clientes: <strong><?= htmlspecialchars($total_clientes) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>