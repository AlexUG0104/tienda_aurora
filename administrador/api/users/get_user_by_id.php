<?php
// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Asumiendo que 'api/users/' está dos niveles por debajo de la raíz del proyecto
require_once '--/../../db.php';
require_once '../../classes/GestorUsuarios.php';

$response = ['error' => ''];

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("La conexión a la base de datos (PDO) no está disponible.");
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("ID de usuario no proporcionado o inválido.");
    }

    $userId = (int)$_GET['id'];
    $gestorUsuarios = new GestorUsuarios($pdo);
    $usuario = $gestorUsuarios->obtenerUsuarioPorId($userId); // Necesitamos esta nueva función

    if ($usuario) {
        // Asegurarse de no enviar la clave hashed
        unset($usuario['clave']); // O el nombre de tu columna de clave

        // Devolver id_tipo_usuario para rellenar el select
        // Asegúrate de que tu SP o método `obtenerUsuarioPorId` devuelva este campo
        if (!isset($usuario['id_tipo_usuario'])) {
             // Si tu SP solo devuelve el nombre_tipo_usuario, podrías necesitar otra consulta
             // o ajustar el SP para que también devuelva el ID del tipo de usuario.
             // Por ahora, si no existe, el select no se seleccionará automáticamente.
        }

        echo json_encode($usuario);
    } else {
        $response['error'] = 'Usuario no encontrado.';
        echo json_encode($response);
    }

} catch (Exception $e) {
    $response['error'] = 'Error al obtener usuario: ' . $e->getMessage();
    error_log("Error en get_user_by_id.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
    echo json_encode($response);
}
?>