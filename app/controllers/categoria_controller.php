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
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/categoria.php'; // Inclui o Model

$categoriaModel = new Categoria($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Usa o Model para buscar os dados
            $categorias = $categoriaModel->listarTodos();
            
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
            // Em caso de erro grave, redireciona para uma página de erro genérica ou dashboard
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de categorias. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
            // echo "<h3>Erro ao carregar lista de categorias</h3>";
            // echo "<p>Por favor, tente novamente mais tarde.</p>";
            // echo "<p><a href='/iga/app/views/dashboard.php'>Voltar ao Dashboard</a></p>";
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
            // Usa o Model para buscar os dados
            $categoria = $categoriaModel->buscarPorId($id);

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
            // Redireciona corretamente dependendo se é edição ou criação
            $redirect_id = $_POST['categoria_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/categoria_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['categoria_id'] ?? null;
        
        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['categoria_nome']),
                'descricao' => trim($_POST['categoria_descricao'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da categoria.";
                header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo a própria categoria) usando o Model
                if ($categoriaModel->nomeExiste($dados['nome'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome de categoria já está cadastrado.";
                    header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($categoriaModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Categoria atualizada com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada."; // Pode ser sucesso também, só não mudou nada
                }
                
                header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar categoria: " . $e->getMessage();
                header("Location: /iga/app/controllers/categoria_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVA CATEGORIA
            $dados = [
                'nome' => trim($_POST['categoria_nome']),
                'descricao' => trim($_POST['categoria_descricao'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da categoria.";
                header('Location: /iga/app/controllers/categoria_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome já existe usando o Model
                if ($categoriaModel->nomeExiste($dados['nome'])) {
                    $_SESSION['erro_salvar'] = "Este nome de categoria já está cadastrado.";
                    header('Location: /iga/app/controllers/categoria_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($categoriaModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Categoria cadastrada com sucesso!";
                    header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar categoria. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/categoria_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                // Log do erro para depuração
                error_log("Erro ao inserir categoria: " . $e->getMessage());
                header('Location: /iga/app/controllers/categoria_controller.php?acao=criar');
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
            // Verificar se categoria existe usando o Model
            $categoria = $categoriaModel->buscarPorId($id);
            if (!$categoria) {
                $_SESSION['erro_excluir'] = "Categoria não encontrada.";
                header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
                exit;
            }
            
            // Tenta excluir usando o Model
            if ($categoriaModel->excluir($id)) {
                 $_SESSION['sucesso_excluir'] = "Categoria excluída com sucesso!";
            } else {
                 // Se a exclusão falhar (ex: restrição de FK), trata o erro
                 // (A lógica real de verificação de uso em produtos foi comentada no Model)
                 $_SESSION['erro_excluir'] = "Erro ao excluir categoria. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir categoria: " . $e->getMessage();
            // Log do erro para depuração
            error_log("Erro ao excluir categoria ID $id: " . $e->getMessage());
        }
        
        header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
        exit;
        
    default:
        header('Location: /iga/app/controllers/categoria_controller.php?acao=listar');
        break;
}
?>