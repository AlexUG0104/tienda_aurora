<?php
// admin/obtener_productos.php
require_once '../db.php'; // Asegúrate de que la ruta sea correcta

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM obtener_productos()";
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($productos);

} catch (PDOException $e) {
    error_log("Error al obtener productos: " . $e->getMessage());
    echo json_encode(['error' => 'Error al cargar productos. Intente de nuevo más tarde.']);
}
?>