<?php
# ========================================
# Controlador de Autenticação - VERSÃO CORRIGIDA E COM DEBUG
# Local: /app/controllers/autenticacao.php
# ========================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config/conexao.php';  // ← Caminho relativo correto

# Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/views/login.php');
    exit;
}

$usuario = trim($_POST['username'] ?? '');
$senha   = $_POST['password'] ?? '';

if (empty($usuario) || empty($senha)) {
    $_SESSION['erro_login'] = "Usuário e senha são obrigatórios.";
    header('Location: ../../app/views/login.php');
    exit;
}

try {
    # CONSULTA COM LEFT JOIN (funciona mesmo se papel não existir)
    $sql = "SELECT 
                u.usuario_id,
                u.usuario_nome,
                u.usuario_login,
                u.usuario_senha,
                u.papel_id,
                COALESCE(p.usuariopapel_nome, 'Usuário') AS usuariopapel_nome
            FROM usuarios u
            LEFT JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
            WHERE u.usuario_login = :login";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':login', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    # ========================================
    # DEBUG TEMPORÁRIO - REMOVA DEPOIS DO LOGIN FUNCIONAR
    # ========================================
    echo "<pre style='background:#f9f9f9; border:2px solid #d00; padding:15px; font-family:monospace; font-size:14px; margin:20px;'>";
    echo "<strong>DEBUG DE AUTENTICAÇÃO</strong>\n\n";
    echo "USUÁRIO DIGITADO: <strong>$usuario</strong>\n";
    echo "SENHA DIGITADA:   <strong>" . str_repeat('*', strlen($senha)) . "</strong> (comprimento: " . strlen($senha) . ")\n\n";
    echo "RESULTADO DA QUERY:\n";
    if ($dados) {
        echo "✓ Usuário encontrado!\n";
        echo "ID: " . $dados['usuario_id'] . "\n";
        echo "Login: " . $dados['usuario_login'] . "\n";
        echo "Papel ID: " . $dados['papel_id'] . "\n";
        echo "Papel Nome: " . $dados['usuariopapel_nome'] . "\n";
        echo "Hash da senha no banco:\n" . $dados['usuario_senha'] . "\n\n";
        $verificado = password_verify($senha, $dados['usuario_senha']);
        echo "password_verify() = " . ($verificado ? "<span style='color:green'>VERDADEIRO</span>" : "<span style='color:red'>FALSO</span>") . "\n";
    } else {
        echo "<span style='color:red'>✗ NENHUM USUÁRIO ENCONTRADO COM O LOGIN: '$usuario'</span>\n";
    }
    echo "\nSESSION ATUAL:\n";
    print_r($_SESSION);
    echo "</pre>";
    exit;
    # ========================================
    # FIM DO DEBUG - REMOVA ATÉ AQUI
    # ========================================

    # Verificação final da senha
    if ($dados && password_verify($senha, $dados['usuario_senha'])) {
        # Login bem-sucedido
        $_SESSION['usuario_id']     = $dados['usuario_id'];
        $_SESSION['usuario_nome']   = $dados['usuario_nome'] ?? $dados['usuario_login'];
        $_SESSION['usuario_login']  = $dados['usuario_login'];
        $_SESSION['usuario_papel']  = $dados['usuariopapel_nome'];
        $_SESSION['logado_em']      = time();

        # Redireciona com caminho absoluto
        header('Location: /3m3erp/app/views/dashboard.php');
        exit;
    } else {
        $_SESSION['erro_login'] = "Usuário ou senha inválidos.";
        header('Location: /3m3erp/app/views/login.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Erro PDO em autenticacao.php: " . $e->getMessage());
    $_SESSION['erro_login'] = "Erro interno. Tente novamente.";
    header('Location: /3m3erp/app/views/login.php');
    exit;
}
?>