<?php
// administrador/api/products/update_product.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorProductos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;

if (empty($data['id']) || empty($data['nombre']) || empty($data['precio_unitario']) || !isset($data['stock'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para la actualización del producto.']);
        exit;
    }

    $gestorProductos = new GestorProductos($pdo); // Nombre de clase actualizado
    $response = $gestorProductos->actualizarProducto($data); // Nombre de método actualizado

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>