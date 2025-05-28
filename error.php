<?php
require_once 'config/config.php';

$errorCode = $_GET['code'] ?? '404';
$errorMessage = match($errorCode) {
    '403' => 'Acesso não autorizado',
    '404' => 'Página não encontrada',
    default => 'Erro desconhecido'
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro <?= $errorCode ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1 text-danger mb-4"><?= $errorCode ?></h1>
                <h2 class="h4 mb-4"><?= $errorMessage ?></h2>
                <p class="mb-4">Desculpe, ocorreu um erro ao processar sua solicitação.</p>
                <a href="<?= BASE_URL ?>" class="btn btn-primary">
                    <i class="bi bi-house-door"></i> Voltar para o Início
                </a>
            </div>
        </div>
    </div>
</body>
</html>
