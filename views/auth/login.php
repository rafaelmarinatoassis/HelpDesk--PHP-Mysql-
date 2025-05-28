<?php
// Como este arquivo é incluído pelo index.php na raiz, usamos caminhos a partir da raiz
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Core/Session.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Models/Model.php';
require_once __DIR__ . '/../../src/Models/Usuario.php';
require_once __DIR__ . '/../../src/Controllers/AuthController.php';

use Core\Session;
use Controllers\AuthController;

Session::init();

// Se já estiver logado, redireciona para o dashboard
if (Session::isLoggedIn()) {
    AuthController::redirectToDashboard();
}

$error = '';

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    
    if ($auth->login($_POST['email'], $_POST['senha'])) {
        AuthController::redirectToDashboard();
    } else {
        $error = 'Email ou senha inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-body {
            padding: 2rem;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .form-floating label {
            color: #6c757d;
        }
        .btn-primary {
            padding: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <h1 class="h3"><?= APP_NAME ?></h1>
                    <p class="text-muted">Entre com suas credenciais</p>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="form-floating mb-3">
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="nome@exemplo.com"
                                       required 
                                       autofocus>
                                <label for="email">Email</label>
                                <div class="invalid-feedback">
                                    Digite seu email
                                </div>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input type="password" 
                                       class="form-control" 
                                       id="senha" 
                                       name="senha" 
                                       placeholder="Senha"
                                       required>
                                <label for="senha">Senha</label>
                                <div class="invalid-feedback">
                                    Digite sua senha
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </button>
                              <div class="text-center">
                                <p class="mb-0">
                                    Não tem uma conta? 
                                    <a href="?page=cadastro" class="text-decoration-none">Cadastre-se</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos os direitos reservados.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário
        (function () {
            'use strict'
            
            var forms = document.querySelectorAll('.needs-validation')
            
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
