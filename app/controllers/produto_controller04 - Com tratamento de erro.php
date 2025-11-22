<?php
// --- TESTE DE DEBUG INICIAL ---
// Se esta mensagem aparecer no navegador ao acessar o controller,
// significa que este arquivo esta sendo executado.
 echo "<h1>TESTE: Novo produto_controller.php esta sendo executado!</h1>";
 exit; // Impede a execucao do resto do codigo para isolar o teste
// --- FIM TESTE DE DEBUG ---

# ========================================
# Controlador de Produtos (CRUD Completo)
# Local: /app/controllers/produto_controller.php
# ========================================




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
    error_log("Produto Controller: Usuário não autenticado. Redirecionando para login.");
    header('Location: /iga/app/views/login.php');
    exit;
}

// Verificar permissões (apenas administradores podem gerenciar produtos)
$acao = $_REQUEST['acao'] ?? 'listar';
error_log("Produto Controller: Ação solicitada: '$acao'");
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    error_log("Produto Controller: Acesso negado para papel '" . $_SESSION['usuario_papel'] . "' na ação '$acao'.");
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/produto.php';

$produtoModel = new Produto($pdo);
error_log("Produto Controller: Model instanciado com sucesso.");

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        error_log("Produto Controller: Executando ação 'listar'");
        try {
            $produtos = $produtoModel->listarTodos();
            error_log("Produto Controller: Listagem obtida com sucesso. Total de produtos: " . count($produtos));

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';
            
            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);
            
            error_log("Produto Controller: Incluindo view de listagem.");
            require_once __DIR__ . '/../views/produtos/index.php';
            error_log("Produto Controller: View de listagem incluída.");
            
        } catch (PDOException $e) {
            error_log("Produto Controller: Erro PDO ao listar produtos: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de produtos. Por favor, tente novamente.";
            header('Location: /iga/app/views/dashboard.php');
            exit;
        } catch (Exception $e) {
             error_log("Produto Controller: Erro geral ao listar produtos: " . $e->getMessage());
             $_SESSION['erro_geral'] = "Erro inesperado ao carregar a lista de produtos. Por favor, tente novamente.";
             header('Location: /iga/app/views/dashboard.php');
             exit;
        }
        break;
        
    case 'criar':
        error_log("Produto Controller: Executando ação 'criar'");
        try {
            error_log("Produto Controller: Buscando categorias para dropdown.");
            $categorias = $produtoModel->listarCategoriasAtivas();
            error_log("Produto Controller: Categorias buscadas. Total: " . count($categorias));

            // Buscar mensagens de erro
            $erro = $_SESSION['erro_salvar'] ?? '';
            unset($_SESSION['erro_salvar']);

            error_log("Produto Controller: Incluindo view de criação.");
            require_once __DIR__ . '/../views/produtos/create.php';
            error_log("Produto Controller: View de criação incluída.");
            
        } catch (PDOException $e) {
            error_log("Produto Controller: Erro PDO ao carregar formulário de criação: " . $e->getMessage());
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        } catch (Exception $e) {
             error_log("Produto Controller: Erro geral ao carregar formulário de criação: " . $e->getMessage());
             $_SESSION['erro_salvar'] = "Erro inesperado ao carregar formulário: " . $e->getMessage();
             header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
             exit;
        }
        break;
        
    case 'editar':
        error_log("Produto Controller: Executando ação 'editar'");
        $id = $_GET['id'] ?? null;
        if (!$id) {
            error_log("Produto Controller: ID do produto não fornecido para edição.");
            $_SESSION['erro_salvar'] = "ID do produto não fornecido.";
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }

        try {
            error_log("Produto Controller: Buscando produto ID $id para edição.");
            $produto = $produtoModel->buscarPorId($id);

            if (!$produto) {
                error_log("Produto Controller: Produto ID $id não encontrado para edição.");
                $_SESSION['erro_salvar'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }
            
            error_log("Produto Controller: Buscando categorias para dropdown na edição.");
            $categorias = $produtoModel->listarCategoriasAtivas();
            
            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            error_log("Produto Controller: Incluindo view de edição.");
            require_once __DIR__ . '/../views/produtos/edit.php';
            error_log("Produto Controller: View de edição incluída.");
            
        } catch (PDOException $e) {
            error_log("Produto Controller: Erro PDO ao carregar dados do produto ID $id: " . $e->getMessage());
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do produto: " . $e->getMessage();
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        } catch (Exception $e) {
             error_log("Produto Controller: Erro geral ao carregar dados do produto ID $id: " . $e->getMessage());
             $_SESSION['erro_salvar'] = "Erro inesperado ao carregar dados do produto: " . $e->getMessage();
             header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
             exit;
        }
        break;

    case 'salvar':
        error_log("Produto Controller: Executando ação 'salvar'");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Produto Controller: Método de requisição inválido para salvar.");
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
            error_log("Produto Controller: Salvando edição do produto ID $id.");
            $dados = [
                'nome' => trim($_POST['produto_nome'] ?? ''),
                'descricao' => trim($_POST['produto_descricao'] ?? ''),
                'codigo_barras' => trim($_POST['produto_codigo_barras'] ?? ''),
                'unidade_medida' => trim($_POST['produto_unidade_medida'] ?? 'UN'),
                'estoque_minimo' => (int)($_POST['produto_estoque_minimo'] ?? 0),
                'estoque_maximo' => (int)($_POST['produto_estoque_maximo'] ?? 0),
                'preco_custo' => (float)($_POST['produto_preco_custo'] ?? 0.00),
                'preco_venda' => (float)($_POST['produto_preco_venda'] ?? 0.00),
                'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
                'status' => $_POST['produto_status'] ?? 'ativo'
            ];

            if (empty($dados['nome']) || empty($dados['categoria_id'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha os campos obrigatórios (Nome e Categoria).";
                header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo o próprio produto)
                if ($produtoModel->nomeExiste($dados['nome'], $id)) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }
                
                // Verificar se código de barras já existe (excluindo o próprio produto)
                if (!empty($dados['codigo_barras']) && $produtoModel->codigoBarrasExiste($dados['codigo_barras'], $id)) {
                    $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                    header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=" . urlencode($id));
                    exit;
                }

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
            error_log("Produto Controller: Salvando novo produto.");
            $dados = [
                'nome' => trim($_POST['produto_nome'] ?? ''),
                'descricao' => trim($_POST['produto_descricao'] ?? ''),
                'codigo_barras' => trim($_POST['produto_codigo_barras'] ?? ''),
                'unidade_medida' => trim($_POST['produto_unidade_medida'] ?? 'UN'),
                'estoque_minimo' => (int)($_POST['produto_estoque_minimo'] ?? 0),
                'estoque_maximo' => (int)($_POST['produto_estoque_maximo'] ?? 0),
                'preco_custo' => (float)($_POST['produto_preco_custo'] ?? 0.00),
                'preco_venda' => (float)($_POST['produto_preco_venda'] ?? 0.00),
                'categoria_id' => (int)($_POST['categoria_id'] ?? 0),
                'status' => $_POST['produto_status'] ?? 'ativo'
            ];

            if (empty($dados['nome']) || empty($dados['categoria_id'])) {
                $_SESSION['erro_salvar'] = "Por favor, preencha os campos obrigatórios (Nome e Categoria).";
                header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome já existe
                if ($produtoModel->nomeExiste($dados['nome'])) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }
                
                // Verificar se código de barras já existe
                if (!empty($dados['codigo_barras']) && $produtoModel->codigoBarrasExiste($dados['codigo_barras'])) {
                    $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }

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
                error_log("Produto Controller: Erro ao inserir produto: " . $e->getMessage());
                header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        error_log("Produto Controller: Executando ação 'excluir'");
        $id = $_POST['produto_id'] ?? null;
        
        if (!$id) {
            error_log("Produto Controller: ID do produto não fornecido para exclusão.");
            $_SESSION['erro_excluir'] = "ID do produto não fornecido.";
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }

        try {
            // Verificar se produto existe
            $produto = $produtoModel->buscarPorId($id);
            if (!$produto) {
                error_log("Produto Controller: Produto ID $id não encontrado para exclusão.");
                $_SESSION['erro_excluir'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }
            
            if ($produtoModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Produto excluído com sucesso!";
            } else {
                $_SESSION['erro_excluir'] = "Erro ao excluir produto.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir produto: " . $e->getMessage();
            error_log("Produto Controller: Erro ao excluir produto ID $id: " . $e->getMessage());
        }
        
        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        exit;
        
    default:
        error_log("Produto Controller: Ação '$acao' não reconhecida. Redirecionando para 'listar'.");
        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        break;
}
error_log("Produto Controller: Finalizando script.");
?>