<?php
# ========================================
# Controlador de Autenticação
# Local: /app/controllers/autenticacao.php
# ========================================

ini_set('display_errors', 1);      // Ativa a exibição de erros
ini_set('display_startup_errors', 1); // Erros no PHP ao iniciar
error_reporting(E_ALL);            // Reporta todos os erros possíveis

session_start();
require_once '../../config/conexao.php';
//require_once('../../app/controllers/autenticacao.php');

# Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = trim($_POST['username'] ?? '');
    $senha   = $_POST['password'] ?? '';

    # Verificação básica
    if (empty($usuario) || empty($senha)) {
        $_SESSION['erro_login'] = "Usuário e senha são obrigatórios.";
        header('Location: ../../app/views/login.php');
        exit;
    }

    try {
        # Consulta ao banco pelo login
        $sql = "SELECT u.*, p.usuariopapel_nome 
                FROM usuarios u
                JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                WHERE u.usuario_login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $usuario);
        $stmt->execute();
        $dados = $stmt->fetch();

        # Se encontrou o usuário
        if ($dados && password_verify($senha, $dados['usuario_senha'])) {
            # Login bem-sucedido
            $_SESSION['usuario_id']   = $dados['usuario_id'];
            $_SESSION['usuario_nome'] = $dados['usuario_nome'];
            $_SESSION['usuario_login'] = $dados['usuario_login'];
            $_SESSION['usuario_papel'] = $dados['usuariopapel_nome'];
            $_SESSION['logado_em']     = time();

            header('Location: ../../app/views/dashboard.php');
            exit;
        } else {
            # Login falhou
            $_SESSION['erro_login'] = "Usuário ou senha inválidos.";
            header('Location: ../../app/views/login.php');
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['erro_login'] = "Erro de conexão com o banco.";
        header('Location: ../../app/views/login.php');
        exit;
    }
} else {
    # Acesso indevido
    header('Location: ../../app/views/login.php');
    exit;

    }

?>