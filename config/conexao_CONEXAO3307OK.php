<?php
# ========================================
# Conexão com o banco de dados MySQL (PDO)
# Local: /config/conexao.php
# Essa arquivo de conexão é temporário para solucuionar o problema xampp que estava utilziando a porta 3306 aqui em casa e tive que mudar para a porta 3307  
# ========================================

# Credenciais do banco de dados
$host     = '127.0.0.1';     // TCP FORÇADO
$port     = '3307';          // PORTA XAMPP
$dbname   = 'bd_3m3erp';
$username = 'root';
$password = '123456';

// DEBUG TEMPORÁRIO - REMOVA DEPOIS
echo "<pre>DEBUG - Configurações:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "DSN: mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4\n";
echo "</pre>";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);

    echo "<h1 style='color: green;'>CONECTADO COM SUCESSO!</h1>";
    echo "Banco: " . $pdo->query("SELECT DATABASE()")->fetchColumn();

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>