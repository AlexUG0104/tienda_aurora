<?php
// administrador/api/products/get_product_by_id.php
require_once '../../../config_sesion.php';
require_once '../../../db.php';           
require_once '../../classes/GestorProductos.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$productId = $_GET['id'] ?? null;

if (empty($productId) || !is_numeric($productId)) {
    echo json_encode(['error' => 'ID de producto inválido.']);
    exit();
}

try {
    $gestorProductos = new GestorProductos($pdo);
    $productData = $gestorProductos->obtenerProductoPorId((int)$productId);

    if ($productData) {
        echo json_encode($productData);
    } else {
        echo json_encode(['error' => 'Producto no encontrado.']);
    }

} catch (Exception $e) {
    error_log("Error en get_product_by_id.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al obtener los datos del producto.']);
}
?>
