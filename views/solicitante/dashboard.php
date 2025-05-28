<?php
use Core\Session;
use Models\Chamado;
use Models\Categoria;
use Models\Setor;

// Verifica permissão
if (!Session::isSolicitante()) {
    header('Location: ' . BASE_URL . 'error.php?code=403');
    exit;
}

// Instancia os modelos necessários
$chamadoModel = new Chamado();
$categoriaModel = new Categoria();
$setorModel = new Setor();
$solicitanteId = Session::getUsuarioId();

// Obtém os chamados do solicitante
$meusChamados = $chamadoModel->findBySolicitante($solicitanteId);
$chamadosAbertos = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_ABERTO);
$chamadosEmAtendimento = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_EM_ATENDIMENTO);
$chamadosAguardando = array_filter($meusChamados, fn($c) => $c['status_id'] == STATUS_AGUARDANDO_SOLICITANTE);

// Obtém as categorias e setores para o formulário rápido
$categorias = $categoriaModel->findAll();
$setores = $setorModel->findAll('nome');

// Mensagens de feedback
$success = '';
$error = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'chamado_criado':
            $success = 'Chamado criado com sucesso!';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'chamado_erro':
            $error = 'Erro ao criar chamado. Por favor, tente novamente.';
            break;
    }
}
?>

<div class="container-fluid px-4">
    <div class="row mt-4">
        <div class="col-12">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center">
                <h1>Meu Painel</h1>
                <a href="<?= BASE_URL ?>?page=chamado_form" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Chamado
                </a>
            </div>
        </div>
    </div>
    
    <!-- Cards de Resumo -->
    <div class="row mt-4">
        <!-- Total de Chamados -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total de Chamados</div>
                            <div class="fs-3"><?= count($meusChamados) ?></div>
                        </div>
                        <i class="bi bi-ticket fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= BASE_URL ?>?page=solicitante_chamados">
                        Ver Todos os Chamados
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Chamados Abertos -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Aguardando Atendimento</div>
                            <div class="fs-3"><?= count($chamadosAbertos) ?></div>
                        </div>
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=solicitante_chamados&status=<?= STATUS_ABERTO ?>">
                        Ver Chamados Abertos
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
                       href="<?= BASE_URL ?>?page=solicitante_chamados&status=<?= STATUS_EM_ATENDIMENTO ?>">
                        Ver Em Atendimento
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Aguardando Resposta -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Aguardando Sua Resposta</div>
                            <div class="fs-3"><?= count($chamadosAguardando) ?></div>
                        </div>
                        <i class="bi bi-chat-dots fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" 
                       href="<?= BASE_URL ?>?page=solicitante_chamados&status=<?= STATUS_AGUARDANDO_SOLICITANTE ?>">
                        Ver Pendentes
                    </a>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Formulário Rápido e Chamados -->
    <div class="row">
        <!-- Formulário Rápido -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-lightning me-1"></i>
                    Abertura Rápida de Chamado
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>?action=chamado_criar" method="POST">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título do Chamado</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="titulo" 
                                   name="titulo" 
                                   required 
                                   placeholder="Descreva o problema brevemente">
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>">
                                        <?= htmlspecialchars($categoria['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="setor_destino_id" class="form-label">Setor de Destino</label>
                            <select class="form-select" id="setor_destino_id" name="setor_destino_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($setores as $setor): ?>
                                    <option value="<?= $setor['id'] ?>">
                                        <?= htmlspecialchars($setor['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3" 
                                      required
                                      placeholder="Descreva o problema detalhadamente"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Abrir Chamado
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Últimos Chamados -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock-history me-1"></i>
                    Meus Últimos Chamados
                </div>
                <div class="card-body">
                    <?php if (empty($meusChamados)): ?>
                        <p class="text-muted">Você ainda não possui chamados registrados.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Categoria</th>
                                        <th>Setor</th>
                                        <th>Status</th>
                                        <th>Última Atualização</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Pega os 5 últimos chamados
                                    $ultimosChamados = array_slice($meusChamados, 0, 5);
                                    foreach ($ultimosChamados as $chamado): 
                                    ?>
                                        <tr>
                                            <td><?= $chamado['id'] ?></td>
                                            <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                                            <td><?= htmlspecialchars($chamado['categoria']) ?></td>
                                            <td><?= htmlspecialchars($chamado['setor']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusBadgeClass($chamado['status_id']) ?>">
                                                    <?= htmlspecialchars($chamado['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($chamado['data_ultima_atualizacao'])) ?></td>
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
                        
                        <div class="text-center mt-3">
                            <a href="<?= BASE_URL ?>?page=solicitante_chamados" class="btn btn-primary btn-sm">
                                Ver Todos os Meus Chamados
                            </a>
                        </div>
                    <?php endif; ?>
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
