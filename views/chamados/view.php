<?php
// Inicializa o controlador de chamados
$chamadoController = new Controllers\ChamadoController();

// Get the chamado ID from URL parameter
$chamado_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch chamado details using the controller
$chamado = $chamadoController->getChamado($chamado_id);

if (!$chamado) {
    echo "<div class='alert alert-danger'>Chamado não encontrado.</div>";
    exit;
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Detalhes do Chamado #<?php echo $chamado['id']; ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($chamado['titulo']); ?></p>
                    <p><strong>Solicitante:</strong> <?php echo htmlspecialchars($chamado['solicitante_nome']); ?></p>
                    <p><strong>Técnico Responsável:</strong> <?php echo htmlspecialchars($chamado['tecnico_nome'] ?? 'Não atribuído'); ?></p>
                    <p><strong>Setor:</strong> <?php echo htmlspecialchars($chamado['setor_nome']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($chamado['status_nome']); ?></p>
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($chamado['categoria_nome']); ?></p>
                    <p><strong>Data de Abertura:</strong> <?php echo date('d/m/Y H:i', strtotime($chamado['data_abertura'])); ?></p>
                    <p><strong>Última Atualização:</strong> <?php echo date('d/m/Y H:i', strtotime($chamado['data_ultima_atualizacao'])); ?></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h4>Descrição</h4>
                    <p><?php echo nl2br(htmlspecialchars($chamado['descricao'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>