<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $conn = new PDO("pgsql:host=localhost;dbname=aurora", "cesar", "1234");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if (empty($usuario) || empty($contrasena)) {
    $_SESSION['login_error'] = "Por favor, ingrese usuario y contraseña.";
    header("Location: login.php");
    exit;
}

$sql = "SELECT id, nombre, contrasena, id_tipo_usuario 
        FROM credencial 
        WHERE nombre = :usuario AND contrasena = :contrasena";

$stmt = $conn->prepare($sql);
$stmt->execute(['usuario' => $usuario, 'contrasena' => $contrasena]);
$credencial = $stmt->fetch(PDO::FETCH_ASSOC);

if ($credencial && $credencial['id_tipo_usuario'] == 3) {
    $_SESSION['usuario'] = $credencial['nombre'];
    $_SESSION['id_credencial'] = $credencial['id'];
    $_SESSION['tipo_usuario'] = $credencial['id_tipo_usuario'];
    header("Location: pedidos_por_enviar.php");
    exit;
} else {
    $_SESSION['login_error'] = "Usuario inválido o no autorizado.";
    header("Location: login.php");
    exit;
}
