<?php
require_once '../config/config.php';
require_once '../src/Core/Session.php';
require_once '../src/Core/Database.php';
require_once '../src/Models/Model.php';
require_once '../src/Models/Usuario.php';
require_once '../src/Controllers/AuthController.php';

use Core\Session;
use Controllers\AuthController;

// Inicia a sessão
Session::init();

// Verifica se está logado, exceto para a página de login e cadastro
$publicPages = ['login', 'cadastro'];
$currentPage = $_GET['page'] ?? '';

if (!Session::isLoggedIn() && !in_array($currentPage, $publicPages)) {
    header('Location: ' . BASE_URL . 'views/auth/login.php');
    exit;
}

// Obtém a página requisitada
$page = $_GET['page'] ?? 'dashboard';

// Define o caminho base para as views
$viewsPath = '../views/';

// Função para incluir o layout header
function includeHeader() {
    $tipoUsuario = \Core\Session::getTipoUsuario();
    $sidebarFile = '';
    
    switch ($tipoUsuario) {
        case USUARIO_ADMIN:
            $sidebarFile = 'layouts/sidebar_admin.php';
            break;
        case USUARIO_TECNICO:
            $sidebarFile = 'layouts/sidebar_tecnico.php';
            break;
        case USUARIO_SOLICITANTE:
            $sidebarFile = 'layouts/sidebar_solicitante.php';
            break;
    }
    
    include '../views/layouts/header.php';
    if ($sidebarFile) {
        include "../views/$sidebarFile";
    }
}

// Função para incluir o layout footer
function includeFooter() {
    include '../views/layouts/footer.php';
}

// Mapeia as páginas para seus respectivos arquivos e permissões
$routes = [
    // Páginas do Administrador
    'admin_dashboard' => [
        'file' => 'admin/dashboard.php',
        'permission' => USUARIO_ADMIN
    ],
    'admin_usuarios' => [
        'file' => 'admin/usuarios_list.php',
        'permission' => USUARIO_ADMIN
    ],
    
    // Páginas do Técnico
    'tecnico_dashboard' => [
        'file' => 'tecnico/dashboard.php',
        'permission' => USUARIO_TECNICO
    ],
    'tecnico_chamados' => [
        'file' => 'tecnico/chamados_list.php',
        'permission' => USUARIO_TECNICO
    ],
    
    // Páginas do Solicitante
    'solicitante_dashboard' => [
        'file' => 'solicitante/dashboard.php',
        'permission' => USUARIO_SOLICITANTE
    ],
    'chamado_form' => [
        'file' => 'solicitante/chamados_form.php',
        'permission' => USUARIO_SOLICITANTE
    ],
    
    // Páginas comuns
    'chamado_view' => [
        'file' => 'chamados/chamado_view_shared.php',
        'permission' => null // Todos podem ver, a verificação é feita na própria página
    ]
];

// Verifica se a página existe e se o usuário tem permissão
if (!isset($routes[$page])) {
    header('Location: error.php?code=404');
    exit;
}

if ($routes[$page]['permission'] !== null) {
    AuthController::requirePermission($routes[$page]['permission']);
}

// Inclui os layouts e a página
includeHeader();
include $viewsPath . $routes[$page]['file'];
includeFooter();
