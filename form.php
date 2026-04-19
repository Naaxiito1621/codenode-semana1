<?php


$nombre = $_POST['name'];
$email = $_POST['email'];
$Num_telefono = $_POST['telefono'];
$Apellidos = $_POST['Apellidos'];
$Dni = $_POST['Dni'];

$nombre = "Este mensaje fue enviado por: " . $nombre;
$Apellidos = "Sus Apellidos: " . $Apellidos;
$email = "Su correo es: " . $email;
$Num_telefono = "Numero de telefono: " . $Num_telefono;
$Dni = "Dni: " . $Dni;

header('Location: respuesta.html');

?>