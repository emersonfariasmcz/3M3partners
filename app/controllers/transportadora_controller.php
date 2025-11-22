<?php
# ========================================
# Controlador de Transportadoras (CRUD Completo)
# Local: /app/controllers/transportadora_controller.php
# ========================================

// Ativar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /3m3erp/app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores podem gerenciar transportadoras)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /3m3erp/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/transportadora.php';

$transportadoraModel = new Transportadora($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $transportadoras = $transportadoraModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/transportadoras/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar transportadoras: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de transportadoras. Por favor, tente novamente.";
            header('Location: /3m3erp/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/transportadoras/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID da transportadora não fornecido.";
            header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
            exit;
        }

        try {
            $transportadora = $transportadoraModel->buscarPorId($id);

            if (!$transportadora) {
                $_SESSION['erro_salvar'] = "Transportadora não encontrada.";
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/transportadoras/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados da transportadora: " . $e->getMessage();
            header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['transportadora_id'] ?? null;
            if ($redirect_id) {
                header("Location: /3m3erp/app/controllers/transportadora_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['transportadora_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['transportadora_nome'] ?? ''),
                'cnpj' => trim($_POST['transportadora_cnpj'] ?? ''),
                'endereco' => trim($_POST['transportadora_endereco'] ?? ''),
                'bairro' => trim($_POST['transportadora_bairro'] ?? ''),
                'cidade' => trim($_POST['transportadora_cidade'] ?? ''),
                'estado' => trim($_POST['transportadora_estado'] ?? ''),
                'telefone' => trim($_POST['transportadora_telefone'] ?? ''),
                'email' => trim($_POST['transportadora_email'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da transportadora.";
                header("Location: /3m3erp/app/controllers/transportadora_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $transportadoraModel->cnpjExiste($dados['cnpj'], $id)) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado para outra transportadora.";
                header("Location: /3m3erp/app/controllers/transportadora_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            if (!empty($dados['email']) && $transportadoraModel->emailExiste($dados['email'], $id)) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outra transportadora.";
                header("Location: /3m3erp/app/controllers/transportadora_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                if ($transportadoraModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Transportadora atualizada com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar transportadora: " . $e->getMessage();
                header("Location: /3m3erp/app/controllers/transportadora_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVA TRANSPORTADORA
            $dados = [
                'nome' => trim($_POST['transportadora_nome'] ?? ''),
                'cnpj' => trim($_POST['transportadora_cnpj'] ?? ''),
                'endereco' => trim($_POST['transportadora_endereco'] ?? ''),
                'bairro' => trim($_POST['transportadora_bairro'] ?? ''),
                'cidade' => trim($_POST['transportadora_cidade'] ?? ''),
                'estado' => trim($_POST['transportadora_estado'] ?? ''),
                'telefone' => trim($_POST['transportadora_telefone'] ?? ''),
                'email' => trim($_POST['transportadora_email'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome da transportadora.";
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $transportadoraModel->cnpjExiste($dados['cnpj'])) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado.";
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
                exit;
            }

            if (!empty($dados['email']) && $transportadoraModel->emailExiste($dados['email'])) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
                exit;
            }

            try {
                if ($transportadoraModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Transportadora cadastrada com sucesso!";
                    header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar transportadora. Nenhum registro foi inserido.";
                    header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir transportadora: " . $e->getMessage());
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['transportadora_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID da transportadora não fornecido.";
            header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se transportadora existe
            $transportadora = $transportadoraModel->buscarPorId($id);
            if (!$transportadora) {
                $_SESSION['erro_excluir'] = "Transportadora não encontrada.";
                header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($transportadoraModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Transportadora excluída com sucesso!";
            } else {
                $_SESSION['erro_excluir'] = "Erro ao excluir transportadora.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir transportadora: " . $e->getMessage();
            error_log("Erro ao excluir transportadora ID $id: " . $e->getMessage());
        }

        header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
        exit;

    default:
        header('Location: /3m3erp/app/controllers/transportadora_controller.php?acao=listar');
        break;
}
?>