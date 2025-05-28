<?php

use Core\Session;

if (!Session::isAdmin()) {
    exit('Acesso não autorizado');
}
?>
<!-- Sidebar do Administrador -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_dashboard' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_dashboard">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_usuarios' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_usuarios">
                    <i class="bi bi-people"></i>
                    Usuários
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_chamados' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_chamados">
                    <i class="bi bi-ticket"></i>
                    Chamados
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_categorias' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_categorias">
                    <i class="bi bi-tags"></i>
                    Categorias
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_setores' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_setores">
                    <i class="bi bi-building"></i>
                    Setores
                </a>
            </li>

            <li class="nav-header mt-3">
                <span class="nav-link text-muted">
                    Relatórios
                </span>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_relatorio_chamados' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_relatorio_chamados">
                    <i class="bi bi-graph-up"></i>
                    Chamados
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_relatorio_tecnicos' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_relatorio_tecnicos">
                    <i class="bi bi-person-workspace"></i>
                    Técnicos
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $page === 'admin_relatorio_setores' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>?page=admin_relatorio_setores">
                    <i class="bi bi-building"></i>
                    Por Setor
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
    </div>
</nav>

<!-- Conteúdo principal -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- O conteúdo específico da página será incluído aqui -->