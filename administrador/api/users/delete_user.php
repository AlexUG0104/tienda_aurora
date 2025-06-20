<?php
// api/users/delete_user.php

// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
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

    $gestorUsuarios = new GestorUsuarios($pdo);

    $userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($userId > 0) {
        $result = $gestorUsuarios->eliminarUsuario($userId);
        $response = $result; 
    } else {
        $response['message'] = 'ID de usuario no proporcionado.';
    }

} catch (Exception $e) {
    error_log("Error en delete_user.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
    $response = ['success' => false, 'message' => 'Error interno del servidor al eliminar usuario: ' . $e->getMessage()];
}

echo json_encode($response);
?>