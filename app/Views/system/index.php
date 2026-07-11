<div class="row g-3 mb-4">
    <?php foreach ($checks as $check): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 <?= $check['ok'] ? 'card-outline card-success' : 'card-outline card-danger' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div><div class="text-body-secondary small text-uppercase fw-semibold"><?= e($check['label']) ?></div><div class="fw-semibold mt-1 text-break"><?= e($check['value']) ?></div></div>
                        <span class="status-icon <?= $check['ok'] ? 'text-bg-success' : 'text-bg-danger' ?>"><i class="bi <?= $check['ok'] ? 'bi-check-lg' : 'bi-exclamation-lg' ?>"></i></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card card-outline card-primary">
    <div class="card-header"><h2 class="card-title"><i class="bi bi-database-check me-2"></i>Stammdatenbestand</h2></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Bereich</th><th class="text-end">Datensätze</th><th class="text-end">Status</th></tr></thead><tbody>
        <?php foreach ($counts as $name => $count): ?><tr><td><?= e($name) ?></td><td class="text-end fw-semibold"><?= number_format((int)$count, 0, ',', '.') ?></td><td class="text-end"><span class="badge <?= (int)$count > 0 ? 'text-bg-success' : 'text-bg-warning' ?>"><?= (int)$count > 0 ? 'Vorhanden' : 'Leer' ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
