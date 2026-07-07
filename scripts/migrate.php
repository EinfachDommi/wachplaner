<?php
require __DIR__.'/../app/Core/bootstrap.php';
$sql=file_get_contents(__DIR__.'/../database/schema.sql');
Database::pdo()->exec($sql);
echo "Wachplaner V0.1: Datenbank installiert und Stammdaten importiert.
";
