<?php
# ========================================
# Controlador de Configurações Gerais
# Local: /app/controllers/configuracoesgerais_controller.php
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

// Verificar permissões (apenas administradores podem gerenciar configurações gerais)
$acao = $_REQUEST['acao'] ?? 'editar';
$acoesRestritas = ['editar', 'salvar'];

if (in_array($acao, $acoesRestritas) && $_SESSION['usuario_papel'] !== 'Administrador') {
    $_SESSION['erro_acesso'] = "Acesso restrito a administradores do sistema.";
    header('Location: /iga/app/views/acesso_negado.php');
    exit;
}

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../models/configuracoesgerais.php';

$configModel = new configuracoesgerais($pdo);

// Determinar a ação com base no parâmetro 'acao'
switch ($acao) {
    case 'editar':
        try {
            // Buscar as configurações atuais
            $configuracoes = $configModel->buscarConfiguracoes();

            if (!$configuracoes) {
                // Se não existir, cria um array vazio para evitar erros na view
                $configuracoes = [
                    'config_id' => null,
                    'config_nome_empresa' => '',
                    'config_cnpj' => '',
                    'config_endereco' => '',
                    'config_cidade' => '',
                    'config_estado_id' => '',
                    'config_cep' => '',
                    'config_telefone' => '',
                    'config_email' => '',
                    'config_site' => '',
                    'config_logo_path' => 'assets/img/img_logo.png',
                    'config_estado_nome' => '',
                    'config_estado_uf' => ''
                ];
            }

            // === ADIÇÃO: Buscar estados para o dropdown ===
            $estados = $configModel->listarEstados();
            // ===============================================

            // Buscar mensagens de erro/sucesso
            $erro = $_SESSION['erro_salvar'] ?? '';
            $sucesso = $_SESSION['sucesso_salvar'] ?? '';
            unset($_SESSION['erro_salvar'], $_SESSION['sucesso_salvar']);

            // Incluir a view de edição
            require_once __DIR__ . '/../views/configuracoesgerais/edit.php';

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao carregar configurações: " . $e->getMessage();
            header('Location: /iga/app/views/dashboard.php');
            exit;
        }
        break;

    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_salvar'] = "Método de requisição inválido.";
            header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
            exit;
        }

        // Dados do formulário
        $dados = [
            'nome_empresa' => trim($_POST['config_nome_empresa'] ?? ''),
            'cnpj' => trim($_POST['config_cnpj'] ?? ''),
            'endereco' => trim($_POST['config_endereco'] ?? ''),
            'cidade' => trim($_POST['config_cidade'] ?? ''),
            'estado_id' => trim($_POST['config_estado_id'] ?? ''), // Corrigido para usar o ID
            'cep' => trim($_POST['config_cep'] ?? ''),
            'telefone' => trim($_POST['config_telefone'] ?? ''),
            'email' => trim($_POST['config_email'] ?? ''),
            'site' => trim($_POST['config_site'] ?? ''),
            'logo_path' => trim($_POST['config_logo_path'] ?? 'assets/img/img_logo.png')
        ];

        // Validação básica (opcional, pode ser expandida)
        if (empty($dados['nome_empresa'])) {
            $_SESSION['erro_salvar'] = "Por favor, informe o nome da empresa.";
            header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
            exit;
        }

        try {
            if ($configModel->atualizar($dados)) {
                $_SESSION['sucesso_salvar'] = "Configurações gerais atualizadas com sucesso!";
            } else {
                $_SESSION['erro_salvar'] = "Erro ao atualizar configurações gerais.";
            }

            header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
            exit;

        } catch (PDOException $e) {
            $_SESSION['erro_salvar'] = "Erro ao salvar configurações: " . $e->getMessage();
            header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
            exit;
        }
        break;

    default:
        // Redireciona para a edição por padrão
        header('Location: /iga/app/controllers/configuracoesgerais_controller.php?acao=editar');
        exit;
}
?>