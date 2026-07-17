<?php

declare(strict_types=1);

$maintenanceEnabled = filter_var(
    $maintenanceSettings['maintenance_enabled'] ?? '0',
    FILTER_VALIDATE_BOOL
);
$registrationEnabled = filter_var(
    $maintenanceSettings['registration_enabled'] ?? '1',
    FILTER_VALIDATE_BOOL
);
$maintenanceMessage = (string) (
    $maintenanceSettings['maintenance_message']
    ?? 'Der Wachplaner wird aktuell gewartet.'
);
?>

<?php if ($saved): ?>
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div>Systemeinstellungen wurden gespeichert.</div>
    </div>
<?php endif; ?>

<?php if ($maintenanceEnabled): ?>
    <div class="alert alert-warning d-flex align-items-center justify-content-between gap-3" role="alert">
        <div>
            <i class="bi bi-cone-striped me-2"></i>
            <strong>Wartungsmodus aktiv.</strong>
            Nur Administratoren können die Anwendung vollständig verwenden.
        </div>
        <span class="badge text-bg-warning">Manuell</span>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <?php foreach ($checks as $check): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 <?= $check['ok'] ? 'card-outline card-success' : 'card-outline card-danger' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="text-body-secondary small text-uppercase fw-semibold">
                                <?= e($check['label']) ?>
                            </div>
                            <div class="fw-semibold mt-1 text-break">
                                <?= e($check['value']) ?>
                            </div>
                        </div>
                        <span class="status-icon <?= $check['ok'] ? 'text-bg-success' : 'text-bg-danger' ?>">
                            <i class="bi <?= $check['ok'] ? 'bi-check-lg' : 'bi-exclamation-lg' ?>"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card card-outline card-warning h-100">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="bi bi-cone-striped me-2"></i>
                    Wartungsmodus
                </h2>
            </div>

            <form method="post" action="/system/maintenance">
                <div class="card-body">
                    <?= csrf_field() ?>

                    <div class="form-check form-switch mb-4">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="maintenance-enabled"
                            name="maintenance_enabled"
                            <?= $maintenanceEnabled ? 'checked' : '' ?>
                        >
                        <label class="form-check-label fw-semibold" for="maintenance-enabled">
                            Wartungsmodus aktivieren
                        </label>
                        <div class="form-text">
                            Besucher und normale Benutzer sehen die Wartungsseite.
                            Administratoren können sich weiterhin anmelden und testen.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="maintenance-message">
                            Wartungsnachricht
                        </label>
                        <textarea
                            class="form-control"
                            id="maintenance-message"
                            name="maintenance_message"
                            rows="4"
                            maxlength="1000"
                        ><?= e($maintenanceMessage) ?></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="registration-enabled"
                            name="registration_enabled"
                            <?= $registrationEnabled ? 'checked' : '' ?>
                        >
                        <label class="form-check-label fw-semibold" for="registration-enabled">
                            Registrierung grundsätzlich erlauben
                        </label>
                        <div class="form-text">
                            Während einer aktiven Wartung ist die Registrierung unabhängig davon gesperrt.
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center gap-3">
                    <span class="text-body-secondary small">
                        ENV-Notfallmodus:
                        <strong><?= Config::get('maintenance.force', false) ? 'aktiv' : 'aus' ?></strong>
                    </span>
                    <button class="btn btn-warning" type="submit">
                        <i class="bi bi-save me-2"></i>
                        Einstellungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card card-outline card-primary h-100">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="bi bi-shield-pulse me-2"></i>
                    Betriebszustand
                </h2>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">Manuelle Wartung</dt>
                    <dd class="col-sm-6 text-sm-end">
                        <span class="badge <?= $maintenanceEnabled ? 'text-bg-warning' : 'text-bg-success' ?>">
                            <?= $maintenanceEnabled ? 'Aktiv' : 'Aus' ?>
                        </span>
                    </dd>

                    <dt class="col-sm-6">Automatische Wartung</dt>
                    <dd class="col-sm-6 text-sm-end">
                        <span class="badge <?= $localMaintenanceState->isAutomatic() ? 'text-bg-danger' : 'text-bg-success' ?>">
                            <?= $localMaintenanceState->isAutomatic() ? 'Aktiv' : 'Bereit' ?>
                        </span>
                    </dd>

                    <dt class="col-sm-6">Registrierung</dt>
                    <dd class="col-sm-6 text-sm-end">
                        <span class="badge <?= $registrationEnabled ? 'text-bg-info' : 'text-bg-secondary' ?>">
                            <?= $registrationEnabled ? 'Erlaubt' : 'Gesperrt' ?>
                        </span>
                    </dd>

                    <dt class="col-sm-6">Health-Endpunkt</dt>
                    <dd class="col-sm-6 text-sm-end">
                        <a href="/system/health" target="_blank" rel="noopener">
                            /system/health
                        </a>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary mt-4">
    <div class="card-header">
        <h2 class="card-title">
            <i class="bi bi-database-check me-2"></i>
            Stammdatenbestand
        </h2>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bereich</th>
                        <th class="text-end">Datensätze</th>
                        <th class="text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($counts as $name => $count): ?>
                        <tr>
                            <td><?= e($name) ?></td>
                            <td class="text-end fw-semibold">
                                <?= number_format((int) $count, 0, ',', '.') ?>
                            </td>
                            <td class="text-end">
                                <span class="badge <?= (int) $count > 0 ? 'text-bg-success' : 'text-bg-warning' ?>">
                                    <?= (int) $count > 0 ? 'Vorhanden' : 'Leer' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
