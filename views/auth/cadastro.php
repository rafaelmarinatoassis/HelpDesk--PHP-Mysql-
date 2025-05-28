<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Core/Session.php';
require_once __DIR__ . '/../../src/Core/Database.php';
require_once __DIR__ . '/../../src/Models/Model.php';
require_once __DIR__ . '/../../src/Models/Usuario.php';
require_once __DIR__ . '/../../src/Models/Setor.php';
require_once __DIR__ . '/../../src/Controllers/AuthController.php';

use Core\Session;
use Models\Usuario;
use Models\Setor;

Session::init();

// Se já estiver logado, redireciona
if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL);
    exit;
}

$error = '';
$success = '';

// Instancia os modelos necessários
$usuarioModel = new Usuario();
$setorModel = new Setor();

// Obtém a lista de setores para o formulário
$setores = $setorModel->findAll('nome');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';
    $setorId = (int)($_POST['setor_id'] ?? 0);
    
    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha)) {
        $error = 'Todos os campos são obrigatórios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido';
    } elseif (strlen($senha) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres';
    } elseif ($senha !== $confirmaSenha) {
        $error = 'As senhas não conferem';
    } elseif ($usuarioModel->findByEmail($email)) {
        $error = 'Este email já está cadastrado';
    } elseif (!$setorId) {
        $error = 'Selecione um setor';
    } else {
        // Tenta criar o usuário
        try {
            $id = $usuarioModel->create([
                'nome_completo' => $nome,
                'email' => $email,
                'senha' => $senha, // será feito hash na classe Usuario
                'tipo_usuario_id' => USUARIO_SOLICITANTE, // usuário padrão é solicitante
                'setor_id' => $setorId,
                'ativo' => true
            ]);
            
            if ($id) {
                $success = 'Cadastro realizado com sucesso! Você já pode fazer login.';
            } else {
                $error = 'Erro ao criar usuário';
            }
        } catch (Exception $e) {
            $error = DEBUG_MODE ? $e->getMessage() : 'Erro ao processar cadastro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6">
                <div class="text-center mb-4">
                    <h1 class="h3"><?= APP_NAME ?></h1>
                    <p class="text-muted">Crie sua conta para abrir chamados</p>
                </div>
                
                <div class="card">
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill"></i>
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nome" 
                                       name="nome"
                                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Digite seu nome completo
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Digite um email válido
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="setor_id" class="form-label">Setor</label>                                <select class="form-select" id="setor_id" name="setor_id" required>
                                    <option value="">Selecione seu setor...</option>
                                    <?php foreach ($setores as $setor): ?>
                                        <option value="<?= $setor['id'] ?>" 
                                            <?= (isset($_POST['setor_id']) && $_POST['setor_id'] == $setor['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($setor['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Selecione seu setor
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="senha" 
                                           name="senha"
                                           minlength="6"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('senha')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    A senha deve ter pelo menos 6 caracteres
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirma_senha" class="form-label">Confirme a Senha</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirma_senha" 
                                           name="confirma_senha"
                                           minlength="6"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('confirma_senha')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Criar Conta
                                </button>
                                <a href="<?= BASE_URL ?>" class="btn btn-light">
                                    <i class="bi bi-arrow-left"></i> Voltar para Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">
                        Já tem uma conta? <a href="<?= BASE_URL ?>">Faça login</a>
                    </p>
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

        // Função para mostrar/ocultar senha
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            
            const icon = event.currentTarget.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        }

        // Verifica se as senhas conferem
        document.getElementById('confirma_senha').addEventListener('input', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmaSenha = e.target.value;
            
            if (senha !== confirmaSenha) {
                e.target.setCustomValidity('As senhas não conferem');
            } else {
                e.target.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
