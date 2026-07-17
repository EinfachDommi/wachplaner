<?php $versionInfo = app_version(); ?>
<!doctype html>
<html lang="de" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= e($page['title']) ?> · Wachplaner</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <?php require __DIR__ . '/../../partials/navbar.php'; ?>
    <?php require __DIR__ . '/../../partials/sidebar.php'; ?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h1 class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi <?= e($page['icon']) ?> text-primary" aria-hidden="true"></i>
                            <?= e($page['title']) ?>
                        </h1>
                        <?php if ($page['subtitle'] !== ''): ?>
                            <p class="text-body-secondary mb-0 mt-1"><?= e($page['subtitle']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-4">
                        <ol class="breadcrumb float-sm-end mb-0">
                            <li class="breadcrumb-item"><a href="/">Start</a></li>
                            <?php if (current_path() !== '/'): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?= e($page['title']) ?></li>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <?php if (isset($manualState) && $manualState instanceof \Wachplaner\Core\System\MaintenanceState && $manualState->isManual()): ?>
                    <div class="alert alert-warning d-flex align-items-center justify-content-between gap-3" role="alert">
                        <div>
                            <i class="bi bi-cone-striped me-2"></i>
                            <strong>Wartungsmodus aktiv.</strong>
                            Die Anwendung ist nur für Administratoren freigegeben.
                        </div>
                        <a class="btn btn-sm btn-outline-dark" href="/system">Wartung verwalten</a>
                    </div>
                <?php endif; ?>
