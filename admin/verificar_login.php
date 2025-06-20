<?php
// administrador/verificar_login.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Asegúrate de que la sesión se inicia aquí o en config_sesion.php

// Incluir el archivo de conexión a la base de datos
// La ruta es ../db.php porque verificar_login.php está en 'administrador/'
// y db.php está en la raíz de 'TIENDA_AURORA/'
require_once '../db.php'; // Aquí se establece la conexión $pdo

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// Validaciones básicas
if (empty($usuario) || empty($contrasena)) {
    $_SESSION['login_error'] = 'Por favor, ingrese usuario y contraseña.';
    header("Location: login.php");
    exit();
}

$sql = "SELECT id, nombre, contrasena, id_tipo_usuario
        FROM credencial
        WHERE nombre = :usuario";

// Usamos $pdo, que viene de db.php
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$credencial = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array for consistent key access

// Verificación de credenciales
if ($credencial) {
    // Usuario encontrado, ahora verificar la contraseña hasheada y el tipo de usuario
    if ($credencial['id_tipo_usuario'] == 1 && password_verify($contrasena, $credencial['contrasena'])) {
        // Credenciales válidas para un administrador
        $_SESSION['usuario'] = $credencial['nombre'];
        $_SESSION['id_credencial'] = $credencial['id'];
        $_SESSION['tipo_usuario'] = $credencial['id_tipo_usuario'];

        header("Location: menu.php"); // Redirigir al menú del administrador
        exit;
    } else {
        // Contraseña incorrecta o tipo de usuario no autorizado para este login (no es admin)
        $_SESSION['login_error'] = 'Usuario o contraseña incorrectos, o no tiene permisos de administrador.';
        header("Location: login.php");
        exit();
    }
} else {
    // Usuario no encontrado en la base de datos
    $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
    header("Location: login.php");
    exit();
}
