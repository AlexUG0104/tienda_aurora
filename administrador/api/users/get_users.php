<?php
// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Asegurarse de que la cabecera sea JSON


require_once '../../../db.php'; 
require_once '../../classes/GestorUsuarios.php'; 

try {
    // Verificar si $pdo está disponible (de db.php)
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("La conexión a la base de datos (PDO) no está disponible.");
    }

    $gestorUsuarios = new GestorUsuarios($pdo);
    $usuarios = $gestorUsuarios->obtenerUsuarios();

    echo json_encode($usuarios);

} catch (Exception $e) {
    // Capturar cualquier excepción y devolverla como JSON
    echo json_encode(['error' => $e->getMessage()]);
    // Opcional: registrar el error más detalladamente en un log del servidor
    error_log("Error en get_users.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
}
?>