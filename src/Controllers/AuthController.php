<?php
namespace Controllers;

use Core\Session;
use Models\Usuario;

/**
 * Controlador de Autenticação
 * Gerencia o login, logout e verificação de autenticação
 */
class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Processa o login do usuário
     */
    public function login(string $email, string $senha): bool {
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if (!$usuario) {
            return false;
        }
        
        if (!$this->usuarioModel->verifyPassword($senha, $usuario['senha_hash'])) {
            return false;
        }
        
        if (!$usuario['ativo']) {
            return false;
        }
        
        // Registra o usuário na sessão
        Session::setUsuarioLogado($usuario);
        
        return true;
    }    /**
     * Realiza o logout do usuário
     */
    public function logout(): void {
        Session::destroy();
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
    
    /**
     * Verifica se o usuário está autenticado
     */
    public static function requireLogin(): void {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
    }
    
    /**
     * Verifica se o usuário tem permissão para acessar uma área
     */
    public static function requirePermission(int $tipoUsuario): void {
        self::requireLogin();
        
        if (!Session::hasUserRole($tipoUsuario)) {
            header('Location: ' . BASE_URL . 'error.php?code=403');
            exit;
        }
    }
    
    /**
     * Redireciona o usuário para sua área apropriada após o login
     */
    public static function redirectToDashboard(): void {
        switch (Session::getTipoUsuario()) {
            case USUARIO_ADMIN:
                header('Location: ' . BASE_URL . '?page=admin_dashboard');
                break;
            case USUARIO_TECNICO:
                header('Location: ' . BASE_URL . '?page=tecnico_dashboard');
                break;
            case USUARIO_SOLICITANTE:
                header('Location: ' . BASE_URL . '?page=solicitante_dashboard');
                break;
            default:
                header('Location: ' . BASE_URL);
        }
        exit;
    }
}
