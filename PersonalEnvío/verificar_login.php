<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");

$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT c.id, c.nombre, c.contrasena, c.id_tipo_usuario
        FROM credencial c
        WHERE c.nombre = :usuario AND c.contrasena = :contrasena";

$stmt = $conn->prepare($sql);
$stmt->execute(['usuario' => $usuario, 'contrasena' => $contrasena]);
$credencial = $stmt->fetch();

if ($credencial && $credencial['id_tipo_usuario'] == 3) {
    $_SESSION['usuario'] = $credencial['usuario'];
    $_SESSION['id_credencial'] = $credencial['id'];
    $_SESSION['tipo_usuario'] = $credencial['id_tipo_usuario'];
    header("Location: pedidos_por_enviar.php");
} else {
    echo "<p>Usuario inválido o no autorizado. <a href='login.php'>Intentar de nuevo</a></p>";
}
