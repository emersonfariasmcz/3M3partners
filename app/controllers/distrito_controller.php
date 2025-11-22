<?php
# ========================================
# Controlador de Distritos (CRUD Completo)
# Local: /app/controllers/distrito_controller.php
# ========================================

// Ativar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão e verificar autenticação
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores podem gerenciar distritos)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: ../../app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/distrito.php';

$distritoModel = new Distrito($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $distritos = $distritoModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/distritos/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar distritos: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de distritos. Por favor, tente novamente.";
            header('Location: ../../app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/distritos/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do distrito não fornecido.";
            header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
            exit;
        }

        try {
            $distrito = $distritoModel->buscarPorId($id);

            if (!$distrito) {
                $_SESSION['erro_salvar'] = "Distrito não encontrado.";
                header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '../../views/distritos/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do distrito: " . $e->getMessage();
            header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['distrito_id'] ?? null;
            if ($redirect_id) {
                header("Location: ../../app/controllers/distrito_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: ../../app/controllers/distrito_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['distrito_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['distrito_nome'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do distrito.";
                header("Location: ../../app/controllers/distrito_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Verificar se nome já existe (excluindo o próprio distrito)
            if ($distritoModel->nomeExiste($dados['nome'], $id)) {
                $_SESSION['erro_salvar'] = "Este nome de distrito já está cadastrado.";
                header("Location: ../../app/controllers/distrito_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }


            try {
                if ($distritoModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Distrito atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar distrito: " . $e->getMessage();
                header("Location: ../../app/controllers/distrito_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO DISTRITO
            $dados = [
                'nome' => trim($_POST['distrito_nome'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do distrito.";
                header('Location: ../../app/controllers/distrito_controller.php?acao=criar');
                exit;
            }

            // Verificar se nome já existe
            if ($distritoModel->nomeExiste($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Este nome de distrito já está cadastrado.";
                header('Location: ../../app/controllers/distrito_controller.php?acao=criar');
                exit;
            }

            try {
                if ($distritoModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Distrito cadastrado com sucesso!";
                    header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar distrito. Nenhum registro foi inserido.";
                    header('Location: ../../app/controllers/distrito_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir distrito: " . $e->getMessage());
                header('Location: ../../app/controllers/distrito_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['distrito_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do distrito não fornecido.";
            header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
            exit;
        }

        // Impede a exclusão do distrito "SEM DISTRITO" (ID 1)
        if ($id == 1) {
             $_SESSION['erro_excluir'] = "O distrito 'SEM DISTRITO' não pode ser excluído.";
             header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
             exit;
        }

        try {
            // Verificar se distrito existe
            $distrito = $distritoModel->buscarPorId($id);
            if (!$distrito) {
                $_SESSION['erro_excluir'] = "Distrito não encontrado.";
                header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($distritoModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Distrito excluído com sucesso!";
            } else {
                 // Se a exclusão falhar (ex: restrição de FK ou tentativa de excluir "SEM DISTRITO")
                 $_SESSION['erro_excluir'] = "Erro ao excluir distrito. Pode estar em uso ou não ser permitido.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir distrito: " . $e->getMessage();
            error_log("Erro ao excluir distrito ID $id: " . $e->getMessage());
        }

        header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
        exit;

    default:
        header('Location: ../../app/controllers/distrito_controller.php?acao=listar');
        break;
}
?>