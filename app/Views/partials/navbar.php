<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Navigation umschalten">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="/" class="nav-link">Dashboard</a></li>
            <li class="nav-item d-none d-md-block"><a href="/projects" class="nav-link">Projekte</a></li>
        </ul>
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <button class="nav-link border-0 bg-transparent" type="button" id="theme-toggle" aria-label="Farbschema wechseln">
                    <i class="bi bi-moon-stars" id="theme-icon"></i>
                </button>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#" role="button">
                    <span class="user-avatar"><?= e(mb_strtoupper(mb_substr(auth_user()['name'] ?? 'A', 0, 1))) ?></span>
                    <span class="d-none d-md-inline"><?= e(auth_user()['name'] ?? 'Administrator') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-body-secondary"><?= e(auth_user()['email'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/system"><i class="bi bi-activity me-2"></i>Systemstatus</a></li>
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Abmelden</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
