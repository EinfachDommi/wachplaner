<?php

declare(strict_types=1);

/**
 * @var string|null $error
 * @var \Wachplaner\Services\Masterdata\ImportResult|null $result
 * @var array<string, \Wachplaner\Services\Masterdata\ImporterInterface> $importers
 * @var array<string, int> $counts
 * @var list<array<string, mixed>> $logs
 */

$resultStatus = $result?->status();
$resultAlertClass = match ($resultStatus) {
    'success' => 'alert-success',
    'warning' => 'alert-warning',
    'error' => 'alert-danger',
    default => 'alert-secondary',
};

$resultIcon = match ($resultStatus) {
    'success' => 'bi-check-circle-fill',
    'warning' => 'bi-exclamation-triangle-fill',
    'error' => 'bi-x-octagon-fill',
    default => 'bi-info-circle-fill',
};

$resultTitle = match ($resultStatus) {
    'success' => 'Import abgeschlossen',
    'warning' => 'Import mit Warnungen abgeschlossen',
    'error' => 'Import fehlgeschlagen',
    default => 'Importstatus',
};

$statusLabels = [
    'success' => 'Erfolgreich',
    'warning' => 'Warnung',
    'error' => 'Fehler',
];

$statusClasses = [
    'success' => 'text-bg-success',
    'warning' => 'text-bg-warning',
    'error' => 'text-bg-danger',
];
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div><?= e($error) ?></div>
    </div>
<?php endif; ?>

<?php if ($result !== null): ?>
    <div class="alert <?= e($resultAlertClass) ?>" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi <?= e($resultIcon) ?> mt-1"></i>
            <div class="flex-grow-1">
                <strong><?= e($resultTitle) ?></strong>
                <div class="mt-1">
                    <?= e($result->type) ?>
                    · verarbeitet: <?= (int) $result->processed ?>
                    · neu: <?= (int) $result->created ?>
                    · aktualisiert: <?= (int) $result->updated ?>
                    · übersprungen: <?= (int) $result->skipped ?>
                </div>

                <?php if ($result->warnings !== []): ?>
                    <hr>
                    <div class="fw-semibold mb-1">Warnungen</div>
                    <ul class="mb-0">
                        <?php foreach (array_slice($result->warnings, 0, 10) as $message): ?>
                            <li><?= e($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($result->errors !== []): ?>
                    <hr>
                    <div class="fw-semibold mb-1">Fehler</div>
                    <ul class="mb-0">
                        <?php foreach (array_slice($result->errors, 0, 10) as $message): ?>
                            <li><?= e($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card card-primary card-outline h-100">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                    Excel-Datei importieren
                </h2>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label" for="import-type">Import-Typ</label>
                        <select name="import_type" id="import-type" class="form-select" required>
                            <?php foreach ($importers as $key => $importer): ?>
                                <option value="<?= e($key) ?>">
                                    <?= e($importer->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="masterdata-file">Excel-Datei (.xlsx)</label>
                        <input
                            type="file"
                            name="masterdata_file"
                            id="masterdata-file"
                            class="form-control"
                            accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            required
                        >
                        <div class="form-text">
                            Vorhandene Einträge werden anhand der Importlogik aktualisiert.
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-cloud-arrow-up me-2"></i>
                        Import starten
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="row g-3">
            <?php
            $summaryCards = [
                ['vehicle_types', 'Fahrzeugtypen', 'bi-truck-front', 'primary'],
                ['training_types', 'Ausbildungen', 'bi-mortarboard', 'info'],
                ['extensions', 'Erweiterungen', 'bi-building-add', 'warning'],
                ['building_costs', 'Baukosten-Zeilen', 'bi-cash-stack', 'success'],
            ];
            ?>

            <?php foreach ($summaryCards as [$key, $label, $icon, $tone]): ?>
                <div class="col-sm-6">
                    <div class="info-box shadow-sm h-100">
                        <span class="info-box-icon text-bg-<?= e($tone) ?>">
                            <i class="bi <?= e($icon) ?>"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?= e($label) ?></span>
                            <span class="info-box-number">
                                <?= number_format((int) ($counts[$key] ?? 0), 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h2 class="card-title">
            <i class="bi bi-clock-history me-2"></i>
            Letzte Importläufe
        </h2>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Zeit</th>
                        <th>Typ</th>
                        <th>Datei</th>
                        <th>Status</th>
                        <th class="text-end">Gesamt</th>
                        <th class="text-end">Neu</th>
                        <th class="text-end">Aktualisiert</th>
                        <th class="text-end">Übersprungen</th>
                        <th>Nachricht</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $status = (string) ($log['status'] ?? 'warning');
                        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                        $statusClass = $statusClasses[$status] ?? 'text-bg-secondary';
                        $message = trim((string) ($log['message'] ?? ''));
                        ?>
                        <tr>
                            <td class="text-nowrap"><?= e((string) ($log['created_at'] ?? '-')) ?></td>
                            <td>
                                <span class="badge text-bg-light border">
                                    <?= e((string) ($log['import_type'] ?? '-')) ?>
                                </span>
                            </td>
                            <td><?= e((string) ($log['source_file'] ?? '-')) ?></td>
                            <td>
                                <span class="badge <?= e($statusClass) ?>">
                                    <?= e($statusLabel) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?= number_format((int) ($log['rows_total'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td class="text-end text-success">
                                <?= number_format((int) ($log['rows_created'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td class="text-end text-primary">
                                <?= number_format((int) ($log['rows_updated'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td class="text-end text-warning">
                                <?= number_format((int) ($log['rows_skipped'] ?? 0), 0, ',', '.') ?>
                            </td>
                            <td style="min-width: 280px;">
                                <?php if ($message !== ''): ?>
                                    <span
                                        title="<?= e($message) ?>"
                                        class="d-inline-block text-truncate"
                                        style="max-width: 420px;"
                                    >
                                        <?= e($message) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-body-secondary">Keine Nachricht</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($logs === []): ?>
                        <tr>
                            <td colspan="9" class="text-center text-body-secondary py-5">
                                Noch keine Importläufe vorhanden.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>