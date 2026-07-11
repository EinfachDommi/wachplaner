<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
    <a class="btn btn-primary" href="/projects"><i class="bi bi-plus-lg me-2"></i>Projekt anlegen</a>
</div>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['key' => 'projects', 'label' => 'Projekte', 'icon' => 'bi-folder2-open', 'tone' => 'primary'],
        ['key' => 'stations', 'label' => 'Wachen', 'icon' => 'bi-building', 'tone' => 'success'],
        ['key' => 'vehicle_types', 'label' => 'Fahrzeugtypen', 'icon' => 'bi-truck-front', 'tone' => 'warning'],
        ['key' => 'trainings', 'label' => 'Ausbildungen', 'icon' => 'bi-mortarboard', 'tone' => 'info'],
    ];
    foreach ($cards as $card):
    ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="info-box shadow-sm h-100">
                <span class="info-box-icon text-bg-<?= e($card['tone']) ?>"><i class="bi <?= e($card['icon']) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= e($card['label']) ?></span>
                    <span class="info-box-number"><?= number_format((int)($stats[$card['key']] ?? 0), 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card card-primary card-outline h-100">
            <div class="card-header">
                <h2 class="card-title"><i class="bi bi-folder2-open me-2"></i>Realbau-Projekte</h2>
                <div class="card-tools"><a href="/projects" class="btn btn-sm btn-outline-primary">Alle Projekte</a></div>
            </div>
            <div class="card-body">
                <?php if (!$projects): ?>
                    <div class="empty-state py-5 text-center">
                        <i class="bi bi-folder-plus display-4 text-body-tertiary"></i>
                        <h3 class="h5 mt-3">Noch keine Projekte vorhanden</h3>
                        <p class="text-body-secondary">Lege dein erstes Realbau-Projekt mit einer benannten Leitstelle an.</p>
                        <a class="btn btn-primary" href="/projects">Projekt erstellen</a>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($projects as $project): ?>
                            <div class="col-md-6">
                                <article class="project-card border rounded-3 p-3 h-100">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <h3 class="h5 mb-1"><?= e($project['name']) ?></h3>
                                            <span class="badge text-bg-success">Aktiv</span>
                                        </div>
                                        <span class="project-icon"><i class="bi bi-map"></i></span>
                                    </div>
                                    <p class="text-body-secondary small mt-3 mb-3"><?= e($project['description'] ?: 'Keine Beschreibung hinterlegt.') ?></p>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                        <span><i class="bi bi-building me-1"></i><?= (int)$project['station_count'] ?> Wachen</span>
                                        <span class="text-body-secondary small">Planungsstand</span>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header"><h2 class="card-title"><i class="bi bi-database-check me-2"></i>Stammdaten</h2></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>Erweiterungen</span><strong><?= number_format((int)$stats['expansions'], 0, ',', '.') ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Baukosten-Zeilen</span><strong><?= number_format((int)$stats['building_costs'], 0, ',', '.') ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Importstatus</span><span class="badge text-bg-success">Bereit</span></li>
                </ul>
            </div>
            <div class="card-footer"><a href="/admin/masterdata" class="btn btn-sm btn-outline-secondary w-100">Stammdaten verwalten</a></div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header"><h2 class="card-title"><i class="bi bi-arrow-repeat me-2"></i>Synchronisation</h2></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <span class="status-dot status-dot-muted"></span>
                    <div><strong>Noch nicht eingerichtet</strong><div class="small text-body-secondary">Die LSS-Synchronisation folgt mit Hermes.</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
