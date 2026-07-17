<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        throw new RuntimeException('Ungültiger CSRF-Token.');
    }
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!auth_user()) {
        redirect('/login');
    }
}

function is_admin(): bool
{
    return (auth_user()['role'] ?? null) === 'admin';
}

function require_admin(): void
{
    require_auth();

    if (!is_admin()) {
        http_response_code(403);
        throw new RuntimeException('Administratorrechte erforderlich.');
    }
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim($path, '/') ?: '/';
}

function nav_active(string $path, bool $prefix = false): string
{
    $current = current_path();
    $active = $prefix ? str_starts_with($current, $path) : $current === $path;
    return $active ? 'active' : '';
}

function page_meta(string $view): array
{
    return match ($view) {
        'dashboard/index' => ['title' => 'Dashboard', 'subtitle' => 'Realbau-Planung im Überblick', 'icon' => 'bi-speedometer2'],
        'projects/index' => ['title' => 'Projekte', 'subtitle' => 'Regionen und Leitstellen verwalten', 'icon' => 'bi-folder2-open'],
        'masterdata/index' => ['title' => 'Stammdaten', 'subtitle' => 'Fahrzeugtypen, Erweiterungen und Ausbildungen', 'icon' => 'bi-database'],
        'admin/masterdata/index' => ['title' => 'Stammdaten-Import', 'subtitle' => 'Excel-Dateien prüfen und importieren', 'icon' => 'bi-file-earmark-arrow-up'],
        'system/index' => ['title' => 'System & Wartung', 'subtitle' => 'Betriebszustand und Wartungsmodus verwalten', 'icon' => 'bi-shield-check'],
        'auth/login' => ['title' => 'Anmelden', 'subtitle' => '', 'icon' => 'bi-box-arrow-in-right'],
        'auth/register' => ['title' => 'Administration einrichten', 'subtitle' => '', 'icon' => 'bi-person-plus'],
        default => ['title' => 'Wachplaner', 'subtitle' => '', 'icon' => 'bi-grid'],
    };
}

function app_version(): array
{
    $defaults = [
        'version' => Config::get('version.number', '0.2.1'),
        'codename' => Config::get('version.codename', 'Atlas'),
        'build' => Config::get('version.build', 'development'),
        'branch' => Config::get('app_env') === 'development' ? 'develop' : 'main',
    ];

    $file = defined('WACHPLANER_ROOT') ? WACHPLANER_ROOT . '/storage/version.json' : null;
    if (!$file || !is_file($file)) {
        return $defaults;
    }

    $decoded = json_decode((string) file_get_contents($file), true);
    return is_array($decoded) ? array_merge($defaults, $decoded) : $defaults;
}

function view(string $view, array $data = []): void
{
    extract($data);
    $page = page_meta($view);
    $isAuthView = str_starts_with($view, 'auth/');
    $layout = $isAuthView ? 'auth' : 'app';

    require __DIR__ . '/../Views/layouts/' . $layout . '/header.php';
    require __DIR__ . '/../Views/' . $view . '.php';
    require __DIR__ . '/../Views/layouts/' . $layout . '/footer.php';
}

function money_fmt($number): string
{
    return number_format((float) $number, 0, ',', '.') . ' Credits';
}
