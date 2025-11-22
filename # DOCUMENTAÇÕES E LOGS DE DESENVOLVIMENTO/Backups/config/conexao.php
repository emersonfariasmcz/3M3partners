<?php
# ========================================
# Conexão com o banco de dados MySQL (PDO)
# Local: /config/conexao.php
# ========================================

# Credenciais do banco de dados
$host = 'localhost';
$dbname = 'iga_bd';
$username = 'root';
$password = '123456';

try {
    # Instanciando objeto PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,            // Exibe exceções
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna como array associativo
            PDO::ATTR_EMULATE_PREPARES => false,                    // Desativa prepare emulado (mais seguro)
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"     // Charset padrão
        ]
    );
} catch (PDOException $e) {
    # Erro na conexão com o banco
   die("Erro de conexão com o banco de dados: " . $e->getMessage()); 
}



?>
