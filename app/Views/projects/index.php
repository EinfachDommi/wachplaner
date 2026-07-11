<div class="row g-4">
    <div class="col-xl-4">
        <div class="card card-primary card-outline sticky-xl-top content-sticky">
            <div class="card-header"><h2 class="card-title"><i class="bi bi-plus-circle me-2"></i>Neues Realbau-Projekt</h2></div>
            <form method="post" action="/projects/create">
                <div class="card-body">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="project-name">Projektname</label>
                        <input class="form-control" id="project-name" name="name" placeholder="z. B. Landkreis Musterstadt" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="dispatch-center">Leitstellenname</label>
                        <input class="form-control" id="dispatch-center" name="dispatch_center" placeholder="z. B. ILS Musterstadt">
                        <div class="form-text">Die Leitstelle wird als zentraler Bereich des Projekts angelegt.</div>
                    </div>
                    <div>
                        <label class="form-label" for="description">Beschreibung</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Gebiet, Zielsetzung oder Realbau-Hinweise"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-floppy me-2"></i>Projekt speichern</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card card-outline card-secondary">
            <div class="card-header"><h2 class="card-title"><i class="bi bi-list-ul me-2"></i>Vorhandene Projekte</h2></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Name</th><th>Status</th><th>Erstellt</th></tr></thead>
                        <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><strong><?= e($project['name']) ?></strong><div class="small text-body-secondary text-truncate table-description"><?= e($project['description'] ?: 'Keine Beschreibung') ?></div></td>
                                <td><span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i><?= e($project['status']) ?></span></td>
                                <td><?= e($project['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$projects): ?><tr><td colspan="3" class="text-center text-body-secondary py-5">Noch keine Projekte vorhanden.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
