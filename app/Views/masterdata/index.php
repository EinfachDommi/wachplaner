<div class="card card-primary card-outline">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#vehicles" type="button"><i class="bi bi-truck-front me-2"></i>Fahrzeugtypen</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#expansions" type="button"><i class="bi bi-building-add me-2"></i>Erweiterungen</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#trainings" type="button"><i class="bi bi-mortarboard me-2"></i>Ausbildungen</button></li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="vehicles">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead><tr><th>Name</th><th>Kategorie</th><th>Wache</th><th>Erweiterung</th><th>Personal</th><th>Ausbildung</th><th class="text-end">Kosten</th></tr></thead>
                        <tbody><?php foreach ($vehicleTypes as $vehicle): ?><tr><td><strong><?= e($vehicle['name']) ?></strong></td><td><span class="badge text-bg-light border"><?= e($vehicle['category']) ?></span></td><td><?= e($vehicle['station_type']) ?></td><td><?= e($vehicle['required_expansion']) ?></td><td><?= e($vehicle['personnel_required']) ?></td><td><?= e($vehicle['required_training']) ?></td><td class="text-end text-nowrap"><?= money_fmt($vehicle['credits']) ?></td></tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="expansions">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead><tr><th>Erweiterung</th><th>Wachtyp</th><th class="text-end">Kosten</th><th>Bauzeit</th><th>Freischaltung</th></tr></thead>
                        <tbody><?php foreach ($expansions as $expansion): ?><tr><td><strong><?= e($expansion['name']) ?></strong></td><td><?= e($expansion['station_type']) ?></td><td class="text-end text-nowrap"><?= money_fmt($expansion['cost']) ?></td><td><?= e($expansion['build_time']) ?></td><td><?= e($expansion['unlocks']) ?></td></tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="trainings">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead><tr><th>Ausbildung</th><th>Organisation</th><th>Schule</th><th>Dauer</th></tr></thead>
                        <tbody><?php foreach ($trainings as $training): ?><tr><td><strong><?= e($training['name']) ?></strong></td><td><span class="badge text-bg-light border"><?= e($training['organisation']) ?></span></td><td><?= e($training['school']) ?></td><td><?= e($training['duration_days']) ?> Tage</td></tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
