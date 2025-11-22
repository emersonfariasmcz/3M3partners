<?php
# ========================================
# Controlador de Produtos (CRUD Completo)
# Local: /app/controllers/produto_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar produtos)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/produto.php'; // Inclui o Model

$produtoModel = new produto($pdo); // Instancia o Model

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Usa o Model para buscar os dados
            $produtos = $produtoModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            require_once __DIR__ . '/../views/produtos/index.php';

        } catch (PDOException $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de produtos. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'criar':
        try {
            // Buscar dados para os dropdowns usando o Model
            $categorias = $produtoModel->listarCategoriasAtivas();
            $fabricantes = $produtoModel->listarFabricantesAtivos();
            $fornecedores = $produtoModel->listarFornecedoresAtivos();
            // === ADIÇÃO: Buscar unidades de medida ===
            $unidadesMedida = $produtoModel->listarUnidadesMedidaAtivas();
            // =========================================

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            // Incluir a view de criação
            require_once __DIR__ . '/../views/produtos/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do produto não fornecido.";
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }

        try {
            // Buscar produto usando o Model
            $produto = $produtoModel->buscarPorId($id);

            if (!$produto) {
                $_SESSION['erro_salvar'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }

            // Buscar dados para os dropdowns usando o Model
            $categorias = $produtoModel->listarCategoriasAtivas();
            $fabricantes = $produtoModel->listarFabricantesAtivos();
            $fornecedores = $produtoModel->listarFornecedoresAtivos();
            // === ADIÇÃO: Buscar unidades de medida ===
            $unidadesMedida = $produtoModel->listarUnidadesMedidaAtivas();
            // =========================================

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/produtos/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do produto: " . $e->getMessage();
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['produto_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['produto_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['produto_nome'] ?? ''),
                'descricao' => trim($_POST['produto_descricao'] ?? ''),
                'codigo_barras' => trim($_POST['produto_codigo_barras'] ?? ''),
                // === ALTERAÇÃO: Usar unidade_medida_id ===
                'unidade_medida_id' => !empty($_POST['produto_unidade_medida_id']) ? (int)$_POST['produto_unidade_medida_id'] : null,
                // ======================================
                'estoque_minimo' => (int)($_POST['produto_estoque_minimo'] ?? 0),
                'estoque_maximo' => (int)($_POST['produto_estoque_maximo'] ?? 0),
                'preco_custo' => (float)($_POST['produto_preco_custo'] ?? 0.00),
                'preco_venda' => (float)($_POST['produto_preco_venda'] ?? 0.00),
                'categoria_id' => (int)$_POST['categoria_id'],
                'fabricante_id' => !empty($_POST['fabricante_id']) ? (int)$_POST['fabricante_id'] : null,
                'fornecedor_id' => !empty($_POST['fornecedor_id']) ? (int)$_POST['fornecedor_id'] : null,
                'status' => $_POST['produto_status'] ?? 'ativo'
            ];

            if (empty($dados['nome']) || empty($dados['categoria_id']) || empty($dados['unidade_medida_id'])) { // Validar unidade_medida_id
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome, Categoria e Unidade de Medida).";
                header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo o próprio produto) usando o Model
                if ($produtoModel->nomeExiste($dados['nome'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Verificar se código de barras já existe (excluindo o próprio produto) usando o Model
                if (!empty($dados['codigo_barras']) && $produtoModel->codigoBarrasExiste($dados['codigo_barras'], $id)) {
                    $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                    header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

                // Atualiza usando o Model
                if ($produtoModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Produto atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar produto: " . $e->getMessage();
                header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO PRODUTO
            $dados = [
                'nome' => trim($_POST['produto_nome'] ?? ''),
                'descricao' => trim($_POST['produto_descricao'] ?? ''),
                'codigo_barras' => trim($_POST['produto_codigo_barras'] ?? ''),
                // === ALTERAÇÃO: Usar unidade_medida_id ===
                'unidade_medida_id' => !empty($_POST['produto_unidade_medida_id']) ? (int)$_POST['produto_unidade_medida_id'] : null,
                // ======================================
                'estoque_minimo' => (int)($_POST['produto_estoque_minimo'] ?? 0),
                'estoque_maximo' => (int)($_POST['produto_estoque_maximo'] ?? 0),
                'preco_custo' => (float)($_POST['produto_preco_custo'] ?? 0.00),
                'preco_venda' => (float)($_POST['produto_preco_venda'] ?? 0.00),
                'categoria_id' => (int)$_POST['categoria_id'],
                'fabricante_id' => !empty($_POST['fabricante_id']) ? (int)$_POST['fabricante_id'] : null,
                'fornecedor_id' => !empty($_POST['fornecedor_id']) ? (int)$_POST['fornecedor_id'] : null,
                'status' => $_POST['produto_status'] ?? 'ativo'
            ];

            if (empty($dados['nome']) || empty($dados['categoria_id']) || empty($dados['unidade_medida_id'])) { // Validar unidade_medida_id
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios (Nome, Categoria e Unidade de Medida).";
                header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome já existe usando o Model
                if ($produtoModel->nomeExiste($dados['nome'])) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }

                // Verificar se código de barras já existe usando o Model
                if (!empty($dados['codigo_barras']) && $produtoModel->codigoBarrasExiste($dados['codigo_barras'])) {
                    $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }

                // Cria usando o Model
                if ($produtoModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Produto cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar produto. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir produto: " . $e->getMessage());
                header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['produto_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do produto não fornecido.";
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se produto existe usando o Model
            $produto = $produtoModel->buscarPorId($id);
            if (!$produto) {
                $_SESSION['erro_excluir'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir usando o Model
            if ($produtoModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Produto excluído com sucesso!";
            } else {
                 $_SESSION['erro_excluir'] = "Erro ao excluir produto.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir produto: " . $e->getMessage();
            error_log("Erro ao excluir produto ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        break;
}
?>