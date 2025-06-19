<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// admin/procesar_usuario.php
require_once '../db.php';

header('Content-Type: application/json'); // Indicar que la respuesta es JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario_select'] ?? '';

    // Validación básica de campos
    if (empty($nombre) || empty($clave) || empty($tipo_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit();
    }

    // Hash de la clave antes de enviarla a la base de datos (¡CRUCIAL POR SEGURIDAD!)
    $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);

    try {
        // Llama a la función de PostgreSQL para insertar el usuario
        $sql = "SELECT insertar_usuario(:nombre, :clave, :tipo_usuario) AS new_user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':clave' => $clave_hasheada,
            ':tipo_usuario' => (int)$tipo_usuario
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $newUserId = $result['new_user_id'];

        echo json_encode(['success' => true, 'message' => 'Usuario insertado con éxito.', 'newUserId' => $newUserId]);

    } catch (PDOException $e) {
        // En un entorno de producción, loggea el error (`error_log($e->getMessage());`)
        // y muestra un mensaje genérico al usuario por seguridad.
        echo json_encode(['success' => false, 'message' => 'Error de base de datos al registrar usuario: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>