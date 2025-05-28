<?php
require_once 'config/config.php';
require_once 'src/Core/Database.php';

use Core\Database;

// Gera um novo hash para a senha 'admin123'
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT, ['cost' => 12]);

// Atualiza no banco de dados
$db = Database::getInstance();
$sql = "UPDATE usuarios SET senha_hash = :hash WHERE email = 'admin@escola.com'";
$db->execute($sql, ['hash' => $hash]);

echo "Senha do administrador atualizada com sucesso!\n";
echo "Email: admin@escola.com\n";
echo "Senha: admin123\n";
