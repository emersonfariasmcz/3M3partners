<?php
$senha_plana = 'admin123';
$hash_novo = password_hash($senha_plana, PASSWORD_DEFAULT);

echo "<h2>SENHA: admin123</h2>";
echo "<h3>HASH NOVO:</h3>";
echo "<pre style='background:#f0f0f0; padding:10px; font-family:monospace;'>" . $hash_novo . "</pre>";
echo "<hr>";
echo "<h3>Teste password_verify():</h3>";
echo password_verify('admin123', $hash_novo) ? "<span style='color:green'>VERDADEIRO ✅</span>" : "<span style='color:red'>FALSO ❌</span>";
?>