<?php
require __DIR__ . '/../app/Core/bootstrap.php';

$checks = [
    'PHP >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'storage writable' => is_writable(__DIR__ . '/../storage'),
    'cache writable' => is_writable(__DIR__ . '/../storage/cache'),
    'logs writable' => is_writable(__DIR__ . '/../storage/logs'),
];

foreach ($checks as $name => $ok) {
    echo sprintf("[%s] %s\n", $ok ? 'OK' : 'FAIL', $name);
}

try {
    $pdo = Database::pdo();
    echo '[OK] database connection' . PHP_EOL;
    foreach (['vehicle_types', 'training_types', 'expansions', 'station_build_costs'] as $table) {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo sprintf("[OK] %s: %d records\n", $table, $count);
    }
} catch (Throwable $e) {
    echo '[FAIL] database: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
