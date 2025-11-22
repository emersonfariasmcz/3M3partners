<?php
# ========================================
# Controlador de Fornecedores (CRUD Completo)
# Local: /app/controllers/fornecedor_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar fornecedores)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/fornecedor.php';

$fornecedorModel = new Fornecedor($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $fornecedores = $fornecedorModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/fornecedores/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar fornecedores: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de fornecedores. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            $estados = $fornecedorModel->listarEstados();

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/fornecedores/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do fornecedor não fornecido.";
            header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
            exit;
        }

        try {
            $fornecedor = $fornecedorModel->buscarPorId($id);

            if (!$fornecedor) {
                $_SESSION['erro_salvar'] = "Fornecedor não encontrado.";
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
                exit;
            }

            $estados = $fornecedorModel->listarEstados();

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/fornecedores/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do fornecedor: " . $e->getMessage();
            header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['fornecedor_id'] ?? null;
            if ($redirect_id) {
                header("Location: /iga/app/controllers/fornecedor_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['fornecedor_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['fornecedor_nome'] ?? ''),
                'razaosocial' => trim($_POST['fornecedor_razaosocial'] ?? ''),
                'cnpj' => trim($_POST['fornecedor_cnpj'] ?? ''),
                'endereco' => trim($_POST['fornecedor_endereco'] ?? ''),
                'bairro' => trim($_POST['fornecedor_bairro'] ?? ''),
                'cidade' => trim($_POST['fornecedor_cidade'] ?? ''),
                'estado_id' => intval($_POST['fornecedor_estado_id'] ?? 0),
                'telefone' => trim($_POST['fornecedor_telefone'] ?? ''),
                'email' => trim($_POST['fornecedor_email'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do fornecedor.";
                header("Location: /iga/app/controllers/fornecedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $fornecedorModel->cnpjExiste($dados['cnpj'], $id)) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado para outro fornecedor.";
                header("Location: /iga/app/controllers/fornecedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            if (!empty($dados['email']) && $fornecedorModel->emailExiste($dados['email'], $id)) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outro fornecedor.";
                header("Location: /iga/app/controllers/fornecedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                if ($fornecedorModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Fornecedor atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar fornecedor: " . $e->getMessage();
                header("Location: /iga/app/controllers/fornecedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO FORNECEDOR
            $dados = [
                'nome' => trim($_POST['fornecedor_nome'] ?? ''),
                'razaosocial' => trim($_POST['fornecedor_razaosocial'] ?? ''),
                'cnpj' => trim($_POST['fornecedor_cnpj'] ?? ''),
                'endereco' => trim($_POST['fornecedor_endereco'] ?? ''),
                'bairro' => trim($_POST['fornecedor_bairro'] ?? ''),
                'cidade' => trim($_POST['fornecedor_cidade'] ?? ''),
                'estado_id' => intval($_POST['fornecedor_estado_id'] ?? 0),
                'telefone' => trim($_POST['fornecedor_telefone'] ?? ''),
                'email' => trim($_POST['fornecedor_email'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do fornecedor.";
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $fornecedorModel->cnpjExiste($dados['cnpj'])) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado.";
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
                exit;
            }

            if (!empty($dados['email']) && $fornecedorModel->emailExiste($dados['email'])) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
                exit;
            }

            try {
                if ($fornecedorModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Fornecedor cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar fornecedor. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir fornecedor: " . $e->getMessage());
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['fornecedor_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do fornecedor não fornecido.";
            header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se fornecedor existe
            $fornecedor = $fornecedorModel->buscarPorId($id);
            if (!$fornecedor) {
                $_SESSION['erro_excluir'] = "Fornecedor não encontrado.";
                header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($fornecedorModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Fornecedor excluído com sucesso!";
            } else {
                // Se a exclusão falhar (ex: restrição de FK), trata o erro
                // (A lógica real de verificação de uso em produtos foi comentada no Model)
                $_SESSION['erro_excluir'] = "Erro ao excluir fornecedor. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir fornecedor: " . $e->getMessage();
            error_log("Erro ao excluir fornecedor ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/fornecedor_controller.php?acao=listar');
        break;
}
?>