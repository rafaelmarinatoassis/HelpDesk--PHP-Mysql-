<?php
/**
 * Arquivo de configuração principal
 * Contém constantes e configurações globais do sistema
 */

// Configurações de ambiente
define('APP_NAME', 'Help Desk - Escola Técnica');
define('BASE_URL', 'http://localhost/');

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'helpdesk');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de Sessão
define('SESSION_NAME', 'helpdesk_session');
define('SESSION_LIFETIME', 3600); // 1 hora em segundos

// Configurações de Debug
define('DEBUG_MODE', true);

// Diretórios da Aplicação
define('APP_ROOT', dirname(__DIR__));
define('VIEW_PATH', APP_ROOT . '/views');
define('CONTROLLER_PATH', APP_ROOT . '/src/Controllers');
define('MODEL_PATH', APP_ROOT . '/src/Models');

// Configurações de Upload e Arquivos
define('UPLOAD_PATH', APP_ROOT . '/public/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB em bytes
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);

// Configurações de Segurança
define('HASH_COST', 12); // Custo do password_hash

// Tipos de Usuário
define('USUARIO_ADMIN', 1);
define('USUARIO_TECNICO', 2);
define('USUARIO_SOLICITANTE', 3);

// Status de Chamados
define('STATUS_ABERTO', 1);
define('STATUS_EM_ATENDIMENTO', 2);
define('STATUS_AGUARDANDO_SOLICITANTE', 3);
define('STATUS_RESOLVIDO', 4);
define('STATUS_FECHADO', 5);
