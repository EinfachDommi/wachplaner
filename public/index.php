<?php
require __DIR__.'/../app/Core/bootstrap.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';
if ($path === '/install') { require __DIR__.'/install.php'; exit; }
if ($path === '/upgrade') { require __DIR__.'/upgrade.php'; exit; }

$pdo = Database::pdo();

if ($path === '/logout') { session_destroy(); redirect('/login'); }
if ($path === '/login') {
    if ($_SERVER['REQUEST_METHOD']==='POST') { csrf_verify(); $stmt=$pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); $stmt->execute([$_POST['email']??'']); $u=$stmt->fetch(); if($u && password_verify($_POST['password']??'', $u['password_hash'])){ session_regenerate_id(true); $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']]; redirect('/'); } $error='Login fehlgeschlagen.'; }
    view('auth/login', ['error'=>$error ?? null]); exit;
}
if ($path === '/register') {
    $count=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($_SERVER['REQUEST_METHOD']==='POST') { csrf_verify(); if($count>0){ die('Registrierung ist nach dem ersten Benutzer gesperrt.'); } $hash=password_hash($_POST['password']??'', PASSWORD_DEFAULT); $stmt=$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)"); $stmt->execute([$_POST['name']??'Admin',$_POST['email']??'', $hash, 'admin']); redirect('/login'); }
    view('auth/register', ['open'=>$count===0]); exit;
}
require_auth();
if ($path === '/') {
    $stats=[];
    $stats['projects']=(int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    $stats['stations']=(int)$pdo->query('SELECT COUNT(*) FROM stations')->fetchColumn();
    $stats['vehicle_types']=(int)$pdo->query('SELECT COUNT(*) FROM vehicle_types')->fetchColumn();
    $stats['expansions']=(int)$pdo->query('SELECT COUNT(*) FROM expansions')->fetchColumn();
    $stats['trainings']=(int)$pdo->query('SELECT COUNT(*) FROM training_types')->fetchColumn();
    $stats['building_costs']=(int)$pdo->query('SELECT COUNT(*) FROM station_build_costs')->fetchColumn();
    $projects=$pdo->query('SELECT p.*, COUNT(s.id) station_count FROM projects p LEFT JOIN stations s ON s.project_id=p.id GROUP BY p.id ORDER BY p.created_at DESC')->fetchAll();
    view('dashboard/index', compact('stats','projects')); exit;
}
if ($path === '/projects') { $projects=$pdo->query('SELECT * FROM projects ORDER BY name')->fetchAll(); view('projects/index', compact('projects')); exit; }
if ($path === '/projects/create' && $_SERVER['REQUEST_METHOD']==='POST') { csrf_verify(); $stmt=$pdo->prepare("INSERT INTO projects(name,description,status) VALUES(?,?,?)"); $stmt->execute([$_POST['name'], $_POST['description']??null, 'active']); $pid=$pdo->lastInsertId(); $stmt=$pdo->prepare('INSERT INTO dispatch_centers(project_id,name) VALUES(?,?)'); $stmt->execute([$pid, $_POST['dispatch_center'] ?: 'Leitstelle']); redirect('/projects'); }

if ($path === '/system') {
    $systemCheck = new \Wachplaner\Services\System\SystemCheckService(WACHPLANER_ROOT);
    $checks = $systemCheck->checks($pdo);
    $counts = [
        'Fahrzeugtypen' => (int)$pdo->query('SELECT COUNT(*) FROM vehicle_types')->fetchColumn(),
        'Ausbildungen' => (int)$pdo->query('SELECT COUNT(*) FROM training_types')->fetchColumn(),
        'Erweiterungen' => (int)$pdo->query('SELECT COUNT(*) FROM expansions')->fetchColumn(),
        'Baukosten' => (int)$pdo->query('SELECT COUNT(*) FROM station_build_costs')->fetchColumn(),
        'Projekte' => (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
    ];
    view('system/index', compact('checks', 'counts'));
    exit;
}


if ($path === '/admin/masterdata') {
    $manager = new \Wachplaner\Services\Masterdata\MasterdataManager($pdo);
    $result = null;
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $type = $_POST['import_type'] ?? '';
        if (!isset($_FILES['masterdata_file']) || ($_FILES['masterdata_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $error = 'Bitte eine gültige XLSX-Datei hochladen.';
        } else {
            $fileName = $_FILES['masterdata_file']['name'] ?? 'upload.xlsx';
            $tmp = $_FILES['masterdata_file']['tmp_name'];
            $result = $manager->import($type, $tmp, $fileName);
        }
    }

    $counts = $manager->counts();
    $logs = $manager->latestLogs();
    $importers = $manager->importers();
    view('admin/masterdata/index', compact('counts', 'logs', 'importers', 'result', 'error'));
    exit;
}

if ($path === '/masterdata') { $vehicleTypes=$pdo->query('SELECT * FROM vehicle_types ORDER BY category,name LIMIT 250')->fetchAll(); $expansions=$pdo->query('SELECT * FROM expansions ORDER BY station_type,name')->fetchAll(); $trainings=$pdo->query('SELECT * FROM training_types ORDER BY organisation,name')->fetchAll(); view('masterdata/index', compact('vehicleTypes','expansions','trainings')); exit; }
http_response_code(404); echo '404';
