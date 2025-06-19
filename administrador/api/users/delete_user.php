<?php
// api/users/delete_user.php
require_once '../../../db.php';
require_once '../../administrador/classes/GestorUsuarios.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $gestorUsuarios = new GestorUsuarios($pdo);

    $userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($userId > 0) {
        $result = $gestorUsuarios->eliminarUsuario($userId);
        $response = $result; // Ya viene con 'success' y 'message'
    } else {
        $response['message'] = 'ID de usuario no proporcionado.';
    }

} catch (Exception $e) {
    error_log("Error en delete_user.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Error interno del servidor al eliminar usuario.'];
}

echo json_encode($response);
?>