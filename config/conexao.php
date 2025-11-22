<?php
# ========================================
# Conexão com o banco de dados MySQL (PDO)
# Local: /config/conexao.php
# ========================================

$host     = '127.0.0.1';
$port     = '3307';
$dbname   = 'bd_3m3erp';
$username = 'root';
$password = '123456';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>