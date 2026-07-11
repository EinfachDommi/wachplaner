<div class="login-logo mb-3">
    <a href="/" class="text-decoration-none text-body-emphasis">
        <span class="brand-mark brand-mark-lg me-2"><i class="bi bi-buildings"></i></span>
        <strong>Wachplaner</strong>
    </a>
</div>
<div class="card card-outline card-primary shadow">
    <div class="card-header text-center border-0 pt-4">
        <h1 class="h4 mb-1">Administration einrichten</h1>
        <p class="text-body-secondary mb-0">Das erste Konto erhält Administratorrechte.</p>
    </div>
    <div class="card-body login-card-body">
        <?php if (!$open): ?>
            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>Es existiert bereits ein Benutzer. Die öffentliche Registrierung ist geschlossen.
            </div>
            <a class="btn btn-primary w-100" href="/login">Zur Anmeldung</a>
        <?php else: ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="form-floating mb-3">
                    <input class="form-control" id="name" name="name" placeholder="Name" autocomplete="name" required autofocus>
                    <label for="name">Anzeigename</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="email" name="email" type="email" placeholder="name@example.de" autocomplete="email" required>
                    <label for="email">E-Mail-Adresse</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="password" name="password" type="password" placeholder="Passwort" autocomplete="new-password" minlength="8" required>
                    <label for="password">Passwort (mindestens 8 Zeichen)</label>
                </div>
                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-person-check me-2"></i>Admin erstellen
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
