<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../app/views/login.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        try {
            $sql = "SELECT u.usuario_id, u.usuario_nome, u.usuario_login, u.usuario_email, p.usuariopapel_nome 
                    FROM usuarios u
                    JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                    ORDER BY u.usuario_nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll();
            require_once __DIR__ . '/../../app/views/usuarios/index.php';
        } catch (PDOException $e) {
            die("Erro ao listar usuários: " . $e->getMessage());
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id    = $_POST['usuario_id'] ?? null;
            $usuario_nome  = trim($_POST['usuario_nome']);
            $usuario_email = trim($_POST['usuario_email']);
            $usuario_login = trim($_POST['usuario_login']);
            $papel_id      = (int)$_POST['papel_id'];
            $usuario_senha = $_POST['usuario_senha'] ?? '';

            if (empty($usuario_nome) || empty($usuario_login) || empty($papel_id)) {
                $_SESSION['erro_salvar'] = "Todos os campos obrigatórios devem ser preenchidos.";
                header('Location: ' . ($usuario_id ? '../../app/views/usuarios/edit.php?id=' . $usuario_id : '../../app/views/usuarios/create.php'));
                exit;
            }

            try {
                if ($usuario_id) { // Atualizar
                    $sql = "UPDATE usuarios SET usuario_nome = ?, usuario_email = ?, usuario_login = ?, papel_id = ?";
                    $params = [$usuario_nome, $usuario_email, $usuario_login, $papel_id];
                    
                    if (!empty($usuario_senha)) {
                        $hash_senha = password_hash($usuario_senha, PASSWORD_DEFAULT);
                        $sql .= ", usuario_senha = ?";
                        $params[] = $hash_senha;
                    }
                    
                    $sql .= " WHERE usuario_id = ?";
                    $params[] = $usuario_id;

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $_SESSION['sucesso_salvar'] = "Usuário atualizado com sucesso!";

                } else { // Inserir novo
                    if (empty($usuario_senha)) {
                         $_SESSION['erro_salvar'] = "A senha é obrigatória para novos usuários.";
                         header('Location: ../../app/views/usuarios/create.php');
                         exit;
                    }

                    $hash_senha = password_hash($usuario_senha, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuarios (usuario_nome, usuario_email, usuario_login, usuario_senha, papel_id)
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$usuario_nome, $usuario_email, $usuario_login, $hash_senha, $papel_id]);
                    $_SESSION['sucesso_salvar'] = "Usuário cadastrado com sucesso!";
                }

            } catch (PDOException $e) {
                if ($e->getCode() === '23000') { // Erro de violação de unicidade (login, email)
                    $_SESSION['erro_salvar'] = "Login ou E-mail já existem no sistema.";
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao salvar usuário: " . $e->getMessage();
                }
            }
            
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $usuario_id = $_GET['id'] ?? null;
        if (!$usuario_id) {
            die("ID de usuário não fornecido.");
        }
        try {
            $sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                die("Usuário não encontrado.");
            }
            $papeis = $pdo->query("SELECT usuariopapel_id, usuariopapel_nome FROM usuariopapeis ORDER BY usuariopapel_nome")->fetchAll();
            require_once __DIR__ . '/../../app/views/usuarios/edit.php';
        } catch (PDOException $e) {
            die("Erro ao buscar usuário: " . $e->getMessage());
        }
        break;

    case 'excluir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? null;
            if (!$usuario_id) {
                $_SESSION['erro_excluir'] = "ID de usuário não fornecido para exclusão.";
                header('Location: usuario_controller.php?acao=listar');
                exit;
            }
            try {
                $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$usuario_id]);
                $_SESSION['sucesso_excluir'] = "Usuário excluído com sucesso!";
            } catch (PDOException $e) {
                $_SESSION['erro_excluir'] = "Erro ao excluir usuário: " . $e->getMessage();
            }
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }
        break;
    
    default:
        // Padrão: Se a ação não for definida, listamos os usuários
        header('Location: usuario_controller.php?acao=listar');
        break;
}
?>