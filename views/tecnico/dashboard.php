<?php
use Core\Session;
use Models\Chamado;

// Verifica permissão
if (!Session::isTecnico()) {
    header('Location: ' . BASE_URL . 'error.php?code=403');
    exit;
}

// Instancia o modelo de chamados
$chamadoModel = new Chamado();
$tecnicoId = Session::getUsuarioId();

// Obtém estatísticas para o dashboard
$meusChamados = $chamadoModel->findByTecnico($tecnicoId);
$chamadosEmAtendimento = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_EM_ATENDIMENTO);
$chamadosAguardando = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_AGUARDANDO_SOLICITANTE);
$chamadosResolvidos = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_RESOLVIDO);

// Chamados abertos sem técnico atribuído
$chamadosDisponiveis = $chamadoModel->findChamadosDisponiveis();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard do Técnico</h1>
    
    <!-- Cards de Resumo -->
    <div class="row mt-4">
        <!-- Meus Chamados -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Meus Chamados</div>
                            <div class="fs-3"><?= count($meusChamados) ?></div>
                        </div>
                        <i class="bi bi-ticket fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= BASE_URL ?>?page=tecnico_chamados_meus">
                        Ver Meus Chamados
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Em Atendimento -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Em Atendimento</div>
                            <div class="fs-3"><?= count($chamadosEmAtendimento) ?></div>
                        </div>
                        <i class="bi bi-gear fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=tecnico_chamados_meus&status=<?= STATUS_EM_ATENDIMENTO ?>">
                        Ver Em Atendimento
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Aguardando Solicitante -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Aguardando Solicitante</div>
                            <div class="fs-3"><?= count($chamadosAguardando) ?></div>
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=tecnico_chamados_meus&status=<?= STATUS_AGUARDANDO_SOLICITANTE ?>">
                        Ver Aguardando
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Resolvidos -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Resolvidos</div>
                            <div class="fs-3"><?= count($chamadosResolvidos) ?></div>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=tecnico_chamados_meus&status=<?= STATUS_RESOLVIDO ?>">
                        Ver Resolvidos
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chamados e Atividades -->
    <div class="row">
        <!-- Meus Chamados em Atendimento -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-gear me-1"></i>
                    Chamados em Atendimento
                </div>
                <div class="card-body">
                    <?php if (empty($chamadosEmAtendimento)): ?>
                        <p class="text-muted">Nenhum chamado em atendimento no momento.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Solicitante</th>
                                        <th>Categoria</th>
                                        <th>Prioridade</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chamadosEmAtendimento as $chamado): ?>
                                        <tr>
                                            <td><?= $chamado['id'] ?></td>
                                            <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                                            <td><?= htmlspecialchars($chamado['solicitante']) ?></td>
                                            <td><?= htmlspecialchars($chamado['categoria']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getPrioridadeBadgeClass($chamado['prioridade']) ?>">
                                                    <?= htmlspecialchars(ucfirst($chamado['prioridade'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>?page=chamado_view&id=<?= $chamado['id'] ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   title="Ver Detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>?page=chamado_atender&id=<?= $chamado['id'] ?>" 
                                                   class="btn btn-sm btn-success"
                                                   title="Atender">
                                                    <i class="bi bi-play"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Chamados Disponíveis -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-inbox me-1"></i>
                    Chamados Disponíveis
                </div>
                <div class="card-body">
                    <?php if (empty($chamadosDisponiveis)): ?>
                        <p class="text-muted">Não há chamados disponíveis para atendimento.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($chamadosDisponiveis as $chamado): ?>
                                <a href="<?= BASE_URL ?>?page=chamado_view&id=<?= $chamado['id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($chamado['titulo']) ?></h6>
                                        <small><?= date('d/m H:i', strtotime($chamado['data_abertura'])) ?></small>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        <?= htmlspecialchars(substr($chamado['descricao'], 0, 100)) ?>...
                                    </p>
                                    <small>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($chamado['categoria']) ?></span>
                                        <span class="badge bg-<?= getPrioridadeBadgeClass($chamado['prioridade']) ?>">
                                            <?= htmlspecialchars(ucfirst($chamado['prioridade'])) ?>
                                        </span>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($chamadosDisponiveis) > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?= BASE_URL ?>?page=tecnico_chamados_abertos" class="btn btn-primary btn-sm">
                                    Ver Todos os Chamados Disponíveis
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Função auxiliar para determinar a classe do badge de prioridade
function getPrioridadeBadgeClass(string $prioridade): string {
    return match(strtolower($prioridade)) {
        'alta' => 'danger',
        'média' => 'warning',
        'baixa' => 'success',
        default => 'secondary'
    };
}
