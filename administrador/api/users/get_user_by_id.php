<?php
// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Desde TIENDA_AURORA/administrador/api/users/
// Para db.php que está en TIENDA_AURORA/
require_once '../../../db.php';

// Para GestorUsuarios.php que está en TIENDA_AURORA/administrador/classes/
require_once '../../classes/GestorUsuarios.php';

header('Content-Type: application/json');

$response = ['error' => ''];

try {
    // Verificar si $pdo está disponible (de db.php)
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("La conexión a la base de datos (PDO) no está disponible.");
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("ID de usuario no proporcionado o inválido.");
    }

    $userId = (int)$_GET['id'];
    $gestorUsuarios = new GestorUsuarios($pdo);
    $usuario = $gestorUsuarios->obtenerUsuarioPorId($userId); // Asegúrate de que este método exista y funcione

    if ($usuario) {
        // MUY IMPORTANTE: No envíes la clave hasheada al frontend por seguridad.
        // Si tu SP o query devuelve una columna llamada 'contrasena' o 'clave', quítala.
        if (isset($usuario['contrasena'])) { // O 'clave' si ese es el nombre de tu columna
            unset($usuario['contrasena']);
        }
        if (isset($usuario['clave'])) { // Si usas 'clave' en tu SP
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
    echo json_encode($response); // Asegúrate de que el JSON de error también sea válido
}
?>