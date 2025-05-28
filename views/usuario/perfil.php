<?php
use Core\Session;
use Models\Usuario;
use Models\Setor;

// Instancia os modelos necessários
$usuarioModel = new Usuario();
$setorModel = new Setor();

// Obtém os dados do usuário logado
$usuarioId = Session::getUsuarioId();
$usuario = $usuarioModel->findById($usuarioId);
$setores = $setorModel->findAll('nome');

// Processa o formulário de atualização
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $setorId = (int)($_POST['setor_id'] ?? 0);
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';
    
    // Validações
    if (empty($nome) || empty($email)) {
        $error = 'Nome e email são obrigatórios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido';
    } elseif (!$setorId) {
        $error = 'Selecione um setor';
    } elseif ($email !== $usuario['email'] && $usuarioModel->findByEmail($email)) {
        $error = 'Este email já está em uso';
    } else {
        $data = [
            'nome_completo' => $nome,
            'email' => $email,
            'setor_id' => $setorId
        ];
        
        // Se está tentando alterar a senha
        if (!empty($senhaAtual)) {
            if (empty($novaSenha) || empty($confirmaSenha)) {
                $error = 'Preencha todos os campos de senha';
            } elseif (strlen($novaSenha) < 6) {
                $error = 'A nova senha deve ter pelo menos 6 caracteres';
            } elseif ($novaSenha !== $confirmaSenha) {
                $error = 'As senhas não conferem';
            } elseif (!$usuarioModel->verifyPassword($senhaAtual, $usuario['senha'])) {
                $error = 'Senha atual incorreta';
            } else {
                $data['senha'] = $novaSenha;
            }
        }
        
        if (empty($error)) {
            if ($usuarioModel->update($usuarioId, $data)) {
                $success = 'Perfil atualizado com sucesso!';
                $usuario = $usuarioModel->findById($usuarioId); // Recarrega os dados
            } else {
                $error = 'Erro ao atualizar perfil';
            }
        }
    }
}
?>

<div class="container-fluid px-4">
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Meu Perfil</h1>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-circle me-1"></i>
                    Informações do Perfil
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nome_completo" class="form-label">Nome Completo</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nome_completo" 
                                   name="nome_completo"
                                   value="<?= htmlspecialchars($usuario['nome_completo']) ?>"
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
                                   value="<?= htmlspecialchars($usuario['email']) ?>"
                                   required>
                            <div class="invalid-feedback">
                                Digite um email válido
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="setor_id" class="form-label">Setor</label>
                            <select class="form-select" id="setor_id" name="setor_id" required>
                                <option value="">Selecione seu setor...</option>
                                <?php foreach ($setores as $setor): ?>
                                    <option value="<?= $setor['id'] ?>" 
                                            <?= $usuario['setor_id'] == $setor['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($setor['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Selecione seu setor
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Alterar Senha</h5>
                        <p class="text-muted small mb-3">
                            Preencha os campos abaixo apenas se desejar alterar sua senha.
                        </p>

                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="senha_atual" 
                                       name="senha_atual"
                                       minlength="6">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('senha_atual')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="nova_senha" 
                                       name="nova_senha"
                                       minlength="6">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('nova_senha')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                A senha deve ter pelo menos 6 caracteres
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirma_senha" class="form-label">Confirme a Nova Senha</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       id="confirma_senha" 
                                       name="confirma_senha"
                                       minlength="6">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('confirma_senha')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-1"></i>
                    Outras Informações
                </div>
                <div class="card-body">
                    <p><strong>Tipo de Usuário:</strong><br>
                        <?php
                        $tipoUsuario = match($usuario['tipo_usuario_id']) {
                            USUARIO_ADMIN => 'Administrador',
                            USUARIO_TECNICO => 'Técnico',
                            USUARIO_SOLICITANTE => 'Solicitante',
                            default => 'Desconhecido'
                        };
                        echo htmlspecialchars($tipoUsuario);
                        ?>
                    </p>                    <p><strong>Cadastrado em:</strong><br>
                        <?= !empty($usuario['created_at']) ? date('d/m/Y H:i', strtotime($usuario['created_at'])) : 'Não disponível' ?>
                    </p>
                    <p><strong>Última Atualização:</strong><br>
                        <?= !empty($usuario['updated_at']) ? date('d/m/Y H:i', strtotime($usuario['updated_at'])) : 'Não disponível' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
    
    const icon = event.currentTarget.querySelector('i');
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
}

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