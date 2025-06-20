<?php
// administrador/api/users/update_user.php
require_once '../../../config_sesion.php'; 
require_once '../../../db.php';       
require_once '../../classes/GestorUsuarios.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $clave = $_POST['clave'] ?? null;
    $tipo_usuario = $_POST['tipo_usuario'] ?? null; 

    if (empty($id) || !is_numeric($id) || empty($nombre) || empty($tipo_usuario) || !is_numeric($tipo_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios o son inválidos para actualizar el usuario.']);
        exit;
    }

    if (!isset($pdo) || !$pdo instanceof PDO) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        exit;
    }

    $gestorUsuarios = new GestorUsuarios($pdo);
    $response = $gestorUsuarios->actualizarUsuario((int)$id, $nombre, $clave, (int)$tipo_usuario);

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>