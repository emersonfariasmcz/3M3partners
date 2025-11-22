<?php
# ========================================
# Controlador de Supervisores (CRUD Completo)
# Local: /app/controllers/supervisor_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar supervisores)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/supervisor.php';

$supervisorModel = new Supervisor($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $supervisores = $supervisorModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/supervisores/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar supervisores: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de supervisores. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/supervisores/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do supervisor não fornecido.";
            header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
            exit;
        }

        try {
            $supervisor = $supervisorModel->buscarPorId($id);

            if (!$supervisor) {
                $_SESSION['erro_salvar'] = "Supervisor não encontrado.";
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/supervisores/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do supervisor: " . $e->getMessage();
            header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['supervisor_id'] ?? null;
            if ($redirect_id) {
                header("Location: /iga/app/controllers/supervisor_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['supervisor_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['supervisor_nome'] ?? ''),
                'email' => trim($_POST['supervisor_email'] ?? ''),
                'telefone' => trim($_POST['supervisor_telefone'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do supervisor.";
                header("Location: /iga/app/controllers/supervisor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Validações adicionais
            if (!empty($dados['email']) && $supervisorModel->emailExiste($dados['email'], $id)) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outro supervisor.";
                header("Location: /iga/app/controllers/supervisor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                if ($supervisorModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Supervisor atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar supervisor: " . $e->getMessage();
                header("Location: /iga/app/controllers/supervisor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO SUPERVISOR
            $dados = [
                'nome' => trim($_POST['supervisor_nome'] ?? ''),
                'email' => trim($_POST['supervisor_email'] ?? ''),
                'telefone' => trim($_POST['supervisor_telefone'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do supervisor.";
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=criar');
                exit;
            }

            // Validações adicionais
            if (!empty($dados['email']) && $supervisorModel->emailExiste($dados['email'])) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=criar');
                exit;
            }

            try {
                if ($supervisorModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Supervisor cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar supervisor. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/supervisor_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir supervisor: " . $e->getMessage());
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['supervisor_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do supervisor não fornecido.";
            header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se supervisor existe
            $supervisor = $supervisorModel->buscarPorId($id);
            if (!$supervisor) {
                $_SESSION['erro_excluir'] = "Supervisor não encontrado.";
                header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($supervisorModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Supervisor excluído com sucesso!";
            } else {
                 // Se a exclusão falhar (ex: restrição de FK)
                 $_SESSION['erro_excluir'] = "Erro ao excluir supervisor. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir supervisor: " . $e->getMessage();
            error_log("Erro ao excluir supervisor ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/supervisor_controller.php?acao=listar');
        break;
}
?>