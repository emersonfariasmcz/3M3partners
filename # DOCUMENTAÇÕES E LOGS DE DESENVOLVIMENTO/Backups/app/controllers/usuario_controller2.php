<?php
# ========================================
# Controlador de Usuários (CRUD Completo)
# Local: /app/controllers/usuario_controller.php
# ========================================

// Ativar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores podem gerenciar usuários)
if ($_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Você não tem permissão para acessar esta funcionalidade.";
    header('Location: ../../app/views/dashboard.php');
    exit;
}

require_once('../../config/conexao.php');

// Determinar a ação com base no parâmetro 'acao'
$acao = $_REQUEST['acao'] ?? 'listar';

switch ($acao) {
    case 'listar':
        // Lógica existente para listagem
        break;
        
    case 'editar':
        // Lógica existente para edição
        break;
        
    case 'salvar':
        // Lógica existente para salvar
        break;
        
    case 'excluir':
        // Adicionar lógica para exclusão segura
        $id = $_POST['usuario_id'] ?? null;
        
        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do usuário não fornecido.";
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }

        // Não permitir que o usuário exclua a si mesmo
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['erro_excluir'] = "Você não pode excluir seu próprio usuário.";
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }

        try {
            // Usar transação para garantir integridade
            $pdo->beginTransaction();
            
            // Primeiro verificar se o usuário existe
            $sqlVerifica = "SELECT usuario_id FROM usuarios WHERE usuario_id = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$id]);
            
            if ($stmtVerifica->rowCount() === 0) {
                $_SESSION['erro_excluir'] = "Usuário não encontrado.";
                $pdo->rollBack();
                header('Location: usuario_controller.php?acao=listar');
                exit;
            }
            
            // Excluir o usuário
            $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $pdo->commit();
            
            $_SESSION['sucesso_excluir'] = "Usuário excluído com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['erro_excluir'] = "Erro ao excluir usuário: " . $e->getMessage();
        }
        
        header('Location: usuario_controller.php?acao=listar');
        exit;
        
    default:
        header('Location: usuario_controller.php?acao=listar');
        break;
}
?>