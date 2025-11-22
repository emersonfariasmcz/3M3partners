<?php
# ========================================
# Controlador de Usuários (CRUD Completo)
# Local: /app/controllers/usuario_controller.php
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

// VERIFICAÇÃO DE PERMISSÃO - IMPLEMENTAÇÃO NOVA
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/controllers/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            // Consulta para listar usuários com seus papéis
            $sql = "SELECT u.*, p.usuariopapel_nome 
                    FROM usuarios u
                    JOIN usuariopapeis p ON u.papel_id = p.usuariopapel_id
                    ORDER BY u.usuario_nome ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Buscar papéis para uso geral
            $sqlPapeis = "SELECT * FROM usuariopapeis ORDER BY usuariopapel_nome";
            $stmtPapeis = $pdo->query($sqlPapeis);
            $papeis = $stmtPapeis->fetchAll(PDO::FETCH_ASSOC);
            
            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';
            
            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);
            
            // Incluir a view de listagem
            require_once __DIR__ . '/../views/usuarios/index.php';
            
        } catch (PDOException $e) {
            // Log do erro
            error_log("Erro ao listar usuários: " . $e->getMessage());
            
            // Mensagem amigável para o usuário
            echo "<h3>Erro ao carregar lista de usuários</h3>";
            echo "<p>Por favor, tente novamente mais tarde.</p>";
            echo "<p><a href='/iga/app/views/dashboard.php'>Voltar ao Dashboard</a></p>";
        }
        break;
        
    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do usuário não fornecido.";
            header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
            exit;
        }

        try {
            $sql = "SELECT * FROM usuarios WHERE usuario_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Busca os papéis de usuário para o dropdown
            $sql_papeis = "SELECT * FROM usuariopapeis";
            $stmt_papeis = $pdo->query($sql_papeis);
            $papeis = $stmt_papeis->fetchAll(PDO::FETCH_ASSOC);

            if ($usuario) {
                require_once __DIR__ . '/../views/usuarios/edit.php';
            } else {
                $_SESSION['erro_salvar'] = "Usuário não encontrado.";
                header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do usuário: " . $e->getMessage();
            header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        // Verificar se é uma requisição POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            header('Location: /iga/app/views/usuarios/create.php');
            exit;
        }

        // Verificar se é edição (tem usuario_id) ou novo cadastro
        $id = $_POST['usuario_id'] ?? null;
        
        if ($id) {
            // É uma EDIÇÃO
            $nome = trim($_POST['usuario_nome']);
            $email = filter_var(trim($_POST['usuario_email']), FILTER_VALIDATE_EMAIL);
            $login = trim($_POST['usuario_login']);
            $papel_id = (int)$_POST['papel_id'];
            $senha = $_POST['usuario_senha'] ?? '';

            if (!$email) {
                $_SESSION['erro_salvar'] = "Por favor, informe um e-mail válido.";
                header("Location: /iga/app/controllers/usuario_controller.php?acao=editar&id=$id");
                exit;
            }

            try {
                // Verificar se e-mail já existe (excluindo o próprio usuário)
                $sqlVerificaEmail = "SELECT COUNT(*) FROM usuarios WHERE usuario_email = ? AND usuario_id != ?";
                $stmtVerificaEmail = $pdo->prepare($sqlVerificaEmail);
                $stmtVerificaEmail->execute([$email, $id]);
                
                if ($stmtVerificaEmail->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                    header("Location: /iga/app/controllers/usuario_controller.php?acao=editar&id=$id");
                    exit;
                }

                // Verificar se login já existe (excluindo o próprio usuário)
                $sqlVerificaLogin = "SELECT COUNT(*) FROM usuarios WHERE usuario_login = ? AND usuario_id != ?";
                $stmtVerificaLogin = $pdo->prepare($sqlVerificaLogin);
                $stmtVerificaLogin->execute([$login, $id]);
                
                if ($stmtVerificaLogin->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este login já está em uso.";
                    header("Location: /iga/app/controllers/usuario_controller.php?acao=editar&id=$id");
                    exit;
                }

                // Construir query de atualização
                $sqlUpdate = "UPDATE usuarios SET 
                             usuario_nome = ?, 
                             usuario_email = ?, 
                             usuario_login = ?, 
                             papel_id = ?";
                
                $params = [$nome, $email, $login, $papel_id];

                // Se senha foi informada, adicionar ao update
                if (!empty($senha)) {
                    $sqlUpdate .= ", usuario_senha = ?";
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $params[] = $senhaHash;
                }

                $sqlUpdate .= " WHERE usuario_id = ?";
                $params[] = $id;

                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute($params);

                if ($stmtUpdate->rowCount() > 0) {
                    $_SESSION['sucesso_salvar'] = "Usuário atualizado com sucesso!";
                } else {
                    $_SESSION['erro_salvar'] = "Nenhuma alteração foi realizada.";
                }
                
                header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar usuário: " . $e->getMessage();
                header("Location: /iga/app/controllers/usuario_controller.php?acao=editar&id=$id");
                exit;
            }

        } else {
            // É um NOVO CADASTRO
            // Validar campos obrigatórios
            $camposObrigatorios = ['usuario_nome', 'usuario_email', 'usuario_login', 'usuario_senha', 'confirmar_senha', 'papel_id'];
            foreach ($camposObrigatorios as $campo) {
                if (empty($_POST[$campo])) {
                    $_SESSION['erro_salvar'] = "Por favor, preencha todos os campos obrigatórios.";
                    header('Location: /iga/app/views/usuarios/create.php');
                    exit;
                }
            }

            // Verificar se as senhas coincidem
            if ($_POST['usuario_senha'] !== $_POST['confirmar_senha']) {
                $_SESSION['erro_salvar'] = "As senhas não coincidem.";
                header('Location: /iga/app/views/usuarios/create.php');
                exit;
            }

            // Sanitizar e validar dados
            $nome = trim($_POST['usuario_nome']);
            $email = filter_var(trim($_POST['usuario_email']), FILTER_VALIDATE_EMAIL);
            $login = trim($_POST['usuario_login']);
            $senha = $_POST['usuario_senha'];
            $papel_id = (int)$_POST['papel_id'];

            if (!$email) {
                $_SESSION['erro_salvar'] = "Por favor, informe um e-mail válido.";
                header('Location: /iga/app/views/usuarios/create.php');
                exit;
            }

            try {
                // Verificar se e-mail já existe
                $sqlVerificaEmail = "SELECT COUNT(*) FROM usuarios WHERE usuario_email = ?";
                $stmtVerificaEmail = $pdo->prepare($sqlVerificaEmail);
                $stmtVerificaEmail->execute([$email]);
                
                if ($stmtVerificaEmail->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este e-mail já está cadastrado.";
                    header('Location: /iga/app/views/usuarios/create.php');
                    exit;
                }

                // Verificar se login já existe
                $sqlVerificaLogin = "SELECT COUNT(*) FROM usuarios WHERE usuario_login = ?";
                $stmtVerificaLogin = $pdo->prepare($sqlVerificaLogin);
                $stmtVerificaLogin->execute([$login]);
                
                if ($stmtVerificaLogin->fetchColumn() > 0) {
                    $_SESSION['erro_salvar'] = "Este login já está em uso.";
                    header('Location: /iga/app/views/usuarios/create.php');
                    exit;
                }

                // Hash da senha
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                // Inserir novo usuário
                $sqlInserir = "INSERT INTO usuarios 
                              (usuario_nome, usuario_email, usuario_login, usuario_senha, papel_id, usuario_criadoem) 
                              VALUES (?, ?, ?, ?, ?, NOW())";
                
                $stmtInserir = $pdo->prepare($sqlInserir);
                $stmtInserir->execute([$nome, $email, $login, $senhaHash, $papel_id]);

                // Verificar se inseriu corretamente
                if ($stmtInserir->rowCount() > 0) {
                    $_SESSION['sucesso_salvar'] = "Usuário cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar usuário. Nenhum registro foi inserido.";
                    header('Location: /iga/app/views/usuarios/create.php');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                header('Location: /iga/app/views/usuarios/create.php');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['usuario_id'] ?? null;
        
        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do usuário não fornecido.";
            header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
            exit;
        }

        // Não permitir que o usuário exclua a si mesmo
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['erro_excluir'] = "Você não pode excluir seu próprio usuário.";
            header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
            exit;
        }

        try {
            // Usar transação para garantir integridade
            $pdo->beginTransaction();
            
            // Primeiro verificar if o usuário existe
            $sqlVerifica = "SELECT usuario_id FROM usuarios WHERE usuario_id = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$id]);
            
            if ($stmtVerifica->rowCount() === 0) {
                $_SESSION['erro_excluir'] = "Usuário não encontrado.";
                $pdo->rollBack();
                header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
                exit;
            }
            
            // Excluir o usuário
            $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $pdo->commit();
            
            $_SESSION['sucesso_excluir'] = "Usuário excluído com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['erro_excluir'] = "Erro ao excluir usuário: " . $e->getMessage();
        }
        
        header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
        exit;
        
    default:
        header('Location: /iga/app/controllers/usuario_controller.php?acao=listar');
        break;
}
?>