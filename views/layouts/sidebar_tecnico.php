<?php

use Core\Session;

if (!Session::isTecnico()) {
    exit('Acesso não autorizado');
}
?>
<!-- Sidebar do Técnico -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $page === 'tecnico_dashboard' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=tecnico_dashboard">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'tecnico_chamados_meus' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=tecnico_chamados_meus">
                    <i class="bi bi-ticket"></i>
                    Meus Chamados
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'tecnico_chamados_abertos' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=tecnico_chamados_abertos">
                    <i class="bi bi-inbox"></i>
                    Chamados Abertos
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'tecnico_chamados_historico' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=tecnico_chamados_historico">
                    <i class="bi bi-clock-history"></i>
                    Histórico
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'usuario_perfil' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=usuario_perfil">
                    <i class="bi bi-person-circle"></i>
                    Meu Perfil
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Filtros Rápidos</span>
        </h6>

        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>?page=tecnico_chamados_meus&status=2">
                    <i class="bi bi-play-circle"></i>
                    Em Atendimento
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>?page=tecnico_chamados_meus&status=3">
                    <i class="bi bi-hourglass-split"></i>
                    Aguardando Solicitante
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>?page=tecnico_chamados_meus&prioridade=alta">
                    <i class="bi bi-exclamation-triangle"></i>
                    Alta Prioridade
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Conteúdo principal -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- O conteúdo específico da página será incluído aqui -->