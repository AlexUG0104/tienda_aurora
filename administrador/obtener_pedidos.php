<?php

require_once '../db.php'; 

header('Content-Type: application/json');

try {
    
    $sql = "SELECT * FROM obtener_pedidos_administrador()"; 
    
    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pedidos);

} catch (PDOException $e) {
   
    error_log("Error al obtener pedidos: " . $e->getMessage());
    
    // Devuelve un JSON de error al frontend
    echo json_encode(['error' => 'Error al cargar pedidos. Intente de nuevo más tarde.', 'details' => $e->getMessage()]);
}
?>