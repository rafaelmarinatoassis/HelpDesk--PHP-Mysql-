<?php
// Inicializa o controlador de chamados
$chamadoController = new Controllers\ChamadoController();
$usuarioId = Core\Session::getUsuarioId();

// Busca os chamados do solicitante
$chamados = $chamadoController->getChamadosBySolicitante($usuarioId);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Meus Chamados</h2>
        <a href="<?= BASE_URL ?>?page=chamado_form" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Chamado
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Data de Abertura</th>
                            <th>Última Atualização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($chamados)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum chamado encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($chamados as $chamado): ?>
                                <tr>
                                    <td><?= $chamado['id'] ?></td>
                                    <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                                    <td><?= htmlspecialchars($chamado['categoria']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusColor($chamado['status_id']) ?>">
                                            <?= htmlspecialchars($chamado['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($chamado['data_abertura'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($chamado['data_ultima_atualizacao'])) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?page=chamado_view&id=<?= $chamado['id'] ?>"
                                            class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusColor($statusId)
{
    return match ($statusId) {
        STATUS_ABERTO => 'primary',
        STATUS_EM_ATENDIMENTO => 'warning',
        STATUS_AGUARDANDO_SOLICITANTE => 'info',
        STATUS_RESOLVIDO => 'success',
        STATUS_FECHADO => 'secondary',
        default => 'light'
    };
}
?>