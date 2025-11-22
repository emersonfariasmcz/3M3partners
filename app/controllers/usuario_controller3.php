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
        try {
            // Consulta para listar usuários com seus papéis
            $sql = "SELECT u.*, p.usuariopapel_nome 
                    FROM usuarios u
                    JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                    ORDER BY u.usuario_nome ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar papéis para filtros (se necessário)
            $sqlPapeis = "SELECT * FROM usuariopapeis ORDER BY usuariopapel_nome";
            $stmtPapeis = $pdo->query($sqlPapeis);
            $papeis = $stmtPapeis->fetchAll(PDO::FETCH_ASSOC);
            
            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';
            
            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);
            
            // Incluir a view de listagem
            require_once('../../app/views/usuarios/index.php');
            
        } catch (PDOException $e) {
            // Em caso de erro, redirecionar com mensagem
            $_SESSION['erro_listagem'] = "Erro ao carregar lista de usuários: " . $e->getMessage();
            header('Location: ../../app/views/dashboard.php');
            exit;
        }
        break;
        
    case 'editar':
        // Lógica existente para edição
        break;
        
    case 'salvar':
        // Lógica existente para salvar
        break;
        
    case 'excluir':
        // Lógica existente para exclusão
        break;
        
    default:
        header('Location: usuario_controller.php?acao=listar');
        break;
}
?>