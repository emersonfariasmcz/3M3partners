<?php
# ========================================
# Controlador de Vendedores (CRUD Completo)
# Local: /app/controllers/vendedor_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar vendedores)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/vendedor.php'; // Inclui o Model

$vendedorModel = new vendedor($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $vendedores = $vendedorModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/vendedores/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar vendedores: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de vendedores. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar dados para os dropdowns usando o Model
            $estados = $vendedorModel->listarEstados();
            $vendedores = $vendedorModel->listarVendedoresAtivos(); // Para o dropdown de supervisores

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/vendedores/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do vendedor não fornecido.";
            header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
            exit;
        }

        try {
            // Buscar vendedor usando o Model
            $vendedor = $vendedorModel->buscarPorId($id);

            if (!$vendedor) {
                $_SESSION['erro_salvar'] = "Vendedor não encontrado.";
                header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
                exit;
            }

            // Buscar dados para os dropdowns usando o Model
            $estados = $vendedorModel->listarEstados();
            $vendedores = $vendedorModel->listarVendedoresAtivos(); // Para o dropdown de supervisores

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/vendedores/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do vendedor: " . $e->getMessage();
            header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['vendedor_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['vendedor_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['vendedor_nome'] ?? ''),
                'matricula' => trim($_POST['vendedor_matricula'] ?? ''),
                'cpf' => trim($_POST['vendedor_cpf'] ?? ''),
                'endereco' => trim($_POST['vendedor_endereco'] ?? ''),
                'bairro' => trim($_POST['vendedor_bairro'] ?? ''),
                'cidade' => trim($_POST['vendedor_cidade'] ?? ''),
                'estado_id' => !empty($_POST['vendedor_estado_id']) ? (int)$_POST['vendedor_estado_id'] : null,
                'cep' => trim($_POST['vendedor_cep'] ?? ''),
                'telefone' => trim($_POST['vendedor_telefone'] ?? ''),
                'email' => trim($_POST['vendedor_email'] ?? ''),
                'comissao_percentual' => (float)($_POST['vendedor_comissao_percentual'] ?? 0.00),
                'data_admissao' => trim($_POST['vendedor_data_admissao'] ?? ''),
                'supervisor_id' => !empty($_POST['vendedor_supervisor_id']) ? (int)$_POST['vendedor_supervisor_id'] : null,
                'observacoes' => trim($_POST['vendedor_observacoes'] ?? ''),
                'status' => $_POST['vendedor_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome).";
                header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se CPF já existe (excluindo o próprio vendedor) usando o Model
                if (!empty($dados['cpf']) && $vendedorModel->cpfExiste($dados['cpf'], $id)) {
                    $_SESSION['erro_salvar'] = "Este CPF já está cadastrado para outro vendedor.";
                    header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Verificar se E-mail já existe (excluindo o próprio vendedor) usando o Model
                if (!empty($dados['email']) && $vendedorModel->emailExiste($dados['email'], $id)) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado para outro vendedor.";
                    header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Verificar se Matrícula já existe (excluindo o próprio vendedor) usando o Model
                if (!empty($dados['matricula']) && $vendedorModel->matriculaExiste($dados['matricula'], $id)) {
                    $_SESSION['erro_salvar'] = "Esta matrícula já está cadastrada para outro vendedor.";
                    header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($vendedorModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Vendedor atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar vendedor: " . $e->getMessage();
                header("Location: /iga/app/controllers/vendedor_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO VENDEDOR
            $dados = [
                'nome' => trim($_POST['vendedor_nome'] ?? ''),
                'matricula' => trim($_POST['vendedor_matricula'] ?? ''),
                'cpf' => trim($_POST['vendedor_cpf'] ?? ''),
                'endereco' => trim($_POST['vendedor_endereco'] ?? ''),
                'bairro' => trim($_POST['vendedor_bairro'] ?? ''),
                'cidade' => trim($_POST['vendedor_cidade'] ?? ''),
                'estado_id' => !empty($_POST['vendedor_estado_id']) ? (int)$_POST['vendedor_estado_id'] : null,
                'cep' => trim($_POST['vendedor_cep'] ?? ''),
                'telefone' => trim($_POST['vendedor_telefone'] ?? ''),
                'email' => trim($_POST['vendedor_email'] ?? ''),
                'comissao_percentual' => (float)($_POST['vendedor_comissao_percentual'] ?? 0.00),
                'data_admissao' => trim($_POST['vendedor_data_admissao'] ?? ''),
                'supervisor_id' => !empty($_POST['vendedor_supervisor_id']) ? (int)$_POST['vendedor_supervisor_id'] : null,
                'observacoes' => trim($_POST['vendedor_observacoes'] ?? ''),
                'status' => $_POST['vendedor_status'] ?? 'ativo'
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome).";
                header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se CPF já existe usando o Model
                if (!empty($dados['cpf']) && $vendedorModel->cpfExiste($dados['cpf'])) {
                    $_SESSION['erro_salvar'] = "Este CPF já está cadastrado.";
                    header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                    exit;
                }

                // Verificar se E-mail já existe usando o Model
                if (!empty($dados['email']) && $vendedorModel->emailExiste($dados['email'])) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                    header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                    exit;
                }

                // Verificar se Matrícula já existe usando o Model
                if (!empty($dados['matricula']) && $vendedorModel->matriculaExiste($dados['matricula'])) {
                    $_SESSION['erro_salvar'] = "Esta matrícula já está cadastrada.";
                    header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($vendedorModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Vendedor cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar vendedor. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir vendedor: " . $e->getMessage());
                header('Location: /iga/app/controllers/vendedor_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['vendedor_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do vendedor não fornecido.";
            header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se vendedor existe usando o Model
            $vendedor = $vendedorModel->buscarPorId($id);
            if (!$vendedor) {
                $_SESSION['erro_excluir'] = "Vendedor não encontrado.";
                header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir usando o Model
            if ($vendedorModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Vendedor excluído com sucesso!";
            } else {
                 $_SESSION['erro_excluir'] = "Erro ao excluir vendedor.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir vendedor: " . $e->getMessage();
            error_log("Erro ao excluir vendedor ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/vendedor_controller.php?acao=listar');
        break;
}
?>