<?php
// administrador/api/orders/get_order_details.php
require_once '../../../config_sesion.php';
require_once '../../../db.php';          
require_once '../../classes/GestorPedidos.php'; 

header('Content-Type: application/json');

if (isset($_GET['id_pedido'])) {
    $id_pedido = (int)$_GET['id_pedido'];

    $gestorPedidos = new GestorPedidos($pdo); 
    $response = $gestorPedidos->obtenerDetallesPedido($id_pedido); 

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'ID de pedido no proporcionado.']);
}
?>