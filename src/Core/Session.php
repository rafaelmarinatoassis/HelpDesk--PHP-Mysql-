<?php
namespace Core;

/**
 * Classe Session
 * Gerencia as sessões do sistema
 */
class Session {
    /**
     * Inicia a sessão com configurações seguras
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurações de segurança da sessão
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
            
            // Regenera o ID da sessão periodicamente para prevenir fixação de sessão
            if (!isset($_SESSION['last_regeneration']) || 
                time() - $_SESSION['last_regeneration'] >= 1800) { // 30 minutos
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Define um valor na sessão
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtém um valor da sessão
     * @param string $key
     * @param mixed $default Valor padrão caso a chave não exista
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Remove um valor da sessão
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    /**
     * Verifica se uma chave existe na sessão
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Destrói a sessão atual
     */
    public static function destroy(): void {
        session_destroy();
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Define o usuário logado na sessão
     * @param array $usuario Dados do usuário
     */
    public static function setUsuarioLogado(array $usuario): void {
        self::set('usuario_id', $usuario['id']);
        self::set('usuario_nome', $usuario['nome_completo']);
        self::set('usuario_email', $usuario['email']);
        self::set('usuario_tipo', $usuario['tipo_usuario_id']);
        self::set('usuario_setor', $usuario['setor_id']);
    }
    
    /**
     * Verifica se há um usuário logado
     */
    public static function isLoggedIn(): bool {
        return self::has('usuario_id');
    }
    
    /**
     * Obtém o ID do usuário logado
     */
    public static function getUsuarioId(): ?int {
        return self::get('usuario_id');
    }
    
    /**
     * Obtém o tipo do usuário logado
     */
    public static function getTipoUsuario(): ?int {
        return self::get('usuario_tipo');
    }
    
    /**
     * Verifica se o usuário logado tem determinado tipo
     */
    public static function hasUserRole(int $tipoUsuario): bool {
        return self::getTipoUsuario() === $tipoUsuario;
    }
    
    /**
     * Verifica se o usuário é administrador
     */
    public static function isAdmin(): bool {
        return self::hasUserRole(USUARIO_ADMIN);
    }
    
    /**
     * Verifica se o usuário é técnico
     */
    public static function isTecnico(): bool {
        return self::hasUserRole(USUARIO_TECNICO);
    }
    
    /**
     * Verifica se o usuário é solicitante
     */
    public static function isSolicitante(): bool {
        return self::hasUserRole(USUARIO_SOLICITANTE);
    }
}
