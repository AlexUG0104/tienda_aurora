<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$sql = "SELECT id, nombre, contrasena, id_tipo_usuario
        FROM credencial
        WHERE nombre = :usuario";

$stmt = $conn->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$credencial = $stmt->fetch(PDO::FETCH_ASSOC);

if ($credencial && $credencial['id_tipo_usuario'] == 1 && $credencial['contrasena'] === $contrasena) {
    $_SESSION['usuario'] = $credencial['nombre'];
    $_SESSION['id_credencial'] = $credencial['id'];
    $_SESSION['tipo_usuario'] = $credencial['id_tipo_usuario'];

    header("Location: menu.php");
    exit;
} else {
    echo "<p>Usuario inválido o no autorizado. <a href='login.php'>Intentar de nuevo</a></p>";
}
