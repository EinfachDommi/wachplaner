<?php
require_once __DIR__ . '/../app/Core/bootstrap.php';

require_auth();

$pdo = Database::pdo();
$runner = new \Wachplaner\Services\UpgradeRunner($pdo, __DIR__ . '/../database/upgrades');
$pending = $runner->pending();
$results = [];
$finished = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $results = $runner->run();
    $pending = $runner->pending();
    $finished = count($pending) === 0;
    foreach ($results as $result) {
        if (($result['status'] ?? '') === 'error') {
            $error = $result['message'] ?? 'Upgrade fehlgeschlagen.';
            break;
        }
    }
}

$currentVersion = 'unbekannt';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_meta (meta_key VARCHAR(100) PRIMARY KEY, meta_value TEXT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmt = $pdo->prepare("SELECT meta_value FROM app_meta WHERE meta_key = 'app_version' LIMIT 1");
    $stmt->execute();
    $currentVersion = $stmt->fetchColumn() ?: '0.1.2';
} catch (Throwable $e) {
    $currentVersion = '0.1.2';
}

?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wachplaner Upgrade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="mb-3">Wachplaner Upgrade</h1>
            <p class="text-muted">Diese Seite aktualisiert eine bestehende Installation. Sie legt nur fehlende Tabellen/Strukturen an und führt ausstehende Upgrade-Migrationen aus.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-white">
                        <div class="small text-muted">Aktuelle Version</div>
                        <strong><?= e($currentVersion) ?></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-white">
                        <div class="small text-muted">Zielversion</div>
                        <strong><?= e(Config::get('version.number')) ?> <?= e(Config::get('version.codename')) ?></strong>
                    </div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">Upgrade fehlgeschlagen: <?= e($error) ?></div>
            <?php elseif ($finished && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-success">Upgrade erfolgreich abgeschlossen.</div>
            <?php endif; ?>

            <?php if ($results): ?>
                <h2 class="h5">Ausgeführte Migrationen</h2>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-striped">
                        <thead><tr><th>Migration</th><th>Status</th><th>Dauer</th></tr></thead>
                        <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= e($result['migration'] ?? '') ?></td>
                                <td><?= ($result['status'] ?? '') === 'success' ? '✅ erfolgreich' : '❌ Fehler' ?></td>
                                <td><?= isset($result['duration_ms']) ? e($result['duration_ms']) . ' ms' : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <h2 class="h5">Ausstehende Migrationen</h2>
            <?php if ($pending): ?>
                <ul class="list-group mb-4">
                    <?php foreach ($pending as $migration): ?>
                        <li class="list-group-item"><?= e($migration) ?></li>
                    <?php endforeach; ?>
                </ul>
                <form method="post">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary">Upgrade starten</button>
                    <a class="btn btn-outline-secondary" href="/system">Systemstatus</a>
                </form>
            <?php else: ?>
                <div class="alert alert-info">Keine offenen Migrationen vorhanden.</div>
                <a class="btn btn-primary" href="/system">Zum Systemstatus</a>
                <a class="btn btn-outline-secondary" href="/admin/masterdata">Zu den Stammdaten</a>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
