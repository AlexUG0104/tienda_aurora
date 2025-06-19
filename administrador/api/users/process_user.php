<?php
// api/users/process_user.php
require_once '../../../db.php';
require_once '../../administrador/classes/GestorUsuarios.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $gestorUsuarios = new GestorUsuarios($pdo);

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0; // Para identificar si es edición
    $nombre = $_POST['nombre'] ?? '';
    $clave = $_POST['clave'] ?? ''; // La clave solo se usa para registrar o si se actualiza
    $id_tipo_usuario = (int)($_POST['tipo_usuario_select'] ?? 0);

    if ($id > 0) {
        // Lógica para actualizar un usuario existente
        // Es crucial que 'actualizar_usuario' en GestorUsuarios reciba la clave como opcional (null)
        // si no se envía una nueva clave desde el formulario.
        $result = $gestorUsuarios->actualizarUsuario($id, $nombre, empty($clave) ? null : $clave, $id_tipo_usuario);
        $response = $result; // Ya viene con 'success' y 'message'
    } else {
        // Lógica para registrar un nuevo usuario
        $result = $gestorUsuarios->registrarUsuario($nombre, $clave, $id_tipo_usuario);
        $response = $result; // Ya viene con 'success' y 'message' / 'newUserId'
    }

} catch (Exception $e) {
    error_log("Error en process_user.php: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Error interno del servidor al procesar el usuario.'];
}

echo json_encode($response);
?>