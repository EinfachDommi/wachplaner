<?php if ($error): ?><div class="alert alert-danger d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="alert <?= $result->success() ? 'alert-success' : 'alert-danger' ?>">
    <div class="d-flex align-items-start gap-2"><i class="bi <?= $result->success() ? 'bi-check-circle-fill' : 'bi-x-octagon-fill' ?> mt-1"></i><div><strong>Import <?= $result->success() ? 'abgeschlossen' : 'fehlgeschlagen' ?></strong><div><?= e($result->type) ?> · verarbeitet: <?= (int)$result->processed ?> · neu: <?= (int)$result->created ?> · aktualisiert: <?= (int)$result->updated ?> · übersprungen: <?= (int)$result->skipped ?></div></div></div>
    <?php if ($result->errors): ?><hr><ul class="mb-0"><?php foreach (array_slice($result->errors, 0, 10) as $message): ?><li><?= e($message) ?></li><?php endforeach; ?></ul><?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card card-primary card-outline h-100">
            <div class="card-header"><h2 class="card-title"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Excel-Datei importieren</h2></div>
            <form method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label" for="import-type">Import-Typ</label><select name="import_type" id="import-type" class="form-select" required><?php foreach ($importers as $key => $importer): ?><option value="<?= e($key) ?>"><?= e($importer->label()) ?></option><?php endforeach; ?></select></div>
                    <div><label class="form-label" for="masterdata-file">Excel-Datei (.xlsx)</label><input type="file" name="masterdata_file" id="masterdata-file" class="form-control" accept=".xlsx" required><div class="form-text">Vorhandene Einträge werden anhand der Importlogik aktualisiert.</div></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary" type="submit"><i class="bi bi-cloud-arrow-up me-2"></i>Import starten</button></div>
            </form>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="row g-3">
            <?php $summaryCards = [['vehicle_types','Fahrzeugtypen','bi-truck-front','primary'],['training_types','Ausbildungen','bi-mortarboard','info'],['extensions','Erweiterungen','bi-building-add','warning'],['building_costs','Baukosten-Zeilen','bi-cash-stack','success']]; foreach ($summaryCards as [$key,$label,$icon,$tone]): ?>
            <div class="col-sm-6"><div class="info-box shadow-sm h-100"><span class="info-box-icon text-bg-<?= e($tone) ?>"><i class="bi <?= e($icon) ?>"></i></span><div class="info-box-content"><span class="info-box-text"><?= e($label) ?></span><span class="info-box-number"><?= number_format((int)$counts[$key], 0, ',', '.') ?></span></div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary">
    <div class="card-header"><h2 class="card-title"><i class="bi bi-clock-history me-2"></i>Letzte Importläufe</h2></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Zeit</th><th>Typ</th><th>Datei</th><th>Verarbeitet</th><th>Neu</th><th>Aktualisiert</th><th>Fehler</th></tr></thead><tbody>
    <?php foreach ($logs as $log): ?><tr><td class="text-nowrap"><?= e($log['created_at']) ?></td><td><span class="badge text-bg-light border"><?= e($log['import_type']) ?></span></td><td><?= e($log['file_name']) ?></td><td><?= (int)$log['processed'] ?></td><td class="text-success"><?= (int)$log['created_count'] ?></td><td class="text-primary"><?= (int)$log['updated_count'] ?></td><td class="<?= (int)$log['error_count'] > 0 ? 'text-danger fw-semibold' : 'text-body-secondary' ?>"><?= (int)$log['error_count'] ?></td></tr><?php endforeach; ?>
    <?php if (!$logs): ?><tr><td colspan="7" class="text-center text-body-secondary py-5">Noch keine Importläufe vorhanden.</td></tr><?php endif; ?>
    </tbody></table></div></div>
</div>
