<?php
// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Desde TIENDA_AURORA/administrador/api/users/
// Para db.php que está en TIENDA_AURORA/
require_once '../../../db.php';

require_once '../../classes/GestorUsuarios.php';

header('Content-Type: application/json');

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
    $usuario = $gestorUsuarios->obtenerUsuarioPorId($userId); 

    if ($usuario) {

        if (isset($usuario['contrasena'])) { 
            unset($usuario['contrasena']);
        }
        if (isset($usuario['clave'])) { 
            unset($usuario['clave']);
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