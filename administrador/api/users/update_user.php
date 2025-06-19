<?php
// administrador/api/users/update_user.php
require_once '../../../config_sesion.php'; // Subir tres niveles (TIENDA_AURORA/)
require_once '../../../db.php';          // Subir tres niveles (TIENDA_AURORA/)
require_once '../../classes/GestorUsuarios.php'; // Subir dos niveles (administrador/), luego entrar a classes

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $clave = $_POST['clave'] ?? null;
    $tipo_usuario = $_POST['tipo_usuario'] ?? null; // Esto debería ser id_tipo_usuario

    // Validación más robusta
    if (empty($id) || !is_numeric($id) || empty($nombre) || empty($tipo_usuario) || !is_numeric($tipo_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios o son inválidos para actualizar el usuario.']);
        exit;
    }

    // Asegúrate de que $pdo esté disponible globalmente desde db.php
    if (!isset($pdo) || !$pdo instanceof PDO) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        exit;
    }

    $gestorUsuarios = new GestorUsuarios($pdo);
    // Llama a actualizarUsuario directamente, NO a procesarUsuario
    $response = $gestorUsuarios->actualizarUsuario((int)$id, $nombre, $clave, (int)$tipo_usuario);

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>