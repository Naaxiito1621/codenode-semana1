<?php
// ============================================================
// Conexión a la base de datos empresa_db (MySQLi)
// ============================================================
// Este archivo se incluye en todas las páginas que necesitan
// acceder a la base de datos mediante: require_once 'conexion.php';
//
// Usa MySQLi (MySQL Improved) en modo orientado a objetos.
// ============================================================

// --- Datos de conexión ---
// En XAMPP, el servidor MySQL corre en localhost (la misma máquina)
// El usuario por defecto es "root" sin contraseña
$servidor   = "localhost";
$usuario    = "root";
$contrasena = "";              // XAMPP usa contraseña vacía por defecto
$base_datos = "empresa_db";

// --- Crear la conexión ---
// new mysqli() intenta conectarse al servidor MySQL con los datos proporcionados.
// Si falla, el objeto $conexion contendrá información del error en ->connect_error
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// --- Verificar la conexión ---
// Si connect_error no es null, significa que hubo un problema
// (por ejemplo: MySQL no está corriendo, la BD no existe, credenciales incorrectas)
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// --- Establecer charset UTF-8 ---
// Esto asegura que los caracteres especiales (ñ, á, é, ü, etc.)
// se transmitan correctamente entre PHP y MySQL.
// Sin esto, los acentos y la ñ podrían verse como símbolos extraños.
$conexion->set_charset("utf8");
?>
