<?php
# ============================================
# Arquivo: salvar.php
# Finalidade: Processa o cadastro de novos usuários
# ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/conexao.php';

// Função para redirecionamento com mensagem de erro
function redirecionar_erro($mensagem) {
    $_SESSION['erro_usuario'] = $mensagem;
    header("Location: ../app/views/usuarios/create.php");
    exit;
}



// Verifica se os campos obrigatórios foram preenchidos
if (
    empty($_POST['usuario_nome']) ||
    empty($_POST['usuario_email']) ||
    empty($_POST['usuario_senha']) ||
    empty($_POST['usuario_papel_id'])
) {
    redirecionar_erro("Por favor, preencha todos os campos.");
}

$nome = trim($_POST['usuario_nome']);
$email = trim($_POST['usuario_email']);
$senha = $_POST['usuario_senha'];
$papel_id = intval($_POST['usuario_papel_id']);

// Verifica se o e-mail já está cadastrado
$sql = "SELECT COUNT(*) FROM usuarios WHERE usuario_email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    redirecionar_erro("Este e-mail já está cadastrado.");
}

// Gera o hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserção do novo usuário
$sql = "INSERT INTO usuarios (usuario_nome, usuario_email, usuario_senha, usuario_papel_id, usuario_data_cadastro) 
        VALUES (?, ?, ?, ?, NOW())";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$nome, $email, $senha_hash, $papel_id]);
    $_SESSION['sucesso_usuario'] = "Usuário cadastrado com sucesso!";
    header("Location: ../app/views/usuarios/index.php");
    exit;
} catch (PDOException $e) {
    redirecionar_erro("Erro ao cadastrar usuário: " . $e->getMessage());
}
