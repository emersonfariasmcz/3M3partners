<?php
# =======================================
# Encerramento de sessão (logout seguro)
# Local: /app/controllers/logout.php
# =======================================

session_start();         // Inicia sessão (caso ainda não esteja)
session_unset();         // Remove todas as variáveis da sessão
session_destroy();       // Destroi a sessão completamente

# Redireciona para a tela de login
header('Location: ../../app/views/login.php');
exit;
