<?php
// administrador/api/products/delete_product.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorProductos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado.']);
        exit;
    }

    $gestorProductos = new GestorProductos($pdo); // Nombre de clase actualizado
    $response = $gestorProductos->eliminarProducto($id); // Nombre de método actualizado

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>