<?php
# ========================================
# Controlador de Papéis de Usuário (CRUD Completo)
# Local: /app/controllers/usuariopapel_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar papéis de usuário)
$acao = $_REQUEST['acao'] ?? 'listar';
$acoesRestritas = ['listar', 'editar', 'salvar', 'excluir', 'criar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/usuariopapel.php';

$usuarioPapelModel = new usuariopapel($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'listar':
        try {
            $papeis = $usuarioPapelModel->listarTodos();

            // Verificar mensagens de sessão
            $sucesso = $_SESSION['sucesso_salvar'] ?? $_SESSION['sucesso_excluir'] ?? '';
            $erro = $_SESSION['erro_salvar'] ?? $_SESSION['erro_excluir'] ?? '';

            // Limpar mensagens da sessão
            unset($_SESSION['sucesso_salvar'], $_SESSION['sucesso_excluir']);
            unset($_SESSION['erro_salvar'], $_SESSION['erro_excluir']);

            // Incluir a view de listagem
            // === CORREÇÃO: Caminho explícito e robusto para a pasta 'usuariospapeis' ===
            $caminhoViewIndex = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'usuariospapeis' . DIRECTORY_SEPARATOR . 'index.php';
            if (!file_exists($caminhoViewIndex)) {
                error_log("ERRO CRITICO: Arquivo da view index.php NAO ENCONTRADO em: " . $caminhoViewIndex);
                die("Erro interno: Arquivo de visualização 'index.php' não encontrado para 'usuariospapeis'. Verifique a estrutura de pastas.");
            }
            require_once $caminhoViewIndex;
            // ==============================================================================

        } catch (PDOException $e) {
            error_log("Erro ao listar papéis de usuário: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao carregar a lista de papéis de usuário. Por favor, tente novamente.";
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
            require_once __DIR__ . '/../views/usuariospapeis/create.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar formulário: " . $e->getMessage();
            header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
            exit;
        }
        break;

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['erro_salvar'] = "ID do papel não fornecido.";
            header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
            exit;
        }

        try {
            $papel = $usuarioPapelModel->buscarPorId($id);

            if (!$papel) {
                $_SESSION['erro_salvar'] = "Papel de usuário não encontrado.";
                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
                exit;
            }

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            require_once __DIR__ . '/../views/usuariospapeis/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar dados do papel: " . $e->getMessage();
            header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            $redirect_id = $_POST['usuariopapel_id'] ?? null;
            if ($redirect_id) {
                 header("Location: /iga/app/controllers/usuariopapel_controller.php?acao=editar&id=" . urlencode($redirect_id));
            } else {
                 header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=criar');
            }
            exit;
        }

        $id = $_POST['usuariopapel_id'] ?? null;

        if ($id) {
            // EDIÇÃO
            $dados = [
                'nome' => trim($_POST['usuariopapel_nome'] ?? ''),
                'descricao' => trim($_POST['usuariopapel_descricao'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do papel.";
                header("Location: /iga/app/controllers/usuariopapel_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            // Verificar se nome já existe (excluindo o próprio papel)
            // Exceto para o papel 'Administrador' (ID 1), cujo nome não deve ser alterado
            if ($id != 1 && $usuarioPapelModel->nomeExiste($dados['nome'], $id)) {
                $_SESSION['erro_salvar'] = "Este nome de papel já está cadastrado.";
                header("Location: /iga/app/controllers/usuariopapel_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

            try {
                if ($usuarioPapelModel->atualizar($id, $dados)) {
                    $_SESSION['sucesso_salvar'] = "Papel de usuário atualizado com sucesso!";
                } else {
                    $_SESSION['sucesso_salvar'] = "Nenhuma alteração foi realizada.";
                }

                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
                exit;

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro ao atualizar papel de usuário: " . $e->getMessage();
                header("Location: /iga/app/controllers/usuariopapel_controller.php?acao=editar&id=" . urlencode($id));
                exit;
            }

        } else {
            // NOVO PAPEL
            $dados = [
                'nome' => trim($_POST['usuariopapel_nome'] ?? ''),
                'descricao' => trim($_POST['usuariopapel_descricao'] ?? '')
            ];

            if (empty($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Por favor, informe o nome do papel.";
                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=criar');
                exit;
            }

            // Verificar se nome já existe
            if ($usuarioPapelModel->nomeExiste($dados['nome'])) {
                $_SESSION['erro_salvar'] = "Este nome de papel já está cadastrado.";
                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=criar');
                exit;
            }

            try {
                if ($usuarioPapelModel->criar($dados)) {
                    $_SESSION['sucesso_salvar'] = "Papel de usuário cadastrado com sucesso!";
                    header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
                    exit;
                } else {
                    $_SESSION['erro_salvar'] = "Erro ao cadastrar papel de usuário. Nenhum registro foi inserido.";
                    header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=criar');
                    exit;
                }

            } catch (PDOException $e) {
                $_SESSION['erro_salvar'] = "Erro no banco de dados: " . $e->getMessage();
                error_log("Erro ao inserir papel de usuário: " . $e->getMessage());
                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=criar');
                exit;
            }
        }
        break;

    case 'excluir':
        $id = $_POST['usuariopapel_id'] ?? null;

        if (!$id) {
            $_SESSION['erro_excluir'] = "ID do papel não fornecido.";
            header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
            exit;
        }

        // Impede a exclusão do papel 'Administrador' (ID 1)
        if ($id == 1) {
             $_SESSION['erro_excluir'] = "O papel 'Administrador' não pode ser excluído.";
             header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
             exit;
        }

        try {
            // Verificar se papel existe
            $papel = $usuarioPapelModel->buscarPorId($id);
            if (!$papel) {
                $_SESSION['erro_excluir'] = "Papel de usuário não encontrado.";
                header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
                exit;
            }

            // Tenta excluir
            if ($usuarioPapelModel->excluir($id)) {
                $_SESSION['sucesso_excluir'] = "Papel de usuário excluído com sucesso!";
            } else {
                 // Se a exclusão falhar (ex: restrição de FK ou tentativa de excluir "Administrador")
                 $_SESSION['erro_excluir'] = "Erro ao excluir papel de usuário. Pode estar em uso ou não ser permitido.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro_excluir'] = "Erro ao excluir papel de usuário: " . $e->getMessage();
            error_log("Erro ao excluir papel de usuário ID $id: " . $e->getMessage());
        }

        header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
        exit;

    default:
        header('Location: /iga/app/controllers/usuariopapel_controller.php?acao=listar');
        break;
}
?>