<div class="login-logo mb-3">
    <a href="/" class="text-decoration-none text-body-emphasis">
        <span class="brand-mark brand-mark-lg me-2"><i class="bi bi-buildings"></i></span>
        <strong>Wachplaner</strong>
    </a>
</div>
<div class="card card-outline card-primary shadow">
    <div class="card-header text-center border-0 pt-4">
        <h1 class="h4 mb-1">Willkommen zurück</h1>
        <p class="text-body-secondary mb-0">Bitte mit dem Administratorkonto anmelden.</p>
    </div>
    <div class="card-body login-card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="input-group mb-3">
                <div class="form-floating">
                    <input class="form-control" id="email" name="email" type="email" placeholder="name@example.de" autocomplete="username" required autofocus>
                    <label for="email">E-Mail-Adresse</label>
                </div>
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            </div>
            <div class="input-group mb-3">
                <div class="form-floating">
                    <input class="form-control" id="password" name="password" type="password" placeholder="Passwort" autocomplete="current-password" required>
                    <label for="password">Passwort</label>
                </div>
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
            </div>
            <button class="btn btn-primary w-100" type="submit">
                <i class="bi bi-box-arrow-in-right me-2"></i>Anmelden
            </button>
        </form>
        <?php if (!empty($registrationAvailable)): ?>
            <p class="text-center small mt-4 mb-0">
                Noch keine Installation? <a href="/register">Ersten Admin anlegen</a>
            </p>
        <?php endif; ?>
    </div>
</div>
