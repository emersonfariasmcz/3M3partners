<?php
# ========================================
# Envio do link de recuperação por e-mail
# Local: /app/controllers/recuperar_senha_handler.php
# ========================================

session_start();
require_once '../../config/conexao.php';

// PHPMailer - Carregar o autoloader do composer  
require_once '../../vendor/autoload.php'; 

// PHPMailer - Importar as classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


# Verifica o envio por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $_SESSION['erro_login'] = 'Informe um e-mail válido.';
        header('Location: ../../app/views/recuperar_senha.php');
        exit;
    }

    # Verifica se existe o e-mail no sistema
    $sql = "SELECT * FROM usuarios WHERE usuario_email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch();

    if ($usuario) {
        # Gera token e salva na base
        $token = bin2hex(random_bytes(32));
        $expira_em = date('Y-m-d H:i:s', time() + 1800); // 30 minutos

        $insert = $pdo->prepare("INSERT INTO tokens_recuperacao (usuario_id, token, expiracao) VALUES (?, ?, ?)");
        $insert->execute([$usuario['usuario_id'], $token, $expira_em]);

        # Link de recuperação
        $link = "http://localhost/iga/app/views/redefinir_senha.php?token=$token";

        # Enviar e-mail com PHPMailer
        // Instância da classe
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor
            $mail->isSMTP(); //Devine o uso de SMTP no envio
            $mail->Host       = 'smtp.gmail.com'; // Informações específicadas pelo Google
            $mail->SMTPAuth   = true; //Habilita a autenticação SMTP
            $mail->Username   = 'seuemail@gmail.com'; // Altere aqui
            $mail->Password   = 'sua-senha-app';       // Senha de app do Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587; // Informações específicadas pelo Google 
            // Define o remetente
            $mail->setFrom('seuemail@gmail.com', 'Sistema de Estoque');
            // Define o destinatário            
            $mail->addAddress($email);
            // Conteúdo da mensagem 
            $mail->isHTML(true); // Seta o formato do e-mail para aceitar conteúdo HTML
            $mail->Subject = 'Recuperação de Senha - Sistema de Estoque'; // Asunto do e-mail
            $mail->Body    = "<p>Olá <strong>{$usuario['usuario_nome']}</strong>,</p>
                              <p>Você solicitou a recuperação de senha. Clique no link abaixo para redefinir:</p>
                              <p><a href='$link'>$link</a></p>
                              <p>Este link expirará em 30 minutos.</p>";

            $mail->send(); // Enviar
            $_SESSION['sucesso'] = 'Link enviado para o seu e-mail!';
        } catch (Exception $e) {
            $_SESSION['erro_login'] = 'Erro ao enviar e-mail: ' . $mail->ErrorInfo;
        }
    } else {
        # Mesmo retorno, independente de existir ou não o e-mail (segurança)
        $_SESSION['sucesso'] = 'Se o e-mail existir, você receberá um link.';
    }

    header('Location: ../../app/views/recuperar_senha.php');
    exit;
}
