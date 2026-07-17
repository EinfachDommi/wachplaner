<?php

declare(strict_types=1);

require __DIR__ . '/../app/Core/bootstrap.php';

use Wachplaner\Core\System\BootstrapHealthCheck;
use Wachplaner\Core\System\MaintenanceManager;
use Wachplaner\Core\System\MaintenanceResponder;
use Wachplaner\Services\Logging\Logger;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rtrim((string) $path, '/') ?: '/';

$logger = new Logger(WACHPLANER_ROOT . '/storage/logs');
$maintenance = new MaintenanceManager(
    WACHPLANER_ROOT,
    $logger,
    (int) Config::get('maintenance.retry_seconds', 30)
);
$healthCheck = new BootstrapHealthCheck();

$forcedState = $maintenance->forcedState();

if ($forcedState !== null) {
    MaintenanceResponder::render(
        $forcedState,
        loginAvailable: false,
        retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
    );
}

if ($maintenance->shouldShortCircuit()) {
    $state = $maintenance->localState();

    if ($path === '/system/health') {
        MaintenanceResponder::json(
            $state,
            (int) Config::get('maintenance.retry_seconds', 30)
        );
    }

    MaintenanceResponder::render(
        $state,
        loginAvailable: false,
        retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
    );
}

if ($path === '/install') {
    require __DIR__ . '/install.php';
    exit;
}

try {
    $pdo = Database::pdo();

    if (!$healthCheck->database($pdo)) {
        throw new RuntimeException('Database health check failed.', 1002);
    }

    $maintenance->recoverAutomatic();
} catch (Throwable $exception) {
    $state = $maintenance->activateAutomatic('database_unavailable', $exception);

    if ($path === '/system/health') {
        MaintenanceResponder::json(
            $state,
            (int) Config::get('maintenance.retry_seconds', 30)
        );
    }

    MaintenanceResponder::render(
        $state,
        loginAvailable: false,
        retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
    );
}

$manualState = $maintenance->manualState($pdo);

if ($path === '/system/health') {
    if ($manualState->isManual()) {
        MaintenanceResponder::json(
            $manualState,
            (int) Config::get('maintenance.retry_seconds', 30)
        );
    }

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    echo json_encode([
        'status' => 'ok',
        'version' => Config::get('version.number'),
        'build' => Config::get('version.build'),
        'database' => 'ok',
        'storage' => $healthCheck->storage(WACHPLANER_ROOT) ? 'ok' : 'error',
        'checked_at' => date(DATE_ATOM),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
$maintenancePublicRoutes = ['/login', '/logout'];
$maintenanceUpgradeRoute = $path === '/upgrade' && is_admin();

if (
    $manualState->isManual()
    && !is_admin()
    && !in_array($path, $maintenancePublicRoutes, true)
    && !$maintenanceUpgradeRoute
) {
    MaintenanceResponder::render(
        $manualState,
        loginAvailable: true,
        retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
    );
}

if ($path === '/upgrade') {
    require_admin();
    require __DIR__ . '/upgrade.php';
    exit;
}

if ($path === '/logout') {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parameters = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $parameters['path'],
            $parameters['domain'],
            $parameters['secure'],
            $parameters['httponly']
        );
    }

    session_destroy();
    redirect('/login');
}

if ($path === '/login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();

        $statement = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([trim((string) ($_POST['email'] ?? ''))]);
        $user = $statement->fetch();

        if (
            $user
            && password_verify((string) ($_POST['password'] ?? ''), $user['password_hash'])
        ) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            if ($manualState->isManual() && $user['role'] !== 'admin') {
                MaintenanceResponder::render(
                    $manualState,
                    loginAvailable: false,
                    retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
                );
            }

            redirect('/');
        }

        $error = 'Login fehlgeschlagen.';
    }

    $registrationAvailable = (bool) Config::get('registration.enabled', true)
        && $maintenance->registrationEnabled($pdo)
        && !$manualState->active
        && (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0;

    view('auth/login', [
        'error' => $error ?? null,
        'registrationAvailable' => $registrationAvailable,
    ]);
    exit;
}

if ($path === '/register') {
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $registrationEnabled = (bool) Config::get('registration.enabled', true)
        && $maintenance->registrationEnabled($pdo)
        && !$manualState->active;

    if (!$registrationEnabled || $userCount > 0) {
        http_response_code(403);
        view('auth/register', ['open' => false]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();

        $hash = password_hash((string) ($_POST['password'] ?? ''), PASSWORD_DEFAULT);
        $statement = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)'
        );
        $statement->execute([
            trim((string) ($_POST['name'] ?? 'Admin')),
            trim((string) ($_POST['email'] ?? '')),
            $hash,
            'admin',
        ]);

        redirect('/login');
    }

    view('auth/register', ['open' => true]);
    exit;
}

require_auth();

if ($manualState->isManual() && !is_admin()) {
    MaintenanceResponder::render(
        $manualState,
        loginAvailable: false,
        retrySeconds: (int) Config::get('maintenance.retry_seconds', 30)
    );
}

if ($path === '/') {
    $stats = [
        'projects' => (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
        'stations' => (int) $pdo->query('SELECT COUNT(*) FROM stations')->fetchColumn(),
        'vehicle_types' => (int) $pdo->query('SELECT COUNT(*) FROM vehicle_types')->fetchColumn(),
        'expansions' => (int) $pdo->query('SELECT COUNT(*) FROM expansions')->fetchColumn(),
        'trainings' => (int) $pdo->query('SELECT COUNT(*) FROM training_types')->fetchColumn(),
        'building_costs' => (int) $pdo->query('SELECT COUNT(*) FROM station_build_costs')->fetchColumn(),
    ];

    $projects = $pdo->query(
        'SELECT p.*, COUNT(s.id) station_count
         FROM projects p
         LEFT JOIN stations s ON s.project_id = p.id
         GROUP BY p.id
         ORDER BY p.created_at DESC'
    )->fetchAll();

    view('dashboard/index', compact('stats', 'projects', 'manualState'));
    exit;
}

if ($path === '/projects') {
    $projects = $pdo->query('SELECT * FROM projects ORDER BY name')->fetchAll();
    view('projects/index', compact('projects', 'manualState'));
    exit;
}

if ($path === '/projects/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $statement = $pdo->prepare(
        'INSERT INTO projects (name, description, status) VALUES (?, ?, ?)'
    );
    $statement->execute([
        $_POST['name'],
        $_POST['description'] ?? null,
        'active',
    ]);

    $projectId = $pdo->lastInsertId();
    $statement = $pdo->prepare(
        'INSERT INTO dispatch_centers (project_id, name) VALUES (?, ?)'
    );
    $statement->execute([
        $projectId,
        $_POST['dispatch_center'] ?: 'Leitstelle',
    ]);

    redirect('/projects');
}

if ($path === '/system') {
    require_admin();

    $systemCheck = new \Wachplaner\Services\System\SystemCheckService(WACHPLANER_ROOT);
    $checks = $systemCheck->checks($pdo);
    $counts = [
        'Fahrzeugtypen' => (int) $pdo->query('SELECT COUNT(*) FROM vehicle_types')->fetchColumn(),
        'Ausbildungen' => (int) $pdo->query('SELECT COUNT(*) FROM training_types')->fetchColumn(),
        'Erweiterungen' => (int) $pdo->query('SELECT COUNT(*) FROM expansions')->fetchColumn(),
        'Baukosten' => (int) $pdo->query('SELECT COUNT(*) FROM station_build_costs')->fetchColumn(),
        'Projekte' => (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
    ];
    $maintenanceSettings = $maintenance->settings($pdo);
    $localMaintenanceState = $maintenance->localState();
    $saved = isset($_GET['saved']);

    view(
        'system/index',
        compact(
            'checks',
            'counts',
            'maintenanceSettings',
            'manualState',
            'localMaintenanceState',
            'saved'
        )
    );
    exit;
}

if ($path === '/system/maintenance' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin();
    csrf_verify();

    $enabled = isset($_POST['maintenance_enabled']);
    $registrationEnabled = isset($_POST['registration_enabled']);
    $message = (string) ($_POST['maintenance_message'] ?? '');

    $maintenance->updateManual(
        $pdo,
        $enabled,
        $message,
        $registrationEnabled,
        (int) auth_user()['id']
    );

    redirect('/system?saved=1');
}

if ($path === '/admin/masterdata') {
    require_admin();

    $manager = new \Wachplaner\Services\Masterdata\MasterdataManager($pdo);
    $result = null;
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $type = $_POST['import_type'] ?? '';

        if (
            !isset($_FILES['masterdata_file'])
            || ($_FILES['masterdata_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        ) {
            $error = 'Bitte eine gültige XLSX-Datei hochladen.';
        } else {
            $fileName = $_FILES['masterdata_file']['name'] ?? 'upload.xlsx';
            $temporaryPath = $_FILES['masterdata_file']['tmp_name'];
            $result = $manager->import($type, $temporaryPath, $fileName);
        }
    }

    $counts = $manager->counts();
    $logs = $manager->latestLogs();
    $importers = $manager->importers();

    view(
        'admin/masterdata/index',
        compact('counts', 'logs', 'importers', 'result', 'error', 'manualState')
    );
    exit;
}

if ($path === '/masterdata') {
    $vehicleTypes = $pdo->query(
        'SELECT * FROM vehicle_types ORDER BY category, name LIMIT 250'
    )->fetchAll();
    $expansions = $pdo->query(
        'SELECT * FROM expansions ORDER BY station_type, name'
    )->fetchAll();
    $trainings = $pdo->query(
        'SELECT * FROM training_types ORDER BY organisation, name'
    )->fetchAll();

    view(
        'masterdata/index',
        compact('vehicleTypes', 'expansions', 'trainings', 'manualState')
    );
    exit;
}

http_response_code(404);
echo '404';
