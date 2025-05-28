<?php
// Bootstrap do aplicativo
require_once 'config/config.php';
require_once 'src/Core/Database.php';
require_once 'src/Core/Session.php';

// Inicializa a sessão
use Core\Session;

Session::init();

// Carrega os modelos necessários
require_once 'src/Models/Model.php';
require_once 'src/Models/Usuario.php';
require_once 'src/Models/Chamado.php';
require_once 'src/Models/Categoria.php';
require_once 'src/Models/Setor.php';
require_once 'src/Models/Status.php';

// Carrega os controladores
require_once 'src/Controllers/AuthController.php';

// Carrega o ChamadoController
require_once 'src/Controllers/ChamadoController.php';

// Verifica se é uma requisição de ação
$action = $_GET['action'] ?? '';

// Ações de autenticação
if ($action === 'login' || $action === 'logout') {
    $authController = new Controllers\AuthController();
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        if ($authController->login($email, $senha)) {
            $authController->redirectToDashboard();
        } else {
            header('Location: ' . BASE_URL . 'login.php?error=1');
        }    } else {
        $authController->logout(); // This will handle the redirect
    }
    exit;
}

// Ações de chamados
if ($action === 'chamado_criar') {
    if (!Session::isSolicitante()) {
        header('Location: ' . BASE_URL . 'error.php?code=403');
        exit;
    }

    $chamadoController = new Controllers\ChamadoController();
    if ($chamadoController->criarChamado($_POST)) {
        header('Location: ' . BASE_URL . '?page=solicitante_dashboard&success=chamado_criado');
    } else {
        header('Location: ' . BASE_URL . '?page=solicitante_dashboard&error=chamado_erro');
    }
    exit;
}

// Roteamento básico
$page = $_GET['page'] ?? 'dashboard';

// Se for página de cadastro, mostra a página
if ($page === 'cadastro') {
    require_once 'views/auth/cadastro.php';
    exit;
}

// Se não estiver logado, redireciona para login
if (!Session::isLoggedIn()) {
    require_once 'views/auth/login.php';
    exit;
}

// Define o layout base de acordo com o tipo de usuário
if (Session::isAdmin()) {
    $sidebarFile = 'views/layouts/sidebar_admin.php';
    if ($page === 'dashboard') $page = 'admin_dashboard';
} elseif (Session::isTecnico()) {
    $sidebarFile = 'views/layouts/sidebar_tecnico.php';
    if ($page === 'dashboard') $page = 'tecnico_dashboard';
} else {
    $sidebarFile = 'views/layouts/sidebar_solicitante.php';
    if ($page === 'dashboard') $page = 'solicitante_dashboard';
}

// Carrega o cabeçalho
require_once 'views/layouts/header.php';

// Carrega a sidebar específica do usuário
require_once $sidebarFile;

// Carrega a view solicitada
$viewFile = match ($page) {
    'admin_dashboard' => 'views/admin/dashboard.php',
    'tecnico_dashboard' => 'views/tecnico/dashboard.php',
    'solicitante_dashboard' => 'views/solicitante/dashboard.php',
    'solicitante_chamados' => 'views/solicitante/chamados.php',
    'chamado_form' => 'views/chamados/form.php',
    'chamado_view' => 'views/chamados/view.php',
    'usuario_perfil' => 'views/usuario/perfil.php',
    default => 'error.php'
};

require_once $viewFile;

// Carrega o rodapé
require_once 'views/layouts/footer.php';
