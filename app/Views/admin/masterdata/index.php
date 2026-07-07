<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="h3 mb-1">Stammdaten-Import</h1>
                <p class="text-muted mb-0">Excel-Dateien importieren und Stammdaten zentral aktualisieren.</p>
            </div>
            <span class="badge text-bg-danger">Sprint 2 · Atlas</span>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="col-12"><div class="alert alert-danger"><?= e($error) ?></div></div>
    <?php endif; ?>

    <?php if ($result): ?>
        <div class="col-12">
            <div class="alert <?= $result->success() ? 'alert-success' : 'alert-danger' ?>">
                <strong>Import <?= $result->success() ? 'abgeschlossen' : 'fehlgeschlagen' ?>:</strong>
                <?= e($result->type) ?> · verarbeitet: <?= (int)$result->processed ?> · neu: <?= (int)$result->created ?> · aktualisiert: <?= (int)$result->updated ?> · übersprungen: <?= (int)$result->skipped ?>
                <?php if ($result->errors): ?>
                    <hr>
                    <ul class="mb-0">
                        <?php foreach (array_slice($result->errors, 0, 10) as $message): ?>
                            <li><?= e($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5">Neue Stammdaten importieren</h2>
                <form method="post" enctype="multipart/form-data" class="vstack gap-3">
                    <?= csrf_field() ?>
                    <div>
                        <label class="form-label">Import-Typ</label>
                        <select name="import_type" class="form-select" required>
                            <?php foreach ($importers as $key => $importer): ?>
                                <option value="<?= e($key) ?>"><?= e($importer->label()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Excel-Datei (.xlsx)</label>
                        <input type="file" name="masterdata_file" class="form-control" accept=".xlsx" required>
                    </div>
                    <button class="btn btn-danger">Import starten</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="row g-3">
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Fahrzeugtypen</div><div class="fs-3 fw-bold"><?= number_format($counts['vehicle_types'], 0, ',', '.') ?></div></div></div></div>
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Ausbildungen</div><div class="fs-3 fw-bold"><?= number_format($counts['training_types'], 0, ',', '.') ?></div></div></div></div>
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Erweiterungen</div><div class="fs-3 fw-bold"><?= number_format($counts['extensions'], 0, ',', '.') ?></div></div></div></div>
            <div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Baukosten-Zeilen</div><div class="fs-3 fw-bold"><?= number_format($counts['building_costs'], 0, ',', '.') ?></div></div></div></div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5">Letzte Importläufe</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Zeit</th><th>Typ</th><th>Datei</th><th>Verarbeitet</th><th>Neu</th><th>Aktualisiert</th><th>Fehler</th></tr></thead>
                        <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= e($log['created_at']) ?></td>
                                <td><?= e($log['import_type']) ?></td>
                                <td><?= e($log['file_name']) ?></td>
                                <td><?= (int)$log['processed'] ?></td>
                                <td><?= (int)$log['created_count'] ?></td>
                                <td><?= (int)$log['updated_count'] ?></td>
                                <td><?= (int)$log['error_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$logs): ?><tr><td colspan="7" class="text-muted">Noch keine Importläufe vorhanden.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
