<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = $_GET['page'] ?? 'autenticacao'; // default para login
$acao = $_GET['acao'] ?? 'index';

$baseControllers = __DIR__ . "/../app/controllers/";

$candidates = [
    $baseControllers . $page . "_controller.php",
    $baseControllers . $page . ".php",
    $baseControllers . rtrim($page, 's') . "_controller.php",
    $baseControllers . rtrim($page, 's') . ".php",
];

$controllerFile = null;
foreach ($candidates as $c) {
    if (file_exists($c)) {
        $controllerFile = $c;
        break;
    }
}

if ($controllerFile) {
    require_once $controllerFile;
} else {
    echo "Página ou controlador não encontrado.<br>";
    foreach ($candidates as $c) {
        echo "Procurado: " . htmlspecialchars($c) . "<br>";
    }
}
