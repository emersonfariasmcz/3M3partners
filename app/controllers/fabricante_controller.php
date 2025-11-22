<?php
# ========================================
# Controlador de Fabricantes (CRUD Completo)
# Local: /app/controllers/fabricante_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar fabricantes)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/fabricante.php';

$fabricanteModel = new Fabricante($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $fabricantes = $fabricanteModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/fabricantes/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar fabricantes: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de fabricantes. Por favor, tente novamente.";
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
            require_once __DIR__ . '/../views/fabricantes/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do fabricante não fornecido.";
            header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
            exit;
        }

        try {
            $fabricante = $fabricanteModel->buscarPorId($id);

            if (!$fabricante) {
                $_SESSION['erro_salvar'] = "Fabricante não encontrado.";
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/fabricantes/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do fabricante: " . $e->getMessage();
            header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['fabricante_id'] ?? null;
            if ($redirect_id) {
                header("Location: /iga/app/controllers/fabricante_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['fabricante_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['fabricante_nome'] ?? ''),
                'cnpj' => trim($_POST['fabricante_cnpj'] ?? ''),
                'telefone' => trim($_POST['fabricante_telefone'] ?? ''),
                'email' => trim($_POST['fabricante_email'] ?? ''),
                'endereco' => trim($_POST['fabricante_endereco'] ?? ''),
                'cidade' => trim($_POST['fabricante_cidade'] ?? ''),
                'estado' => trim($_POST['fabricante_estado'] ?? ''),
                'cep' => trim($_POST['fabricante_cep'] ?? ''),
                'observacoes' => trim($_POST['fabricante_observacoes'] ?? ''),
                'status' => $_POST['fabricante_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do fabricante.";
                header("Location: /iga/app/controllers/fabricante_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $fabricanteModel->cnpjExiste($dados['cnpj'], $id)) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado para outro fabricante.";
                header("Location: /iga/app/controllers/fabricante_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            if (!empty($dados['email']) && $fabricanteModel->emailExiste($dados['email'], $id)) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outro fabricante.";
                header("Location: /iga/app/controllers/fabricante_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                if ($fabricanteModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Fabricante atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar fabricante: " . $e->getMessage();
                header("Location: /iga/app/controllers/fabricante_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO FABRICANTE
            $dados = [
                'nome' => trim($_POST['fabricante_nome'] ?? ''),
                'cnpj' => trim($_POST['fabricante_cnpj'] ?? ''),
                'telefone' => trim($_POST['fabricante_telefone'] ?? ''),
                'email' => trim($_POST['fabricante_email'] ?? ''),
                'endereco' => trim($_POST['fabricante_endereco'] ?? ''),
                'cidade' => trim($_POST['fabricante_cidade'] ?? ''),
                'estado' => trim($_POST['fabricante_estado'] ?? ''),
                'cep' => trim($_POST['fabricante_cep'] ?? ''),
                'observacoes' => trim($_POST['fabricante_observacoes'] ?? ''),
                'status' => $_POST['fabricante_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do fabricante.";
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
                exit;
            }

            // Validações adicionais
            if (!empty($dados['cnpj']) && $fabricanteModel->cnpjExiste($dados['cnpj'])) {
                $_SESSION['erro_salvar'] = "Este CNPJ já está cadastrado.";
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
                exit;
            }

            if (!empty($dados['email']) && $fabricanteModel->emailExiste($dados['email'])) {
                $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
                exit;
            }

            try {
                if ($fabricanteModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Fabricante cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar fabricante. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir fabricante: " . $e->getMessage());
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['fabricante_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do fabricante não fornecido.";
            header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se fabricante existe
            $fabricante = $fabricanteModel->buscarPorId($id);
            if (!$fabricante) {
                $_SESSION['erro_excluir'] = "Fabricante não encontrado.";
                header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($fabricanteModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Fabricante excluído com sucesso!";
            } else {
                $_SESSION['erro_excluir'] = "Erro ao excluir fabricante. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir fabricante: " . $e->getMessage();
            error_log("Erro ao excluir fabricante ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/fabricante_controller.php?acao=listar');
        break;
}
?>