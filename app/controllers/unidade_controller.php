<?php
# ========================================
# Controlador de Unidades de Saúde (CRUD Completo)
# Local: /app/controllers/unidade_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar unidades de saúde)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/unidadedesaude.php'; // Inclui o Model

$unidadeModel = new UnidadeDeSaude($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $unidades = $unidadeModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/unidades/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar unidades de saúde: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de unidades de saúde. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar dados para os dropdowns usando o Model
            $estados = $unidadeModel->listarEstados();
            $distritos = $unidadeModel->listarDistritos();
            $supervisores = $unidadeModel->listarSupervisores();

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/unidades/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID da unidade não fornecido.";
            header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
            exit;
        }

        try {
            // Buscar unidade usando o Model
            $unidade = $unidadeModel->buscarPorId($id);

            if (!$unidade) {
                $_SESSION['erro_salvar'] = "Unidade de saúde não encontrada.";
                header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
                exit;
            }

            // Buscar dados para os dropdowns usando o Model
            $estados = $unidadeModel->listarEstados();
            $distritos = $unidadeModel->listarDistritos();
            $supervisores = $unidadeModel->listarSupervisores();

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            require_once __DIR__ . '/../views/unidades/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados da unidade: " . $e->getMessage();
            header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['unidadedesaude_id'] ?? null;
            if ($redirect_id) {
                header("Location: /iga/app/controllers/unidade_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['unidadedesaude_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome_completo' => trim($_POST['unidadedesaude_nomecomp'] ?? ''),
                'nome_abreviado' => trim($_POST['unidadedesaude_nomeabrev'] ?? ''),
                'endereco' => trim($_POST['unidadedesaude_endereco'] ?? ''),
                'bairro' => trim($_POST['unidadedesaude_bairro'] ?? ''),
                'cidade' => trim($_POST['unidadedesaude_cidade'] ?? ''),
                'estado_id' => !empty($_POST['unidadedesaude_estado_id']) ? (int)$_POST['unidadedesaude_estado_id'] : null,
                'direcao_adm' => trim($_POST['unidadedesaude_direcaoadm'] ?? ''),
                'distrito_id' => !empty($_POST['unidadedesaude_distrito_id']) ? (int)$_POST['unidadedesaude_distrito_id'] : null,
                'supervisor_id' => !empty($_POST['unidadedesaude_supervisor_id']) ? (int)$_POST['unidadedesaude_supervisor_id'] : null,
            ];

            if (empty($dados['nome_completo']) || empty($dados['nome_abreviado']) || empty($dados['distrito_id']) || empty($dados['supervisor_id'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios.";
                header("Location: /iga/app/controllers/unidade_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se nome completo ou abreviado já existe (excluindo a própria unidade) usando o Model
                if ($unidadeModel->nomeCompletoExiste($dados['nome_completo'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome completo de unidade já está cadastrado.";
                    header("Location: /iga/app/controllers/unidade_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                if ($unidadeModel->nomeAbreviadoExiste($dados['nome_abreviado'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome abreviado de unidade já está cadastrado.";
                    header("Location: /iga/app/controllers/unidade_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($unidadeModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Unidade de saúde atualizada com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar unidade de saúde: " . $e->getMessage();
                header("Location: /iga/app/controllers/unidade_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVA UNIDADE
            $dados = [
                'nome_completo' => trim($_POST['unidadedesaude_nomecomp'] ?? ''),
                'nome_abreviado' => trim($_POST['unidadedesaude_nomeabrev'] ?? ''),
                'endereco' => trim($_POST['unidadedesaude_endereco'] ?? ''),
                'bairro' => trim($_POST['unidadedesaude_bairro'] ?? ''),
                'cidade' => trim($_POST['unidadedesaude_cidade'] ?? ''),
                'estado_id' => !empty($_POST['unidadedesaude_estado_id']) ? (int)$_POST['unidadedesaude_estado_id'] : null,
                'direcao_adm' => trim($_POST['unidadedesaude_direcaoadm'] ?? ''),
                'distrito_id' => !empty($_POST['unidadedesaude_distrito_id']) ? (int)$_POST['unidadedesaude_distrito_id'] : null,
                'supervisor_id' => !empty($_POST['unidadedesaude_supervisor_id']) ? (int)$_POST['unidadedesaude_supervisor_id'] : null,
            ];

            if (empty($dados['nome_completo']) || empty($dados['nome_abreviado']) || empty($dados['distrito_id']) || empty($dados['supervisor_id'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios.";
                header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome completo ou abreviado já existe usando o Model
                if ($unidadeModel->nomeCompletoExiste($dados['nome_completo'])) {
                    $_SESSION['erro_salvar'] = "Este nome completo de unidade já está cadastrado.";
                    header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
                    exit;
                }

                if ($unidadeModel->nomeAbreviadoExiste($dados['nome_abreviado'])) {
                    $_SESSION['erro_salvar'] = "Este nome abreviado de unidade já está cadastrado.";
                    header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($unidadeModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Unidade de saúde cadastrada com sucesso!";
                    header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar unidade de saúde. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir unidade de saúde: " . $e->getMessage());
                header('Location: /iga/app/controllers/unidade_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['unidadedesaude_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID da unidade não fornecido.";
            header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se unidade existe usando o Model
            $unidade = $unidadeModel->buscarPorId($id);
            if (!$unidade) {
                $_SESSION['erro_excluir'] = "Unidade de saúde não encontrada.";
                header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir usando o Model
            if ($unidadeModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Unidade de saúde excluída com sucesso!";
            } else {
                 $_SESSION['erro_excluir'] = "Erro ao excluir unidade de saúde.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir unidade de saúde: " . $e->getMessage();
            error_log("Erro ao excluir unidade de saúde ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/unidade_controller.php?acao=listar');
        break;
}
?>