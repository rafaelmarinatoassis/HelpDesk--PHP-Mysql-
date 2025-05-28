<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Core/Session.php';
require_once __DIR__ . '/../../src/Models/Setor.php';
require_once __DIR__ . '/../../src/Models/Categoria.php';

use Core\Session;
use Models\Setor;
use Models\Categoria;

Session::init();

// Verifica se o usuário está logado e é um solicitante
if (!Session::isSolicitante()) {
    header('Location: ' . BASE_URL . 'error.php?code=403');
    exit;
}

// Instancia os modelos necessários
$setorModel = new Setor();
$categoriaModel = new Categoria();

// Obtém listas para os selects do formulário
$setores = $setorModel->findAll('nome');
$categorias = $categoriaModel->findAll('nome');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <?php require_once __DIR__ . '/../layouts/sidebar_solicitante.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-0">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-4 mb-3 border-bottom">
            <h1 class="h2">Novo Chamado</h1>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mx-0 border-0 rounded-0 h-100">
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>?action=chamado_criar" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Chamado</label>
                        <input type="text"
                            class="form-control"
                            id="titulo"
                            name="titulo"
                            required
                            maxlength="100"
                            placeholder="Digite um título breve e descritivo">
                        <div class="invalid-feedback">
                            Por favor, informe um título para o chamado
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição Detalhada</label>
                        <textarea class="form-control"
                            id="descricao"
                            name="descricao"
                            rows="5"
                            required
                            placeholder="Descreva detalhadamente o problema ou solicitação"></textarea>
                        <div class="invalid-feedback">
                            Por favor, forneça uma descrição detalhada
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione uma categoria...</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>">
                                        <?= htmlspecialchars($categoria['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Selecione uma categoria
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="setor_destino_id" class="form-label">Setor de Destino</label>
                            <select class="form-select" id="setor_destino_id" name="setor_destino_id" required>
                                <option value="">Selecione o setor de destino...</option>
                                <?php foreach ($setores as $setor): ?>
                                    <option value="<?= $setor['id'] ?>">
                                        <?= htmlspecialchars($setor['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Selecione o setor de destino
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="<?= BASE_URL ?>" class="btn btn-light me-md-2">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Criar Chamado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
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