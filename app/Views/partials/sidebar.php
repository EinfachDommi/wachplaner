<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="/" class="brand-link text-decoration-none">
            <span class="brand-mark"><i class="bi bi-buildings"></i></span>
            <span class="brand-text fw-semibold">Wachplaner</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/" class="nav-link <?= nav_active('/') ?>">
                        <i class="nav-icon bi bi-speedometer2"></i><p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">PLANUNG</li>
                <li class="nav-item">
                    <a href="/projects" class="nav-link <?= nav_active('/projects', true) ?>">
                        <i class="nav-icon bi bi-folder2-open"></i><p>Projekte</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link disabled" aria-disabled="true">
                        <i class="nav-icon bi bi-broadcast-pin"></i><p>Leitstellen <span class="nav-badge badge text-bg-secondary me-3">später</span></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link disabled" aria-disabled="true">
                        <i class="nav-icon bi bi-building"></i><p>Wachen <span class="nav-badge badge text-bg-secondary me-3">später</span></p>
                    </a>
                </li>

                <li class="nav-header">STAMMDATEN</li>
                <li class="nav-item">
                    <a href="/masterdata" class="nav-link <?= nav_active('/masterdata') ?>">
                        <i class="nav-icon bi bi-database"></i><p>Übersicht</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/masterdata" class="nav-link <?= nav_active('/admin/masterdata') ?>">
                        <i class="nav-icon bi bi-file-earmark-arrow-up"></i><p>Import</p>
                    </a>
                </li>

                <li class="nav-header">LEITSTELLENSPIEL</li>
                <li class="nav-item">
                    <a href="#" class="nav-link disabled" aria-disabled="true">
                        <i class="nav-icon bi bi-arrow-repeat"></i><p>Synchronisation <span class="nav-badge badge text-bg-secondary me-3">Hermes</span></p>
                    </a>
                </li>

                <li class="nav-header">ADMINISTRATION</li>
                <li class="nav-item">
                    <a href="/system" class="nav-link <?= nav_active('/system') ?>">
                        <i class="nav-icon bi bi-shield-check"></i><p>System & Wartung</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/upgrade" class="nav-link <?= nav_active('/upgrade') ?>">
                        <i class="nav-icon bi bi-cloud-arrow-up"></i><p>Upgrade</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
