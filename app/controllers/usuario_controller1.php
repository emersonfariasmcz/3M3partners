<?php
# ========================================
# Controlador de Usuários (CRUD)
# Local: /app/controllers/usuario_controller.php
# ========================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../../config/conexao.php');

$acao = $_REQUEST['acao'] ?? '';

switch ($acao) {
    case 'listar':
        // Constrói a consulta SQL base
        $sql = "SELECT u.*, p.usuariopapel_nome 
                FROM usuarios u
                JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id";
        $parametros = [];

        // Verifica se há um termo de busca
        if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
            $busca = '%' . trim($_GET['busca']) . '%';
            $sql .= " WHERE u.usuario_nome LIKE :busca OR u.usuario_email LIKE :busca OR u.usuario_login LIKE :busca";
            $parametros[':busca'] = $busca;
        }

        $sql .= " ORDER BY u.usuario_id ASC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($parametros);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Inclui a view para exibir a lista de usuários
            require_once('../../app/views/usuarios/index.php');
        } catch (PDOException $e) {
            echo "Erro ao buscar usuários: " . $e->getMessage();
            die();
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }

        try {
            $sql = "SELECT * FROM usuarios WHERE usuario_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Busca os papéis de usuário para o dropdown
            $sql_papeis = "SELECT * FROM usuariopapeis";
            $stmt_papeis = $pdo->query($sql_papeis);
            $papeis = $stmt_papeis->fetchAll(PDO::FETCH_ASSOC);

            if ($usuario) {
                require_once('../../app/views/usuarios/edit.php');
            } else {
                $_SESSION['erro_salvar'] = "Usuário não encontrado.";
                header('Location: usuario_controller.php?acao=listar');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do usuário: " . $e->getMessage();
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        $id = $_POST['usuario_id'] ?? null;
        $nome = $_POST['usuario_nome'] ?? '';
        $login = $_POST['usuario_login'] ?? '';
        $email = $_POST['usuario_email'] ?? '';
        $senha = $_POST['usuario_senha'] ?? '';
        $papel_id = $_POST['papel_id'] ?? '';

        if (empty($nome) || empty($login) || empty($papel_id)) {
            $_SESSION['erro_salvar'] = "Nome, Login e Papel são campos obrigatórios.";
            if ($id) {
                header("Location: usuario_controller.php?acao=editar&id=" . $id);
            } else {
                header('Location: ../../app/views/usuarios/create.php');
            }
            exit;
        }

        try {
            if ($id) { // Atualiza usuário existente
                $sql_update = "UPDATE usuarios SET usuario_nome = :nome, usuario_login = :login, usuario_email = :email, papel_id = :papel_id";
                if (!empty($senha)) {
                    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
                    $sql_update .= ", usuario_senha = :senha";
                }
                $sql_update .= " WHERE usuario_id = :id";
                
                $stmt = $pdo->prepare($sql_update);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':papel_id', $papel_id, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                if (!empty($senha)) {
                    $stmt->bindParam(':senha', $senha_hashed);
                }
                
                $stmt->execute();
                $_SESSION['sucesso_salvar'] = "Usuário atualizado com sucesso!";
            } else { // Insere novo usuário
                $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO usuarios (usuario_nome, usuario_login, usuario_email, usuario_senha, papel_id) VALUES (:nome, :login, :email, :senha, :papel_id)";
                $stmt = $pdo->prepare($sql_insert);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha_hashed);
                $stmt->bindParam(':papel_id', $papel_id, PDO::PARAM_INT);
                
                $stmt->execute();
                $_SESSION['sucesso_salvar'] = "Usuário cadastrado com sucesso!";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao salvar usuário: " . $e->getMessage();
        }
        header('Location: usuario_controller.php?acao=listar');
        exit;

    case 'excluir':
        $id = $_POST['usuario_id'] ?? null;
        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do usuário não fornecido.";
            header('Location: usuario_controller.php?acao=listar');
            exit;
        }

        try {
            $sql = "DELETE FROM usuarios WHERE usuario_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['sucesso_excluir'] = "Usuário excluído com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir usuário: " . $e->getMessage();
        }
        header('Location: usuario_controller.php?acao=listar');
        exit;

    default:
        header('Location: usuario_controller.php?acao=listar');
        break;
}