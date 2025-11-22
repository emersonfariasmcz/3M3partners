<?php
# ========================================
# Visualização de Fabricantes (Listagem)
# Local: /app/views/fabricantes/index.php
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

if (!isset($fabricantes)) {
    header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
    exit;
}

$total_fabricantes = count($fabricantes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabricantes - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/iga/assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Fabricantes</h2>
            <div class="d-flex gap-2">
                <a href="/iga/app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="/iga/app/controllers/fabricante_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Fabricante
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
                                <th>CNPJ</th>
                                <th>Cidade/UF</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_fabricantes > 0): ?>
                                <?php foreach ($fabricantes as $fabricante): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fabricante['fabricante_id']) ?></td>
                                        <td><strong><?= htmlspecialchars($fabricante['fabricante_nome']) ?></strong></td>
                                        <td><?= !empty($fabricante['fabricante_cnpj']) ? htmlspecialchars($fabricante['fabricante_cnpj']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($fabricante['fabricante_cidade'] ?? 'N/A') ?>/<?= htmlspecialchars($fabricante['fabricante_estado'] ?? 'N/A') ?></td>
                                        <td><?= !empty($fabricante['fabricante_telefone']) ? htmlspecialchars($fabricante['fabricante_telefone']) : 'N/A' ?></td>
                                        <td><?= !empty($fabricante['fabricante_email']) ? htmlspecialchars($fabricante['fabricante_email']) : 'N/A' ?></td>
                                        <td>
                                            <span class="badge <?= $fabricante['fabricante_status'] === 'ativo' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?= ucfirst(htmlspecialchars($fabricante['fabricante_status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= !empty($fabricante['fabricante_criadoem']) ? date('d/m/Y H:i', strtotime($fabricante['fabricante_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/iga/app/controllers/fabricante_controller.php?acao=editar&id=<?= htmlspecialchars($fabricante['fabricante_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar fabricante">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/iga/app/controllers/fabricante_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este fabricante?');">
                                                    <input type="hidden" name="fabricante_id" value="<?= htmlspecialchars($fabricante['fabricante_id']) ?>">
                                                    <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir fabricante">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Nenhum fabricante encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de fabricantes: <strong><?= htmlspecialchars($total_fabricantes) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>