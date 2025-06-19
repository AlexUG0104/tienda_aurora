<?php
// administrador/api/orders/update_order_status.php
require_once '../../../config_sesion.php'; // Subir tres niveles
require_once '../../../db.php';          // Subir tres niveles
require_once '../../classes/GestorPedidos.php'; // Subir dos niveles, luego entrar a classes

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'] ?? null;
    $new_status_id = $_POST['new_status_id'] ?? null;

    if (empty($id_pedido) || empty($new_status_id)) {
        echo json_encode(['success' => false, 'message' => 'Faltan ID de pedido o nuevo estado.']);
        exit;
    }

    $gestorPedidos = new GestorPedidos($pdo); // Nombre de clase actualizado
    $response = $gestorPedidos->actualizarEstadoPedido($id_pedido, $new_status_id); // Nombre de método actualizado

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>