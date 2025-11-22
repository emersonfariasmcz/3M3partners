<?php
# ================================================
# Handler para Redefinir Senha
# Local: /app/controllers/redefinir_senha_handler.php
# ================================================

session_start();
require_once '../../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token            = $_POST['token'] ?? '';
    $nova_senha       = $_POST['nova_senha'] ?? '';
    $confirmar_senha  = $_POST['confirmar_senha'] ?? '';

    // 1. Validação básica
    if (empty($token) || empty($nova_senha) || empty($confirmar_senha)) {
        $_SESSION['redefinir_erro'] = 'Preencha todos os campos.';
        header("Location: ../../app/views/redefinir_senha.php?token=$token");
        exit;
    }

    if ($nova_senha !== $confirmar_senha) {
        $_SESSION['redefinir_erro'] = 'As senhas não coincidem.';
        header("Location: ../../app/views/redefinir_senha.php?token=$token");
        exit;
    }

    if (strlen($nova_senha) < 6) {
        $_SESSION['redefinir_erro'] = 'A senha deve ter pelo menos 6 caracteres.';
        header("Location: ../../app/views/redefinir_senha.php?token=$token");
        exit;
    }

    try {
        // 2. Verifica o token
        $stmt = $pdo->prepare("SELECT * FROM tokens_recuperacao WHERE token = :token AND expiracao >= NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $tokenData = $stmt->fetch();

        if (!$tokenData) {
            $_SESSION['redefinir_erro'] = 'Token inválido ou expirado.';
            header('Location: ../../app/views/login.php');
            exit;
        }

        $usuario_id = $tokenData['usuario_id'];

        // 3. Atualiza a senha do usuário
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET usuario_senha = :senha WHERE usuario_id = :id");
        $stmt->bindParam(':senha', $hash);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();

        // 4. Invalida o token (recomendado: deletar)
        $del = $pdo->prepare("DELETE FROM tokens_recuperacao WHERE token = :token");
        $del->bindParam(':token', $token);
        $del->execute();

        $_SESSION['redefinir_sucesso'] = 'Senha redefinida com sucesso! Faça login com a nova senha.';
        header('Location: ../../app/views/login.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['redefinir_erro'] = 'Erro ao redefinir a senha. Tente novamente.';
        header("Location: ../../app/views/redefinir_senha.php?token=$token");
        exit;
    }
} else {
    header('Location: ../../app/views/login.php');
    exit;
}