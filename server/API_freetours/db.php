<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "free_tours";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

/*$host = "sql311.infinityfree.com";  // Reemplaza con tu host real
$usuario = "if0_38336530";  // Reemplaza con tu usuario
$password = "Nso5Eq0mBYHMzPl";  // Reemplaza con tu contraseña
$base_datos = "if0_38336530_freetours";  // Reemplaza con tu base de datos

$conexion = new mysqli($host, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

echo "Conexión exitosa"*/
?>


