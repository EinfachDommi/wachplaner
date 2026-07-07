<?php

require __DIR__ . '/../app/Core/bootstrap.php';

use Wachplaner\Services\Masterdata\MasterdataManager;

if (PHP_SAPI !== 'cli') {
    exit("Dieses Skript ist nur für die Kommandozeile gedacht.\n");
}

$type = $argv[1] ?? null;
$file = $argv[2] ?? null;

if (!$type || !$file || !is_file($file)) {
    echo "Nutzung: php scripts/import_masterdata.php <vehicles|trainings|extensions|building_costs> <datei.xlsx>\n";
    exit(1);
}

$manager = new MasterdataManager(Database::pdo());
$result = $manager->import($type, $file, basename($file));

echo "Import: {$result->type}\n";
echo "Verarbeitet: {$result->processed}\n";
echo "Neu: {$result->created}\n";
echo "Aktualisiert: {$result->updated}\n";
echo "Übersprungen: {$result->skipped}\n";

if (!$result->success()) {
    echo "Fehler:\n";
    foreach ($result->errors as $error) {
        echo "- {$error}\n";
    }
    exit(1);
}

echo "OK\n";
