<?php
// administrador/api/users/update_user.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorUsuarios.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $clave = $_POST['clave'] ?? null;
    $tipo_usuario = $_POST['tipo_usuario'] ?? null;

    if (empty($id) || empty($nombre) || empty($tipo_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para actualizar el usuario.']);
        exit;
    }

    $gestorUsuarios = new GestorUsuarios($pdo); // Nombre de clase actualizado
    $response = $gestorUsuarios->actualizarUsuario($id, $nombre, $clave, $tipo_usuario); // Nombre de método actualizado

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>