<?php
// ============================================================
// Conexion a la base de datos empresa_db (MySQLi)
// ============================================================

$servidor = "localhost";
$usuario   = "root";
$contrasena = "";          // XAMPP usa contrasena vacia por defecto
$base_datos = "empresa_db";

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar la conexion
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// Establecer charset UTF-8 para evitar problemas con acentos
$conexion->set_charset("utf8");
?>
