<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

session_start();

if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

unset($_SESSION['csrf_token']);

if (!isset($_POST['name'], $_POST['email'], $_POST['telefono'], $_POST['Apellidos'], $_POST['Dni'])) {
    http_response_code(400);
    exit('Missing required fields');
}

$nombre       = htmlspecialchars(trim($_POST['name']),       ENT_QUOTES, 'UTF-8');
$Apellidos    = htmlspecialchars(trim($_POST['Apellidos']),  ENT_QUOTES, 'UTF-8');
$Num_telefono = htmlspecialchars(trim($_POST['telefono']),   ENT_QUOTES, 'UTF-8');
$Dni          = htmlspecialchars(trim($_POST['Dni']),         ENT_QUOTES, 'UTF-8');

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if ($email === false) {
    http_response_code(400);
    exit('Invalid email address');
}
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

if (!preg_match('/^\d{6,15}$/', $Num_telefono)) {
    http_response_code(400);
    exit('Invalid phone number');
}

if (!preg_match('/^\d{8}[A-Za-z]$/', $Dni)) {
    http_response_code(400);
    exit('Invalid DNI format');
}

header('Location: respuesta.html');
exit;

?>
