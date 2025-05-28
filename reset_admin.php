<?php
// Arquivo temporário para redefinir a senha do administrador
require_once 'config/config.php';
require_once 'src/Core/Database.php';

use Core\Database;

try {
    // Gera um novo hash para a senha 'admin123'
    $senha = 'admin123';
    $hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

    // Atualiza no banco de dados
    $db = Database::getInstance();
    $sql = "UPDATE usuarios SET senha_hash = :hash WHERE email = 'admin@escola.com'";
    $result = $db->execute($sql, ['hash' => $hash]);
    
    echo "<p style='color: green;'>✓ Senha do administrador atualizada com sucesso!</p>";
    echo "<p>Email: admin@escola.com<br>Senha: admin123</p>";
    echo "<p><a href='/'>Voltar para o login</a></p>";
    
    // Auto-destruição do arquivo após 5 segundos
    header("refresh:5;url=/");
    echo "<p>Esta página será excluída automaticamente em 5 segundos...</p>";
    
    // Programa a exclusão do arquivo
    register_shutdown_function(function() {
        @unlink(__FILE__);
    });
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro ao atualizar a senha: " . $e->getMessage() . "</p>";
}
