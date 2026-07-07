<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect($path){ header('Location: '.$path); exit; }
function csrf_token(){ if(empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function csrf_field(){ return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">'; }
function csrf_verify(){ if($_SERVER['REQUEST_METHOD']==='POST' && (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']))) { http_response_code(419); die('Ungültiger CSRF-Token.'); } }
function auth_user(){ return $_SESSION['user'] ?? null; }
function require_auth(){ if(!auth_user()) redirect('/login'); }
function view($view, $data=[]){ extract($data); require __DIR__.'/../Views/layout/header.php'; require __DIR__.'/../Views/'.$view.'.php'; require __DIR__.'/../Views/layout/footer.php'; }
function money_fmt($n){ return number_format((float)$n,0,',','.').' Credits'; }
