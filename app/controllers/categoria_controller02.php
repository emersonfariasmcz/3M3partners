<?php
# ========================================
# Controlador de Categorias (CRUD Completo)
# Local: /app/controllers/categoria_controller.php
# ========================================

// Ativar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /iga/app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores podem gerenciar categorias)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/controllers/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Consulta para listar categorias
            $sql = "SELECT * FROM categorias ORDER BY categoria_nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';
            
            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);
            
            // Incluir a view de listagem
            require_once __DIR__ . '/../views/categorias/index.php';
            
        } catch (PDOException $e) {
            error_log("Erro ao listar categorias: " . $e->getMessage());
            echo "<h3>Erro ao carregar lista de categorias</h3>";
            echo "<p>Por favor, tente novamente mais tarde.</p>";
            echo "<p><a href='/iga/app/views/dashboard.php'>Voltar ao Dashboard</a></p>";
        }
        break;
        
    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID da categoria não fornecido.";
            header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
            exit;
        }

        try {
            $sql = "SELECT * FROM categorias WHERE categoria_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoria) {
                require_once __DIR__ . '/../views/categorias/edit.php';
            } else {
                $_SESSION['erro_salvar'] = "Categoria não encontrada.";
                header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados da categoria: " . $e->getMessage();
            header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            header('Location: /iga/app/views/categorias/create.php');
            exit;
        }

        $id = $_POST['categoria_id'] ?? null;
        
        if ($id) {
            // EDIÇÃO
            $nome = trim($_POST['categoria_nome']);
            $descricao = trim($_POST['categoria_descricao'] ?? '');

            if (empty($nome)) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da categoria.";
                header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=$id");
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo a própria categoria)
                $sqlVerifica = "SELECT COUNT(*) FROM categorias WHERE categoria_nome = ? AND categoria_id != ?";
                $stmtVerifica = $pdo->prepare($sqlVerifica);
                $stmtVerifica->execute([$nome, $id]);
                
                if ($stmtVerifica->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este nome de categoria já está cadastrado.";
                    header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=$id");
                    exit;
                }

                $sqlUpdate = "UPDATE categorias SET 
                             categoria_nome = ?, 
                             categoria_descricao = ? 
                             WHERE categoria_id = ?";
                
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$nome, $descricao, $id]);

                if ($stmtUpdate->rowCount() > 0) {
                    $_SESSION['sucesso_salvar'] = "Categoria atualizada com sucesso!";
                } else {
                    $_SESSION['erro_salvar'] = "Nenhuma alteração foi realizada.";
                }
                
                header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar categoria: " . $e->getMessage();
                header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=$id");
                exit;
            }

        } else {
            // NOVA CATEGORIA
            $nome = trim($_POST['categoria_nome']);
            $descricao = trim($_POST['categoria_descricao'] ?? '');

            if (empty($nome)) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da categoria.";
                header('Location: /iga/app/views/categorias/create.php');
                exit;
            }

            try {
                // Verificar se nome já existe
                $sqlVerifica = "SELECT COUNT(*) FROM categorias WHERE categoria_nome = ?";
                $stmtVerifica = $pdo->prepare($sqlVerifica);
                $stmtVerifica->execute([$nome]);
                
                if ($stmtVerifica->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este nome de categoria já está cadastrado.";
                    header('Location: /iga/app/views/categorias/create.php');
                    exit;
                }

                $sqlInserir = "INSERT INTO categorias 
                              (categoria_nome, categoria_descricao) 
                              VALUES (?, ?)";
                
                $stmtInserir = $pdo->prepare($sqlInserir);
                $stmtInserir->execute([$nome, $descricao]);

                if ($stmtInserir->rowCount() > 0) {
                    $_SESSION['sucesso_salvar'] = "Categoria cadastrada com sucesso!";
                    header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar categoria. Nenhum registro foi inserido.";
                    header('Location: /iga/app/views/categorias/create.php');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                header('Location: /iga/app/views/categorias/create.php');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['categoria_id'] ?? null;
        
        if (!$id) {
            $_SESSION['erro_excluir'] = "ID da categoria não fornecido.";
            header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se categoria existe
            $sqlVerifica = "SELECT categoria_id FROM categorias WHERE categoria_id = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$id]);
            
            if ($stmtVerifica->rowCount() === 0) {
                $_SESSION['erro_excluir'] = "Categoria não encontrada.";
                header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                exit;
            }
            
            // Excluir a categoria
            $sql = "DELETE FROM categorias WHERE categoria_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $_SESSION['sucesso_excluir'] = "Categoria excluída com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir categoria: " . $e->getMessage();
        }
        
        header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
        exit;
        
    default:
        header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
        break;
}
?>