<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); 


require_once '../../../db.php'; 
require_once '../../classes/GestorUsuarios.php'; 

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception("La conexión a la base de datos (PDO) no está disponible.");
    }

    $gestorUsuarios = new GestorUsuarios($pdo);
    $usuarios = $gestorUsuarios->obtenerUsuarios();

    echo json_encode($usuarios);

} catch (Exception $e) {
 
    echo json_encode(['error' => $e->getMessage()]);
    error_log("Error en get_users.php: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
}
?>