<?php
// administrador/api/products/delete_product.php
require_once '../../../config_sesion.php';
require_once '../../../db.php';
require_once '../../classes/GestorProductos.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado.']);
        exit;
    }

    $gestorProductos = new GestorProductos($pdo);
    $response = $gestorProductos->eliminarProducto($id);

    if ($response) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el producto.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
