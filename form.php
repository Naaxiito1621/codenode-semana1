<?php

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: Este formulario solo acepta solicitudes POST.";
    exit;
}

// Validate that all required fields are present and non-empty
$requiredFields = ['name', 'email', 'telefono', 'Apellidos', 'Dni'];
$errors = [];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $errors[] = "El campo '$field' es obligatorio.";
    }
}

// Validate email format
if (isset($_POST['email']) && trim($_POST['email']) !== '') {
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no tiene un formato válido.";
    }
}

// Validate phone number (only digits, 9 characters for Spanish numbers)
if (isset($_POST['telefono']) && trim($_POST['telefono']) !== '') {
    if (!preg_match('/^\d{9}$/', trim($_POST['telefono']))) {
        $errors[] = "El número de teléfono debe contener exactamente 9 dígitos.";
    }
}

// Validate DNI format (8 digits + 1 letter)
if (isset($_POST['Dni']) && trim($_POST['Dni']) !== '') {
    if (!preg_match('/^\d{8}[A-Za-z]$/', trim($_POST['Dni']))) {
        $errors[] = "El DNI debe tener 8 dígitos seguidos de una letra (ej: 12345678K).";
    }
}

// If there are validation errors, redirect back with error info
if (!empty($errors)) {
    $errorQuery = http_build_query(['errors' => $errors]);
    header('Location: index.html?' . $errorQuery);
    exit;
}

// Sanitize inputs
$nombre = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
$Num_telefono = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
$Apellidos = htmlspecialchars(trim($_POST['Apellidos']), ENT_QUOTES, 'UTF-8');
$Dni = htmlspecialchars(trim($_POST['Dni']), ENT_QUOTES, 'UTF-8');

$nombre = "Este mensaje fue enviado por: " . $nombre;
$Apellidos = "Sus Apellidos: " . $Apellidos;
$email = "Su correo es: " . $email;
$Num_telefono = "Numero de telefono: " . $Num_telefono;
$Dni = "Dni: " . $Dni;

header('Location: respuesta.html');
exit;

?>
