<?php
use Core\Session;
use Models\Chamado;
use Models\Usuario;
use Models\Categoria;
use Models\Setor;

// Verifica permissão
if (!Session::isAdmin()) {
    header('Location: ' . BASE_URL . 'error.php?code=403');
    exit;
}

// Instancia os modelos necessários
$chamadoModel = new Chamado();
$usuarioModel = new Usuario();
$categoriaModel = new Categoria();
$setorModel = new Setor();

// Obtém estatísticas para o dashboard
$totalChamados = count($chamadoModel->findAll());
$chamadosAbertos = count($chamadoModel->findByStatus(STATUS_ABERTO));
$chamadosEmAtendimento = count($chamadoModel->findByStatus(STATUS_EM_ATENDIMENTO));
$chamadosResolvidos = count($chamadoModel->findByStatus(STATUS_RESOLVIDO));

$totalUsuarios = count($usuarioModel->findAll());
$totalTecnicos = count($usuarioModel->findByTipo(USUARIO_TECNICO));
$totalSolicitantes = count($usuarioModel->findByTipo(USUARIO_SOLICITANTE));

// Últimos chamados abertos (limite 5)
$ultimosChamados = $chamadoModel->findRecentes(5);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Administrativo</h1>
    
    <!-- Cards de Resumo -->
    <div class="row mt-4">
        <!-- Card de Chamados -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total de Chamados</div>
                            <div class="fs-3"><?= $totalChamados ?></div>
                        </div>
                        <i class="bi bi-ticket fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= BASE_URL ?>?page=admin_chamados">
                        Ver Detalhes
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Card de Chamados Abertos -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Chamados Abertos</div>
                            <div class="fs-3"><?= $chamadosAbertos ?></div>
                        </div>
                        <i class="bi bi-exclamation-circle fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=admin_chamados&status=<?= STATUS_ABERTO ?>">
                        Ver Chamados Abertos
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Card de Chamados em Atendimento -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Em Atendimento</div>
                            <div class="fs-3"><?= $chamadosEmAtendimento ?></div>
                        </div>
                        <i class="bi bi-gear fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=admin_chamados&status=<?= STATUS_EM_ATENDIMENTO ?>">
                        Ver Em Atendimento
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Card de Chamados Resolvidos -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Resolvidos</div>
                            <div class="fs-3"><?= $chamadosResolvidos ?></div>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=admin_chamados&status=<?= STATUS_RESOLVIDO ?>">
                        Ver Resolvidos
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos e Tabelas -->
    <div class="row">
        <!-- Últimos Chamados -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-table me-1"></i>
                    Últimos Chamados
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Solicitante</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimosChamados as $chamado): ?>
                                    <tr>
                                        <td><?= $chamado['id'] ?></td>
                                        <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                                        <td><?= htmlspecialchars($chamado['solicitante']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getStatusBadgeClass($chamado['status_id']) ?>">
                                                <?= htmlspecialchars($chamado['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($chamado['data_abertura'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?page=chamado_view&id=<?= $chamado['id'] ?>" 
                                               class="btn btn-sm btn-primary"
                                               title="Ver Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estatísticas de Usuários -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-people me-1"></i>
                    Usuários do Sistema
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <div>Total de Usuários</div>
                            <div class="fw-bold"><?= $totalUsuarios ?></div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <div>Técnicos</div>
                            <div class="fw-bold"><?= $totalTecnicos ?></div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" 
                                 style="width: <?= ($totalTecnicos / $totalUsuarios) * 100 ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <div>Solicitantes</div>
                            <div class="fw-bold"><?= $totalSolicitantes ?></div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= ($totalSolicitantes / $totalUsuarios) * 100 ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>?page=admin_usuarios" class="btn btn-primary btn-sm">
                            Gerenciar Usuários
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Função auxiliar para determinar a classe do badge de status
function getStatusBadgeClass(int $statusId): string {
    return match($statusId) {
        STATUS_ABERTO => 'warning',
        STATUS_EM_ATENDIMENTO => 'info',
        STATUS_AGUARDANDO_SOLICITANTE => 'secondary',
        STATUS_RESOLVIDO => 'success',
        STATUS_FECHADO => 'dark',
        default => 'primary'
    };
}
