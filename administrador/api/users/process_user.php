<?php
// administrador/api/users/process_user.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../db.php'; 
require_once '../../classes/GestorUsuarios.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("La conexión a la base de datos (PDO) no está disponible.");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de solicitud no permitido. Se espera POST.");
    }

    $action = $_POST['action'] ?? '';

    $gestorUsuarios = new GestorUsuarios($pdo);

    if ($action === 'register') {
        $nombre = $_POST['nombre'] ?? null;
        $clave = $_POST['clave'] ?? null;
        $tipoUsuarioId = $_POST['tipo_usuario_select'] ?? null; 

        // Validaciones aquí
        if (empty($nombre) || empty($clave) || !is_numeric($tipoUsuarioId)) {
            throw new Exception("Faltan datos obligatorios para el registro o son inválidos.");
        }

        $response = $gestorUsuarios->registrarUsuario($nombre, $clave, (int)$tipoUsuarioId);

    } elseif ($action === 'update') {
        $id = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $clave = $_POST['clave'] ?? null; 
        $tipoUsuarioId = $_POST['tipo_usuario_select'] ?? null; 

        if (empty($id) || !is_numeric($id) || empty($nombre) || !is_numeric($tipoUsuarioId)) {
            throw new Exception("Faltan datos obligatorios para la actualización o son inválidos.");
        }

        $response = $gestorUsuarios->actualizarUsuario((int)$id, $nombre, $clave, (int)$tipoUsuarioId);

    } else {
        throw new Exception("Acción no reconocida.");
    }

} catch (Exception $e) {
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
    error_log("Error en process_user.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
}

echo json_encode($response);
?>