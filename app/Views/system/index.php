<?php

declare(strict_types=1);

$activeTab = in_array($activeTab ?? 'overview', [
    'overview', 'health', 'maintenance', 'logs', 'updates', 'security', 'settings'
], true) ? $activeTab : 'overview';
$maintenanceEnabled = filter_var($maintenanceSettings['maintenance_enabled'] ?? '0', FILTER_VALIDATE_BOOL);
$registrationEnabled = filter_var($maintenanceSettings['registration_enabled'] ?? '1', FILTER_VALIDATE_BOOL);
$maintenanceMessage = (string) ($maintenanceSettings['maintenance_message'] ?? 'Der Wachplaner wird aktuell gewartet.');
$healthTone = match ($healthSummary['status'] ?? 'critical') {
    'healthy' => ['success', 'Gesund', 'bi-check-circle-fill'],
    'warning' => ['warning', 'Warnungen', 'bi-exclamation-triangle-fill'],
    default => ['danger', 'Kritisch', 'bi-x-octagon-fill'],
};
?>

<?php if (!empty($saved)): ?>
<div class="alert alert-success d-flex align-items-center" role="alert"><i class="bi bi-check-circle-fill me-2"></i><div>Systemeinstellungen wurden gespeichert.</div></div>
<?php endif; ?>
<?php if (!empty($saveError)): ?>
<div class="alert alert-danger d-flex align-items-center" role="alert"><i class="bi bi-x-octagon-fill me-2"></i><div><?= e($saveError) ?></div></div>
<?php endif; ?>
<?php if ($maintenanceEnabled): ?>
<div class="alert alert-warning d-flex align-items-center justify-content-between gap-3" role="alert"><div><i class="bi bi-cone-striped me-2"></i><strong>Wartungsmodus aktiv.</strong> Nur Administratoren können die Anwendung vollständig verwenden.</div><span class="badge text-bg-warning">Manuell</span></div>
<?php endif; ?>

<div class="card card-outline card-primary mb-4">
<div class="card-body py-3"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h2 class="h4 mb-1">Guardian System-Center</h2><div class="text-body-secondary">Betriebszustand, Wartung, Logs und systemweite Einstellungen.</div></div><div class="d-flex gap-2"><span class="badge text-bg-<?= e($healthTone[0]) ?> fs-6"><i class="bi <?= e($healthTone[2]) ?> me-1"></i><?= e($healthTone[1]) ?></span><span class="badge text-bg-light border fs-6"><?= e((string) Config::get('version.number')) ?> <?= e((string) Config::get('version.codename')) ?></span></div></div></div>
<div class="card-header p-0 border-top"><ul class="nav nav-tabs flex-nowrap overflow-auto px-3 pt-2">
<?php foreach ([
'overview'=>['Übersicht','bi-speedometer2'],'health'=>['Health','bi-heart-pulse'],'maintenance'=>['Wartung','bi-cone-striped'],'logs'=>['Logs','bi-journal-text'],'updates'=>['Updates','bi-cloud-arrow-up'],'security'=>['Sicherheit','bi-shield-lock'],'settings'=>['Einstellungen','bi-sliders']
] as $key => [$label,$icon]): ?>
<li class="nav-item"><a class="nav-link text-nowrap <?= $activeTab === $key ? 'active' : '' ?>" href="/system?tab=<?= e($key) ?>"><i class="bi <?= e($icon) ?> me-1"></i><?= e($label) ?></a></li>
<?php endforeach; ?>
</ul></div></div>

<?php if ($activeTab === 'overview'): ?>
<div class="row g-3 mb-4">
<div class="col-md-6 col-xl-3"><div class="info-box shadow-sm"><span class="info-box-icon text-bg-<?= e($healthTone[0]) ?>"><i class="bi <?= e($healthTone[2]) ?>"></i></span><div class="info-box-content"><span class="info-box-text">Systemzustand</span><span class="info-box-number"><?= e($healthTone[1]) ?></span></div></div></div>
<div class="col-md-6 col-xl-3"><div class="info-box shadow-sm"><span class="info-box-icon text-bg-warning"><i class="bi bi-cone-striped"></i></span><div class="info-box-content"><span class="info-box-text">Wartung</span><span class="info-box-number"><?= $maintenanceEnabled ? 'Aktiv' : 'Aus' ?></span></div></div></div>
<div class="col-md-6 col-xl-3"><div class="info-box shadow-sm"><span class="info-box-icon text-bg-info"><i class="bi bi-person-plus"></i></span><div class="info-box-content"><span class="info-box-text">Registrierung</span><span class="info-box-number"><?= $registrationEnabled ? 'Erlaubt' : 'Gesperrt' ?></span></div></div></div>
<div class="col-md-6 col-xl-3"><div class="info-box shadow-sm"><span class="info-box-icon text-bg-primary"><i class="bi bi-flag"></i></span><div class="info-box-content"><span class="info-box-text">Feature Flags</span><span class="info-box-number"><?= count(array_filter($featureFlags, fn($flag) => $flag['enabled'])) ?> aktiv</span></div></div></div>
</div>
<div class="row g-4"><div class="col-xl-7"><div class="card card-outline card-primary h-100"><div class="card-header"><h2 class="card-title">Systemstatus</h2></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Prüfung</th><th>Wert</th><th class="text-end">Status</th></tr></thead><tbody><?php foreach ($checks as $check): ?><tr><td><?= e((string)$check['label']) ?></td><td><?= e((string)$check['value']) ?></td><td class="text-end"><span class="badge <?= $check['ok'] ? 'text-bg-success' : (($check['level'] ?? '') === 'warning' ? 'text-bg-warning' : 'text-bg-danger') ?>"><?= $check['ok'] ? 'OK' : e((string)($check['level'] ?? 'Fehler')) ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<div class="col-xl-5"><div class="card card-outline card-secondary h-100"><div class="card-header"><h2 class="card-title">Stammdatenbestand</h2></div><div class="card-body p-0"><table class="table table-hover mb-0"><tbody><?php foreach ($counts as $name=>$count): ?><tr><td><?= e($name) ?></td><td class="text-end fw-semibold"><?= number_format((int)$count,0,',','.') ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php endif; ?>

<?php if ($activeTab === 'health'): ?>
<div class="row g-3"><?php foreach ($checks as $check): ?><div class="col-md-6 col-xl-4"><div class="card h-100 card-outline <?= $check['ok'] ? 'card-success' : (($check['level'] ?? '') === 'warning' ? 'card-warning' : 'card-danger') ?>"><div class="card-body"><div class="d-flex justify-content-between gap-3"><div><div class="small text-uppercase text-body-secondary fw-semibold"><?= e((string)$check['label']) ?></div><div class="fw-semibold mt-1 text-break"><?= e((string)$check['value']) ?></div></div><span class="status-icon <?= $check['ok'] ? 'text-bg-success' : 'text-bg-danger' ?>"><i class="bi <?= $check['ok'] ? 'bi-check-lg' : 'bi-exclamation-lg' ?>"></i></span></div></div></div></div><?php endforeach; ?></div>
<?php endif; ?>

<?php if ($activeTab === 'maintenance'): ?>
<div class="row g-4"><div class="col-xl-8"><div class="card card-outline card-warning"><div class="card-header"><h2 class="card-title">Wartungsmodus</h2></div><form method="post" action="/system/maintenance"><div class="card-body"><?= csrf_field() ?><div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" id="maintenance-enabled" name="maintenance_enabled" <?= $maintenanceEnabled?'checked':'' ?>><label class="form-check-label fw-semibold" for="maintenance-enabled">Wartungsmodus aktivieren</label></div><div class="mb-4"><label class="form-label" for="maintenance-message">Wartungsnachricht</label><textarea class="form-control" id="maintenance-message" name="maintenance_message" rows="5" maxlength="1000"><?= e($maintenanceMessage) ?></textarea></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="registration-enabled" name="registration_enabled" <?= $registrationEnabled?'checked':'' ?>><label class="form-check-label fw-semibold" for="registration-enabled">Registrierung grundsätzlich erlauben</label></div></div><div class="card-footer text-end"><button class="btn btn-warning" type="submit"><i class="bi bi-save me-2"></i>Speichern</button></div></form></div></div><div class="col-xl-4"><div class="card card-outline card-primary"><div class="card-header"><h2 class="card-title">Betriebszustand</h2></div><div class="card-body"><dl class="row mb-0"><dt class="col-7">Manuell</dt><dd class="col-5 text-end"><?= $maintenanceEnabled?'Aktiv':'Aus' ?></dd><dt class="col-7">Automatisch</dt><dd class="col-5 text-end"><?= $localMaintenanceState->isAutomatic()?'Aktiv':'Bereit' ?></dd><dt class="col-7">ENV-Override</dt><dd class="col-5 text-end"><?= Config::get('maintenance.force',false)?'Aktiv':'Aus' ?></dd><dt class="col-7">Health API</dt><dd class="col-5 text-end"><a href="/system/health" target="_blank">öffnen</a></dd></dl></div></div></div></div>
<?php endif; ?>

<?php if ($activeTab === 'logs'): ?>
<div class="row g-4"><div class="col-xl-4"><div class="card card-outline card-secondary"><div class="card-header"><h2 class="card-title">Logdateien</h2></div><div class="list-group list-group-flush"><?php foreach ($logFiles as $file): ?><div class="list-group-item"><div class="d-flex justify-content-between"><strong><?= e($file['channel']) ?>.log</strong><span><?= number_format($file['size']/1024,1,',','.') ?> KB</span></div><small class="text-body-secondary"><?= $file['modified']?date('d.m.Y H:i:s',$file['modified']):'-' ?></small></div><?php endforeach; ?><?php if ($logFiles===[]): ?><div class="list-group-item text-body-secondary">Keine Logdateien vorhanden.</div><?php endif; ?></div></div></div><div class="col-xl-8"><div class="card card-outline card-primary"><div class="card-header"><h2 class="card-title">Letzte Einträge</h2></div><div class="card-body bg-body-tertiary" style="max-height:600px;overflow:auto"><pre class="small mb-0 text-wrap"><?php foreach ($logs as $entry): ?><span class="badge text-bg-secondary me-2"><?= e($entry['channel']) ?></span><?= e($entry['line']) ?>
<?php endforeach; ?><?php if ($logs===[]): ?>Keine Logeinträge vorhanden.<?php endif; ?></pre></div></div></div></div>
<?php endif; ?>

<?php if ($activeTab === 'updates'): ?>
<div class="card card-outline card-primary"><div class="card-header"><h2 class="card-title">Version und Updates</h2></div><div class="card-body"><dl class="row"><dt class="col-sm-3">Version</dt><dd class="col-sm-9"><?= e((string)Config::get('version.number')) ?> <?= e((string)Config::get('version.codename')) ?></dd><dt class="col-sm-3">Build</dt><dd class="col-sm-9"><?= e((string)Config::get('version.build')) ?></dd><dt class="col-sm-3">Umgebung</dt><dd class="col-sm-9"><?= e((string)Config::get('app_env')) ?></dd></dl><a class="btn btn-primary" href="/upgrade"><i class="bi bi-cloud-arrow-up me-2"></i>Upgrade-Assistent öffnen</a></div></div>
<?php endif; ?>

<?php if ($activeTab === 'security'): ?>
<div class="row g-4"><div class="col-xl-6"><div class="card card-outline card-primary"><div class="card-header"><h2 class="card-title">Feature Flags</h2></div><form method="post" action="/system/feature-flags"><div class="card-body"><?= csrf_field() ?><?php foreach($featureFlags as $key=>$flag): ?><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="<?= e($key) ?>" name="flags[<?= e($key) ?>]" <?= $flag['enabled']?'checked':'' ?>><label class="form-check-label" for="<?= e($key) ?>"><?= e($flag['label']) ?></label></div><?php endforeach; ?><div class="alert alert-info mb-0">Feature Flags schalten vorbereitete Module frei. Nicht implementierte Module bleiben wirkungslos.</div></div><div class="card-footer text-end"><button class="btn btn-primary" type="submit">Feature Flags speichern</button></div></form></div></div><div class="col-xl-6"><div class="card card-outline card-secondary"><div class="card-header"><h2 class="card-title">Shield Vorbereitung</h2></div><div class="card-body"><ul class="list-group list-group-flush"><li class="list-group-item d-flex justify-content-between">CSRF-Schutz <span class="badge text-bg-success">Aktiv</span></li><li class="list-group-item d-flex justify-content-between">Sichere Session-Cookies <span class="badge text-bg-success">Aktiv</span></li><li class="list-group-item d-flex justify-content-between">Rate Limiting <span class="badge text-bg-secondary">V0.2.3</span></li><li class="list-group-item d-flex justify-content-between">Honeypot <span class="badge text-bg-secondary">V0.2.3</span></li><li class="list-group-item d-flex justify-content-between">Audit-Log <span class="badge text-bg-secondary">V0.2.3</span></li></ul></div></div></div></div>
<?php endif; ?>

<?php if ($activeTab === 'settings'): ?>
<div class="card card-outline card-primary"><div class="card-header"><h2 class="card-title">Systemeinstellungen</h2></div><form method="post" action="/system/settings"><div class="card-body"><?= csrf_field() ?><div class="row g-3"><div class="col-md-6"><label class="form-label" for="app-timezone">Zeitzone</label><input class="form-control" id="app-timezone" name="app_timezone" value="<?= e($systemSettings['app_timezone']??'Europe/Berlin') ?>" maxlength="80"></div><div class="col-md-6"><label class="form-label" for="app-locale">Locale</label><input class="form-control" id="app-locale" name="app_locale" value="<?= e($systemSettings['app_locale']??'de_DE') ?>" maxlength="20"></div></div></div><div class="card-footer text-end"><button class="btn btn-primary" type="submit"><i class="bi bi-save me-2"></i>Einstellungen speichern</button></div></form></div>
<?php endif; ?>
