<?php
# ========================================
# Visualização de Distritos (Listagem)
# Local: /app/views/distritos/index.php
# ========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../app/views/login.php');
    exit;
}

$sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
$erro    = $_SESSION['erro_salvar']    ?? $_SESSION['erro_excluir']   ?? '';

unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

if (!isset($distritos)) {
    header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
    exit;
}

$total_distritos = count($distritos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distritos - Sistema IGA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Distritos</h2>
            <div class="d-flex gap-2">
                <a href="../../app/views/dashboard.php" class="btn btn-custom-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="../../app/controllers/distrito_controller.php?acao=criar" class="btn btn-custom-add">
                    <i class="fas fa-plus"></i> Novo Distrito
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
                                <th>Criado em</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_distritos > 0): ?>
                                <?php foreach ($distritos as $distrito): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($distrito['distrito_id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($distrito['distrito_nome']) ?></strong>
                                            <?php if ($distrito['distrito_id'] == 1): ?>
                                                <span class="badge bg-warning text-dark ms-2">Sistema</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($distrito['distrito_criadoem']) ? date('d/m/Y H:i', strtotime($distrito['distrito_criadoem'])) : 'N/A' ?></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="../../app/controllers/distrito_controller.php?acao=editar&id=<?= htmlspecialchars($distrito['distrito_id']) ?>" class="btn btn-custom-edit btn-sm" title="Editar distrito">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($distrito['distrito_id'] != 1): // Impede exclusão do distrito "SEM DISTRITO" ?>
                                                    <form action="../../app/controllers/distrito_controller.php?acao=excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este distrito? Esta ação não pode ser desfeita e falhará se o distrito estiver em uso.');">
                                                        <input type="hidden" name="distrito_id" value="<?= htmlspecialchars($distrito['distrito_id']) ?>">
                                                        <button type="submit" class="btn btn-custom-delete btn-sm" title="Excluir distrito">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                     <button class="btn btn-secondary btn-sm" title="Distrito do sistema, não pode ser excluído" disabled>
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum distrito encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="total-users mt-3">
            Total de distritos: <strong><?= htmlspecialchars($total_distritos) ?></strong>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>