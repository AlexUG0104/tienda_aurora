<?php
// administrador/verificar_login.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../db.php'; 

$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';


if (empty($usuario) || empty($contrasena)) {
    $_SESSION['login_error'] = 'Por favor, ingrese usuario y contraseña.';
    header("Location: login.php");
    exit();
}

$sql = "SELECT id, nombre, contrasena, id_tipo_usuario
        FROM credencial
        WHERE nombre = :usuario";

$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$credencial = $stmt->fetch(PDO::FETCH_ASSOC); 

if ($credencial) {

    if ($credencial['id_tipo_usuario'] == 1 && password_verify($contrasena, $credencial['contrasena'])) {

        $_SESSION['usuario'] = $credencial['nombre'];
        $_SESSION['id_credencial'] = $credencial['id'];
        $_SESSION['tipo_usuario'] = $credencial['id_tipo_usuario'];

        header("Location:../administrador/gestionar_usuarios.php"); 
        exit;
    } else {

        $_SESSION['login_error'] = 'Usuario o contraseña incorrectos, o no tiene permisos de administrador.';
        header("Location: login.php");
        exit();
    }
} else {

    $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
    header("Location: login.php");
    exit();
}
