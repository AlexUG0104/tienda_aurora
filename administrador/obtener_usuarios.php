<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once '../db.php'; // Ajusta la ruta si es necesario

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM obtener_usuarios_existentes();";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);

} catch (PDOException $e) {
    http_response_code(500);
    
    echo json_encode(['error' => 'Error al obtener usuarios desde la función de DB: ' . $e->getMessage()]);
    error_log("Error en obtener_usuarios.php (función DB): " . $e->getMessage());
}
?>