<?php

require_once '../db.php'; // Ajusta la ruta si es necesario

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit;
}

$id = $_POST['id'] ?? null; // ID del producto a eliminar

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado.']);
    exit;
}

try {
    $sql = "SELECT eliminar_producto(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    
    echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente.']);

} catch (PDOException $e) {
    error_log("Error al eliminar producto: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al eliminar producto. Intente de nuevo más tarde.', 'error_detail' => $e->getMessage()]);
}
?>