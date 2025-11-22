<?php
# ========================================
# Página Inicial de Configurações Gerais (Redireciona para Edição)
# Local: /app/views/configuracoesgerais/index.php
# ========================================

// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores)
if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

// Redireciona diretamente para a página de edição
header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
exit;
?>