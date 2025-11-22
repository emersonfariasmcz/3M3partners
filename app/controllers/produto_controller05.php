<?php
# ========================================
# Controlador de Produtos (CRUD Completo)
# Local: /app/controllers/produto_controller.php
# ========================================

// Ativar exibição de erros para desenvolvimento
// IMPORTANTE: Em produção, considere desativar e usar logs
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

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Consulta para listar produtos com informações de categoria
            $sql = "SELECT p.*, c.categoria_nome
                    FROM produtos p
                    LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
                    ORDER BY p.produto_nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            // echo "<h3>Erro ao carregar lista de produtos</h3>";
            // echo "<p>Por favor, tente novamente mais tarde.</p>";
            // echo "<p><a href='/iga/app/views/dashboard.php'>Voltar ao Dashboard</a></p>";
        }
        break;

    case 'criar':
        try 
        {
          // === TESTE DE DEBUG ===
          error_log("DEBUG PRODUTO: Tentando verificar se create.php existe e é acessível.");
          // === FIM TESTE DE DEBUG ===

            // === CORREÇÃO AQUI ===
            // Buscar categorias para o dropdown - CONSULTA CORRIGIDA
            $sqlCategorias = "SELECT categoria_id, categoria_nome FROM categorias WHERE categoria_status = 'ativo' ORDER BY categoria_nome ASC";
            $stmtCategorias = $pdo->prepare($sqlCategorias);
            $stmtCategorias->execute();
            $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
            // === FIM CORREÇÃO ===

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
            // Buscar produto
            $sql = "SELECT * FROM produtos WHERE produto_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto) {
                 // Buscar categorias para o dropdown - CONSULTA CORRIGIDA
                $sqlCategorias = "SELECT categoria_id, categoria_nome FROM categorias WHERE categoria_status = 'ativo' ORDER BY categoria_nome ASC";
                $stmtCategorias = $pdo->prepare($sqlCategorias);
                $stmtCategorias->execute();
                $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

                // Buscar mensagens de erro
                $erro = $_SESSION['erro_salvar'] ?? '';
                unset($_SESSION['erro_salvar']);

                require_once __DIR__ . '/../views/produtos/edit.php';
            } else {
                $_SESSION['erro_salvar'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do produto: " . $e->getMessage();
            header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            // Redireciona corretamente dependendo se é edição ou criação
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
            $nome = trim($_POST['produto_nome']);
            $descricao = trim($_POST['produto_descricao'] ?? '');
            $codigo_barras = trim($_POST['produto_codigo_barras'] ?? '');
            $unidade_medida = trim($_POST['produto_unidade_medida']);
            $estoque_minimo = (int)($_POST['produto_estoque_minimo'] ?? 0);
            $estoque_maximo = (int)($_POST['produto_estoque_maximo'] ?? 0);
            $preco_custo = (float)($_POST['produto_preco_custo'] ?? 0);
            $preco_venda = (float)($_POST['produto_preco_venda'] ?? 0);
            $categoria_id = (int)$_POST['categoria_id'];
            $status = $_POST['produto_status'] ?? 'ativo';

            if (empty($nome) || empty($unidade_medida) || empty($categoria_id)) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios.";
                header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=$id");
                exit;
            }

            try {
                // Verificar se nome já existe (excluindo o próprio produto)
                $sqlVerifica = "SELECT COUNT(*) FROM produtos WHERE produto_nome = ? AND produto_id != ?";
                $stmtVerifica = $pdo->prepare($sqlVerifica);
                $stmtVerifica->execute([$nome, $id]);

                if ($stmtVerifica->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=$id");
                    exit;
                }

                // Verificar se código de barras já existe (excluindo o próprio produto)
                if (!empty($codigo_barras)) {
                    $sqlVerificaBarras = "SELECT COUNT(*) FROM produtos WHERE produto_codigo_barras = ? AND produto_id != ?";
                    $stmtVerificaBarras = $pdo->prepare($sqlVerificaBarras);
                    $stmtVerificaBarras->execute([$codigo_barras, $id]);

                    if ($stmtVerificaBarras->fetchColumn() > 0) {
                        $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                        header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=$id");
                        exit;
                    }
                }

                $sqlUpdate = "UPDATE produtos SET
                             produto_nome = ?,
                             produto_descricao = ?,
                             produto_codigo_barras = ?,
                             produto_unidade_medida = ?,
                             produto_estoque_minimo = ?,
                             produto_estoque_maximo = ?,
                             produto_preco_custo = ?,
                             produto_preco_venda = ?,
                             categoria_id = ?,
                             produto_status = ?
                             WHERE produto_id = ?";

                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    $nome, $descricao, $codigo_barras, $unidade_medida,
                    $estoque_minimo, $estoque_maximo, $preco_custo, $preco_venda,
                    $categoria_id, $status, $id
                ]);

                if ($stmtUpdate->rowCount() > 0) {
                    $_SESSION['sucesso_salvar'] = "Produto atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada."; // Pode ser sucesso também, só não mudou nada
                }

                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar produto: " . $e->getMessage();
                header("Location: /iga/app/controllers/produto_controller.php?acao=editar&id=$id");
                exit;
            }

        } else {
            // NOVO PRODUTO
            $nome = trim($_POST['produto_nome']);
            $descricao = trim($_POST['produto_descricao'] ?? '');
            $codigo_barras = trim($_POST['produto_codigo_barras'] ?? '');
            $unidade_medida = trim($_POST['produto_unidade_medida']);
            $estoque_minimo = (int)($_POST['produto_estoque_minimo'] ?? 0);
            $estoque_maximo = (int)($_POST['produto_estoque_maximo'] ?? 0);
            $preco_custo = (float)($_POST['produto_preco_custo'] ?? 0);
            $preco_venda = (float)($_POST['produto_preco_venda'] ?? 0);
            $categoria_id = (int)$_POST['categoria_id'];
            $status = $_POST['produto_status'] ?? 'ativo';

            if (empty($nome) || empty($unidade_medida) || empty($categoria_id)) {
                $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                exit;
            }

            try {
                // Verificar se nome já existe
                $sqlVerifica = "SELECT COUNT(*) FROM produtos WHERE produto_nome = ?";
                $stmtVerifica = $pdo->prepare($sqlVerifica);
                $stmtVerifica->execute([$nome]);

                if ($stmtVerifica->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este nome de produto já está cadastrado.";
                    header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                    exit;
                }

                // Verificar se código de barras já existe
                if (!empty($codigo_barras)) {
                    $sqlVerificaBarras = "SELECT COUNT(*) FROM produtos WHERE produto_codigo_barras = ?";
                    $stmtVerificaBarras = $pdo->prepare($sqlVerificaBarras);
                    $stmtVerificaBarras->execute([$codigo_barras]);

                    if ($stmtVerificaBarras->fetchColumn() > 0) {
                        $_SESSION['erro_salvar'] = "Este código de barras já está cadastrado.";
                        header('Location: /iga/app/controllers/produto_controller.php?acao=criar');
                        exit;
                    }
                }

                $sqlInserir = "INSERT INTO produtos
                              (produto_nome, produto_descricao, produto_codigo_barras, produto_unidade_medida,
                               produto_estoque_minimo, produto_estoque_maximo, produto_preco_custo,
                               produto_preco_venda, categoria_id, produto_status)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtInserir = $pdo->prepare($sqlInserir);
                $stmtInserir->execute([
                    $nome, $descricao, $codigo_barras, $unidade_medida,
                    $estoque_minimo, $estoque_maximo, $preco_custo, $preco_venda,
                    $categoria_id, $status
                ]);

                if ($stmtInserir->rowCount() > 0) {
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
                // Log do erro para depuração
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
            // Verificar se produto existe
            $sqlVerifica = "SELECT produto_id FROM produtos WHERE produto_id = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$id]);

            if ($stmtVerifica->rowCount() === 0) {
                $_SESSION['erro_excluir'] = "Produto não encontrado.";
                header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
                exit;
            }

            // Excluir o produto
            $sql = "DELETE FROM produtos WHERE produto_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $_SESSION['sucesso_excluir'] = "Produto excluído com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir produto: " . $e->getMessage();
            // Log do erro para depuração
            error_log("Erro ao excluir produto ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/produto_controller.php?acao=listar');
        break;
}
?>