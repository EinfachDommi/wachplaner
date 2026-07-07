<?php require __DIR__.'/../layout/header.php'; ?>
<h1 class="h3 mb-4">Systemstatus</h1>
<div class="row g-3">
    <?php foreach ($checks as $check): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small"><?= e($check['label']) ?></div>
                            <div class="fw-semibold"><?= e($check['value']) ?></div>
                        </div>
                        <span class="badge <?= $check['ok'] ? 'text-bg-success' : 'text-bg-danger' ?>">
                            <?= $check['ok'] ? 'OK' : 'Prüfen' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="h5 mt-5">Stammdaten</h2>
<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead><tr><th>Bereich</th><th class="text-end">Datensätze</th></tr></thead>
        <tbody>
        <?php foreach ($counts as $name => $count): ?>
            <tr><td><?= e($name) ?></td><td class="text-end"><?= number_format((int)$count, 0, ',', '.') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__.'/../layout/footer.php'; ?>
