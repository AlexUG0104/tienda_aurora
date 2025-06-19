<?php
// administrador/api/orders/get_order_details.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorPedidos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

if (isset($_GET['id_pedido'])) {
    $id_pedido = (int)$_GET['id_pedido'];

    $gestorPedidos = new GestorPedidos($pdo); // Nombre de clase actualizado
    $response = $gestorPedidos->obtenerDetallesPedido($id_pedido); // Nombre de método actualizado

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'ID de pedido no proporcionado.']);
}
?>