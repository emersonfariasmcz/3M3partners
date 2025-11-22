<?php
# ========================================
# Controlador de Unidades de Medida (CRUD Completo)
# Local: /app/controllers/unidademedida_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar unidades de medida)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/unidademedida.php'; // Inclui o Model

$unidademedidaModel = new unidademedida($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Usa o Model para buscar os dados
            $unidademedidas = $unidademedidaModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/unidademedidas/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar unidades de medida: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de unidades de medida. Por favor, tente novamente.";
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
            require_once __DIR__ . '/../views/unidademedidas/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID da unidade de medida não fornecido.";
            header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
            exit;
        }

        try {
            // Buscar unidade de medida usando o Model
            $unidademedida = $unidademedidaModel->buscarPorId($id);

            if (!$unidademedida) {
                $_SESSION['erro_salvar'] = "Unidade de medida não encontrada.";
                header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/unidademedidas/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados da unidade de medida: " . $e->getMessage();
            header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['unidademedida_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/unidademedida_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['unidademedida_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['unidademedida_nome'] ?? ''),
                'sigla' => trim($_POST['unidademedida_sigla'] ?? ''),
                'descricao' => trim($_POST['unidademedida_descricao'] ?? '')
            ];

            if (empty($dados['nome']) || empty($dados['sigla'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome e Sigla).";
                header("Location: /iga/app/controllers/unidademedida_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo a própria unidade) usando o Model
                if ($unidademedidaModel->nomeExiste($dados['nome'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome de unidade de medida já está cadastrado.";
                    header("Location: /iga/app/controllers/unidademedida_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Verificar se sigla já existe (excluindo a própria unidade) usando o Model
                if ($unidademedidaModel->siglaExiste($dados['sigla'], $id)) {
                    $_SESSION['erro_salvar'] = "Esta sigla de unidade de medida já está cadastrada.";
                    header("Location: /iga/app/controllers/unidademedida_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($unidademedidaModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Unidade de medida atualizada com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar unidade de medida: " . $e->getMessage();
                header("Location: /iga/app/controllers/unidademedida_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVA UNIDADE DE MEDIDA
            $dados = [
                'nome' => trim($_POST['unidademedida_nome'] ?? ''),
                'sigla' => trim($_POST['unidademedida_sigla'] ?? ''),
                'descricao' => trim($_POST['unidademedida_descricao'] ?? '')
            ];

            if (empty($dados['nome']) || empty($dados['sigla'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome e Sigla).";
                header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome já existe usando o Model
                if ($unidademedidaModel->nomeExiste($dados['nome'])) {
                    $_SESSION['erro_salvar'] = "Este nome de unidade de medida já está cadastrado.";
                    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
                    exit;
                }

                // Verificar se sigla já existe usando o Model
                if ($unidademedidaModel->siglaExiste($dados['sigla'])) {
                    $_SESSION['erro_salvar'] = "Esta sigla de unidade de medida já está cadastrada.";
                    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($unidademedidaModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Unidade de medida cadastrada com sucesso!";
                    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar unidade de medida. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir unidade de medida: " . $e->getMessage());
                header('Location: /iga/app/controllers/unidademedida_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['unidademedida_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID da unidade de medida não fornecido.";
            header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se unidade de medida existe usando o Model
            $unidademedida = $unidademedidaModel->buscarPorId($id);
            if (!$unidademedida) {
                $_SESSION['erro_excluir'] = "Unidade de medida não encontrada.";
                header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir usando o Model
            if ($unidademedidaModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Unidade de medida excluída com sucesso!";
            } else {
                 // Se a exclusão falhar (ex: restrição de FK)
                 $_SESSION['erro_excluir'] = "Erro ao excluir unidade de medida. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir unidade de medida: " . $e->getMessage();
            error_log("Erro ao excluir unidade de medida ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/unidademedida_controller.php?acao=listar');
        break;
}
?>