<?php
/**
 * Conexión a la base de datos empresa_db usando PDO.
 *
 * Se utilizan consultas preparadas nativas (ATTR_EMULATE_PREPARES = false)
 * para prevenir inyecciones SQL.
 */

$host     = 'localhost';
$dbname   = 'empresa_db';
$user     = 'root';
$password = '';  // XAMPP usa contraseña vacía por defecto

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
