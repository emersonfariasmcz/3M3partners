<?php
# ========================================
# Controlador de Clientes (CRUD Completo)
# Local: /app/controllers/cliente_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar clientes)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/cliente.php'; // Inclui o Model

$clienteModel = new cliente($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $clientes = $clienteModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/clientes/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar clientes: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de clientes. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar dados para os dropdowns usando o Model
            $estados = $clienteModel->listarEstados();

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/clientes/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do cliente não fornecido.";
            header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
            exit;
        }

        try {
            // Buscar cliente usando o Model
            $cliente = $clienteModel->buscarPorId($id);

            if (!$cliente) {
                $_SESSION['erro_salvar'] = "Cliente não encontrado.";
                header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
                exit;
            }

            // Buscar dados para os dropdowns usando o Model
            $estados = $clienteModel->listarEstados();

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/clientes/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do cliente: " . $e->getMessage();
            header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['cliente_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/cliente_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['cliente_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['cliente_nome'] ?? ''),
                'nome_fantasia' => trim($_POST['cliente_nome_fantasia'] ?? ''),
                'cnpj_cpf' => trim($_POST['cliente_cnpj_cpf'] ?? ''),
                'inscricao_estadual' => trim($_POST['cliente_inscricao_estadual'] ?? ''),
                'endereco' => trim($_POST['cliente_endereco'] ?? ''),
                'bairro' => trim($_POST['cliente_bairro'] ?? ''),
                'cidade' => trim($_POST['cliente_cidade'] ?? ''),
                'estado_id' => !empty($_POST['cliente_estado_id']) ? (int)$_POST['cliente_estado_id'] : null,
                'cep' => trim($_POST['cliente_cep'] ?? ''),
                'telefone' => trim($_POST['cliente_telefone'] ?? ''),
                'telefone_secundario' => trim($_POST['cliente_telefone_secundario'] ?? ''),
                'email' => trim($_POST['cliente_email'] ?? ''),
                'contato_principal' => trim($_POST['cliente_contato_principal'] ?? ''),
                'observacoes' => trim($_POST['cliente_observacoes'] ?? ''),
                'status' => $_POST['cliente_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome).";
                header("Location: /iga/app/controllers/cliente_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se CNPJ/CPF já existe (excluindo o próprio cliente)
                if (!empty($dados['cnpj_cpf']) && $clienteModel->cnpjCpfExiste($dados['cnpj_cpf'], $id)) {
                    $_SESSION['erro_salvar'] = "Este CNPJ/CPF já está cadastrado para outro cliente.";
                    header("Location: /iga/app/controllers/cliente_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Verificar se e-mail já existe (excluindo o próprio cliente)
                if (!empty($dados['email']) && $clienteModel->emailExiste($dados['email'], $id)) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outro cliente.";
                    header("Location: /iga/app/controllers/cliente_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($clienteModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Cliente atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar cliente: " . $e->getMessage();
                header("Location: /iga/app/controllers/cliente_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO CLIENTE
            $dados = [
                'nome' => trim($_POST['cliente_nome'] ?? ''),
                'nome_fantasia' => trim($_POST['cliente_nome_fantasia'] ?? ''),
                'cnpj_cpf' => trim($_POST['cliente_cnpj_cpf'] ?? ''),
                'inscricao_estadual' => trim($_POST['cliente_inscricao_estadual'] ?? ''),
                'endereco' => trim($_POST['cliente_endereco'] ?? ''),
                'bairro' => trim($_POST['cliente_bairro'] ?? ''),
                'cidade' => trim($_POST['cliente_cidade'] ?? ''),
                'estado_id' => !empty($_POST['cliente_estado_id']) ? (int)$_POST['cliente_estado_id'] : null,
                'cep' => trim($_POST['cliente_cep'] ?? ''),
                'telefone' => trim($_POST['cliente_telefone'] ?? ''),
                'telefone_secundario' => trim($_POST['cliente_telefone_secundario'] ?? ''),
                'email' => trim($_POST['cliente_email'] ?? ''),
                'contato_principal' => trim($_POST['cliente_contato_principal'] ?? ''),
                'observacoes' => trim($_POST['cliente_observacoes'] ?? ''),
                'status' => $_POST['cliente_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome).";
                header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se CNPJ/CPF já existe
                if (!empty($dados['cnpj_cpf']) && $clienteModel->cnpjCpfExiste($dados['cnpj_cpf'])) {
                    $_SESSION['erro_salvar'] = "Este CNPJ/CPF já está cadastrado.";
                    header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
                    exit;
                }

                // Verificar se e-mail já existe
                if (!empty($dados['email']) && $clienteModel->emailExiste($dados['email'])) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                    header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($clienteModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Cliente cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar cliente. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir cliente: " . $e->getMessage());
                header('Location: /iga/app/controllers/cliente_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['cliente_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do cliente não fornecido.";
            header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se cliente existe usando o Model
            $cliente = $clienteModel->buscarPorId($id);
            if (!$cliente) {
                $_SESSION['erro_excluir'] = "Cliente não encontrado.";
                header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir usando o Model
            if ($clienteModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Cliente excluído com sucesso!";
            } else {
                 $_SESSION['erro_excluir'] = "Erro ao excluir cliente. Pode estar em uso.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir cliente: " . $e->getMessage();
            error_log("Erro ao excluir cliente ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/cliente_controller.php?acao=listar');
        break;
}
?>